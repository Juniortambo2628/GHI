import { useState, useRef, useCallback, useEffect } from 'react';
import { usePage } from '@inertiajs/react';

function formatSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

function GalleryItem({ item, onRemove, onRetry, onMove, index, total }) {
    return (
        <div className="gallery-item-card">
            {item.type === 'video' ? (
                <video src={item.preview} muted loop playsInline />
            ) : (
                <img src={item.preview} alt="" />
            )}
            {item.type === 'video' && <div className="gallery-item-video-badge"><i className="bi bi-play-circle"></i></div>}
            {item.status === 'uploading' && (
                <div className="gallery-progress-bar">
                    <div className="gallery-progress-fill" style={{ width: item.progress + '%' }}></div>
                </div>
            )}
            {item.status === 'error' && (
                <div className="gallery-item-error">
                    <i className="bi bi-exclamation-triangle"></i>
                    <span>{item.error}</span>
                    <button onClick={() => onRetry(item.id)}>Retry</button>
                </div>
            )}
            {item.status !== 'uploading' && item.status !== 'error' && (
                <div className="gallery-item-overlay">
                    <button type="button" className="btn btn-sm btn-outline-light" onClick={() => onMove(item.id, -1)} disabled={index === 0} title="Move left">
                        <i className="bi bi-arrow-left"></i>
                    </button>
                    <button type="button" className="btn btn-sm btn-danger" onClick={() => onRemove(item.id)} title="Remove">
                        <i className="bi bi-trash"></i>
                    </button>
                    <button type="button" className="btn btn-sm btn-outline-light" onClick={() => onMove(item.id, 1)} disabled={index === total - 1} title="Move right">
                        <i className="bi bi-arrow-right"></i>
                    </button>
                </div>
            )}
            <div className="gallery-item-counter">{index + 1}/{total}</div>
        </div>
    );
}

const videoTypes = ['mp4', 'webm', 'mov', 'avi'];

function getFileType(file) {
    if (file.type?.startsWith('video/')) return 'video';
    const ext = file.name?.split('.').pop()?.toLowerCase();
    if (videoTypes.includes(ext)) return 'video';
    return 'image';
}

