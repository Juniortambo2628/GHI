import { createContext, useContext, useState, useCallback, useRef } from 'react';
import { usePage } from '@inertiajs/react';

const UploadContext = createContext(null);

let batchCounter = 0;

export function UploadProvider({ children }) {
    const { csrf_token: csrfToken } = usePage().props;
    const [batches, setBatches] = useState([]);
    const processingRef = useRef({});

    const updateBatch = useCallback((batchId, updates) => {
        setBatches(prev => prev.map(b => b.id === batchId ? { ...b, ...updates } : b));
    }, []);

    const updateItem = useCallback((batchId, itemId, updates) => {
        setBatches(prev => prev.map(b => {
            if (b.id !== batchId) return b;
            return {
                ...b,
                items: b.items.map(i => i.id === itemId ? { ...i, ...updates } : i),
            };
        }));
    }, []);

    const removeBatch = useCallback((batchId) => {
        setBatches(prev => prev.filter(b => b.id !== batchId));
    }, []);

    const uploadFile = useCallback((batchId, item) => {
        return new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append('file', item.file);

            const xhr = new XMLHttpRequest();
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const pct = Math.round((e.loaded / e.total) * 100);
                    updateItem(batchId, item.id, { progress: pct, status: 'uploading' });
                }
            });
            xhr.addEventListener('load', () => {
                try {
                    const payload = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && payload?.success && payload.path) {
                        resolve(payload.path);
                    } else {
                        reject(new Error(payload.message || 'Upload failed'));
                    }
                } catch {
                    reject(new Error('Invalid server response'));
                }
            });
            xhr.addEventListener('error', () => reject(new Error('Network error')));
            xhr.addEventListener('abort', () => reject(new Error('Cancelled')));
            xhr.open('POST', '/upload/media');
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || '');
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.send(formData);
        });
    }, [csrfToken, updateItem]);

    const processQueue = useCallback(async (batchId) => {
        if (processingRef.current[batchId]) return;
        processingRef.current[batchId] = true;

        let batch;
        const getBatch = () => {
            setBatches(prev => {
                batch = prev.find(b => b.id === batchId);
                return prev;
            });
        };

        getBatch();

        while (batch && batch.queue.length > 0) {
            const itemId = batch.queue[0];
            const item = batch.items.find(i => i.id === itemId);

            if (!item || item.status === 'done' || item.status === 'error') {
                setBatches(prev => prev.map(b => {
                    if (b.id !== batchId) return b;
                    return { ...b, queue: b.queue.slice(1) };
                }));
                getBatch();
                continue;
            }

            updateItem(batchId, itemId, { status: 'uploading', progress: 0 });
            setBatches(prev => prev.map(b => {
                if (b.id !== batchId) return b;
                return { ...b, queue: b.queue.slice(1) };
            }));

            try {
                const path = await uploadFile(batchId, item);
                updateItem(batchId, itemId, { status: 'done', path, progress: 100 });
            } catch (err) {
                updateItem(batchId, itemId, { status: 'error', error: err.message, progress: 0 });
            }

            getBatch();
        }

        processingRef.current[batchId] = false;

        setBatches(prev => {
            const b = prev.find(x => x.id === batchId);
            if (b && b.queue.length === 0 && !b.items.some(i => i.status === 'uploading')) {
                return prev;
            }
            return prev;
        });
    }, [uploadFile, updateItem]);

    const startBatch = useCallback((eventId, eventTitle, files, onComplete) => {
        const batchId = `batch-${++batchCounter}`;
        const items = Array.from(files).map((file, idx) => ({
            id: `${batchId}-item-${idx}`,
            file,
            name: file.name,
            size: file.size,
            type: file.type?.startsWith('video/') ? 'video' : 'image',
            status: 'queued',
            progress: 0,
            path: null,
            error: null,
        }));

        setBatches(prev => [...prev, {
            id: batchId,
            eventId,
            eventTitle,
            items,
            queue: items.map(i => i.id),
            onComplete,
            createdAt: Date.now(),
        }]);

        setTimeout(() => processQueue(batchId), 50);
        return batchId;
    }, [processQueue]);

    const retryItem = useCallback((batchId, itemId) => {
        setBatches(prev => prev.map(b => {
            if (b.id !== batchId) return b;
            const item = b.items.find(i => i.id === itemId);
            if (!item) return b;
            return {
                ...b,
                items: b.items.map(i => i.id === itemId ? { ...i, status: 'queued', progress: 0, error: null } : i),
                queue: [...b.queue, itemId],
            };
        }));
        setTimeout(() => processQueue(batchId), 50);
    }, [processQueue]);

    const cancelBatch = useCallback((batchId) => {
        setBatches(prev => prev.map(b => {
            if (b.id !== batchId) return b;
            return {
                ...b,
                queue: [],
                items: b.items.map(i => i.status === 'queued' || i.status === 'uploading'
                    ? { ...i, status: 'cancelled', error: 'Cancelled' }
                    : i
                ),
            };
        }));
    }, []);

    const dismissBatch = useCallback((batchId) => {
        removeBatch(batchId);
        delete processingRef.current[batchId];
    }, [removeBatch]);

    const activeCount = batches.reduce((sum, b) =>
        sum + b.items.filter(i => i.status === 'queued' || i.status === 'uploading').length, 0
    );

    const totalDone = batches.reduce((sum, b) =>
        sum + b.items.filter(i => i.status === 'done').length, 0
    );

    const totalItems = batches.reduce((sum, b) => sum + b.items.length, 0);

    return (
        <UploadContext.Provider value={{
            batches,
            startBatch,
            retryItem,
            cancelBatch,
            dismissBatch,
            activeCount,
            totalDone,
            totalItems,
        }}>
            {children}
        </UploadContext.Provider>
    );
}

export function useUploads() {
    const ctx = useContext(UploadContext);
    if (!ctx) throw new Error('useUploads must be used within UploadProvider');
    return ctx;
}