export default function GalleryUpload({ eventId, images = [], onImagesChange }) {
    const { csrf_token: csrfToken } = usePage().props;
    const fileInputRef = useRef(null);
    const [items, setItems] = useState(() =>
        images.map(img => ({
            id: img.id || 'existing-' + img.path,
            preview: img.path.startsWith('http') ? img.path : '/' + img.path,
            status: 'done',
            path: img.path,
            type: img.type || 'image',
            progress: 100,
            sort_order: img.sort_order ?? 0,
            file: null,
        }))
    );
    const [dragOver, setDragOver] = useState(false);
    const uploadQueueRef = useRef([]);
    const processingRef = useRef(false);

    useEffect(() => {
        onImagesChange(items.filter(i => i.status === 'done').map((i, idx) => ({
            id: i.id,
            path: i.path,
            type: i.type || 'image',
            sort_order: idx,
        })));
    }, [items]);

    const MAX_FILE_SIZE = 20 * 1024 * 1024;

    const uploadFile = useCallback(async (item) => {
        const formData = new FormData();
        formData.append('file', item.file);

        try {
            const xhr = new XMLHttpRequest();
            return new Promise((resolve, reject) => {
                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const pct = Math.round((e.loaded / e.total) * 100);
                        setItems(prev => prev.map(i => i.id === item.id ? { ...i, progress: pct } : i));
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
                    } catch { reject(new Error('Invalid server response')); }
                });
                xhr.addEventListener('error', () => reject(new Error('Network error')));
                xhr.addEventListener('abort', () => reject(new Error('Cancelled')));
                xhr.open('POST', '/api/upload/media');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || '');
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.send(formData);
            });
        } catch (err) {
            throw err;
        }
    }, [csrfToken]);

    const processQueue = useCallback(async () => {
        if (processingRef.current) return;
        processingRef.current = true;

        while (uploadQueueRef.current.length > 0) {
            const item = uploadQueueRef.current.shift();
            setItems(prev => prev.map(i => i.id === item.id ? { ...i, status: 'uploading', progress: 0 } : i));
            try {
                const path = await uploadFile(item);
                setItems(prev => prev.map(i => i.id === item.id ? { ...i, status: 'done', path, progress: 100 } : i));
            } catch (err) {
                setItems(prev => prev.map(i => i.id === item.id ? { ...i, status: 'error', error: err.message, progress: 0 } : i));
            }
        }

        processingRef.current = false;
    }, [uploadFile]);

    const addFiles = useCallback((fileList) => {
        const newItems = Array.from(fileList).map(file => {
            const type = getFileType(file);
            const preview = URL.createObjectURL(file);
            return {
                id: 'new-' + Date.now() + '-' + Math.random().toString(36).slice(2, 8),
                file,
                preview,
                type,
                status: 'queued',
                path: '',
                progress: 0,
                sort_order: 0,
            };
        });

        setItems(prev => {
            const updated = [...prev, ...newItems];
            newItems.forEach(item => uploadQueueRef.current.push(item));
            processQueue();
            return updated;
        });
    }, [processQueue]);

    const handleDrop = useCallback((e) => {
        e.preventDefault();
        setDragOver(false);
        if (e.dataTransfer.files.length > 0) addFiles(e.dataTransfer.files);
    }, [addFiles]);

    const handleDragOver = useCallback((e) => { e.preventDefault(); setDragOver(true); }, []);
    const handleDragLeave = useCallback((e) => { e.preventDefault(); setDragOver(false); }, []);

    const handleRemove = useCallback((id) => {
        setItems(prev => prev.filter(i => i.id !== id));
    }, []);

    const handleRetry = useCallback((id) => {
        setItems(prev => {
            const item = prev.find(i => i.id === id);
            if (!item) return prev;
            const retryItem = { ...item, status: 'queued', progress: 0 };
            uploadQueueRef.current.push(retryItem);
            processQueue();
            return prev.map(i => i.id === id ? retryItem : i);
        });
    }, [processQueue]);

    const handleMove = useCallback((id, direction) => {
        setItems(prev => {
            const idx = prev.findIndex(i => i.id === id);
            if (idx === -1) return prev;
            const newIdx = idx + direction;
            if (newIdx < 0 || newIdx >= prev.length) return prev;
            const next = [...prev];
            [next[idx], next[newIdx]] = [next[newIdx], next[idx]];
            return next;
        });
    }, []);

    const handleReorder = useCallback((id, direction) => {
        handleMove(id, direction);
    }, [handleMove]);

    const totalSize = items.reduce((sum, i) => sum + (i.file?.size || 0), 0);
    const uploading = items.some(i => i.status === 'uploading');
    const errors = items.filter(i => i.status === 'error');
    const done = items.filter(i => i.status === 'done');

    return (
        <div>
            <div
                className={`gallery-upload-zone ${dragOver ? 'dragover' : ''}`}
                onDrop={handleDrop}
                onDragOver={handleDragOver}
                onDragLeave={handleDragLeave}
                onClick={() => fileInputRef.current?.click()}
            >
                <input
                    ref={fileInputRef}
                    type="file"
                    accept="image/png,image/jpeg,image/webp,image/gif,video/mp4,video/webm,video/quicktime"
                    multiple
                    style={{ display: 'none' }}
                    onChange={(e) => { if (e.target.files.length > 0) addFiles(e.target.files); e.target.value = ''; }}
                />
                <div className="gallery-upload-icon"><i className="bi bi-images"></i></div>
                <div className="gallery-upload-text">
                    Drag &amp; drop images or videos here, or <strong>browse</strong>
                </div>
                <div className="gallery-upload-limit">Supports JPG, PNG, WebP, GIF, MP4, WebM, MOV. Max 100MB per file.</div>
            </div>

            {items.length > 0 && (
                <>
                    <div className="gallery-upload-summary">
                        <span><strong>{done.length}</strong> file{done.length !== 1 ? 's' : ''}</span>
                        {uploading && <span><i className="bi bi-arrow-repeat spin me-1"></i>Uploading...</span>}
                        {errors.length > 0 && <span className="text-danger"><i className="bi bi-exclamation-circle me-1"></i>{errors.length} failed</span>}
                        {totalSize > 0 && <span>{formatSize(totalSize)}</span>}
                    </div>
                    <div className="gallery-grid">
                        {items.map((item, idx) => (
                            <GalleryItem
                                key={item.id}
                                item={item}
                                index={idx}
                                total={items.length}
                                onRemove={handleRemove}
                                onRetry={handleRetry}
                                onMove={handleReorder}
                            />
                        ))}
                    </div>
                </>
            )}
        </div>
    );
}
