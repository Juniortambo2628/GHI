import { useState, useRef, useCallback, useEffect } from 'react';
import { useUploads } from '../../Contexts/UploadContext';
import mediaUrl from './mediaUrl';
import MediaPicker from './MediaPicker';

function formatSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

function GalleryItem({ item, onRemove, onMove, index, total }) {
    return (
        <div className="gallery-item-card">
            {item.type === 'video' ? (
                <video src={item.preview} muted loop playsInline />
            ) : (
                <img src={item.preview} alt="" />
            )}
            {item.type === 'video' && <div className="gallery-item-video-badge"><i className="bi bi-play-circle"></i></div>}
            {item.status === 'done' && (
                <div style={{ position: 'absolute', top: '8px', right: '8px', zIndex: 10, background: 'rgba(255, 255, 255, 0.9)', borderRadius: '50%', width: '24px', height: '24px', display: 'flex', alignItems: 'center', justifyContent: 'center', boxShadow: '0 2px 4px rgba(0,0,0,0.2)' }}>
                    <i className="bi bi-check-circle-fill text-success" style={{ fontSize: '16px', lineHeight: 1 }}></i>
                </div>
            )}
            {item.status === 'uploading' && (
                <div style={{ position: 'absolute', inset: 0, background: 'rgba(0,6,86,0.4)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 10 }}>
                    <div className="spinner-border text-light" style={{ width: '1.5rem', height: '1.5rem' }}></div>
                </div>
            )}
            {item.status !== 'uploading' && (
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

export default function GalleryUpload({ eventId, eventTitle, images = [], onImagesChange }) {
    const { startBatch, batches } = useUploads();
    const fileInputRef = useRef(null);
    const [items, setItems] = useState(() =>
        images.map(img => ({
            id: img.id || 'existing-' + img.path,
            preview: img.path.startsWith('http') ? img.path : mediaUrl(img.path),
            status: 'done',
            path: img.path,
            type: img.type || 'image',
            sort_order: img.sort_order ?? 0,
            file: null,
        }))
    );
    const [dragOver, setDragOver] = useState(false);
    const [showMediaPicker, setShowMediaPicker] = useState(false);

    const handleMediaSelect = useCallback((selectedAssets) => {
        if (!selectedAssets || selectedAssets.length === 0) return;
        
        setItems(prev => {
            const currentCount = prev.length;
            const newItems = selectedAssets.map((asset, idx) => ({
                id: 'library-' + asset.id + '-' + Date.now() + idx,
                preview: mediaUrl(asset.path),
                status: 'done',
                path: asset.path,
                type: (asset.mime_type && asset.mime_type.startsWith('video/')) || (asset.original_name && videoTypes.includes(asset.original_name.split('.').pop()?.toLowerCase())) ? 'video' : 'image',
                sort_order: currentCount + idx,
                file: null,
            }));
            
            return [...prev, ...newItems];
        });
    }, []);

    const eventBatches = batches.filter(b => b.eventId === eventId);
    const bgItems = eventBatches.flatMap(b => b.items.filter(i => i.status === 'done' || i.status === 'uploading' || i.status === 'queued'));

    useEffect(() => {
        onImagesChange(items.filter(i => i.status === 'done').map((i, idx) => ({
            id: i.id,
            path: i.path,
            type: i.type || 'image',
            sort_order: idx,
        })));
    }, [items]);

    const addFiles = useCallback((fileList) => {
        const newFiles = Array.from(fileList);
        if (newFiles.length === 0) return;

        const previewItems = newFiles.map(file => ({
            id: 'uploading-' + Date.now() + '-' + Math.random().toString(36).slice(2, 8),
            file,
            preview: URL.createObjectURL(file),
            type: getFileType(file),
            status: 'uploading',
            path: '',
            sort_order: 0,
        }));

        setItems(prev => [...prev, ...previewItems]);

        startBatch(eventId, eventTitle || `Event #${eventId || 'new'}`, newFiles, (completedItems) => {
            setItems(prev => {
                const uploading = prev.filter(i => previewItems.some(p => p.id === i.id));
                const rest = prev.filter(i => !previewItems.some(p => p.id === i.id));
                const completed = completedItems
                    .filter(i => i.status === 'done' && i.path)
                    .map((ci, idx) => ({
                        id: ci.id,
                        preview: mediaUrl(ci.path),
                        status: 'done',
                        path: ci.path,
                        type: ci.type || 'image',
                        sort_order: rest.length + idx,
                        file: null,
                    }));
                return [...rest, ...completed];
            });
        });
    }, [eventId, eventTitle, startBatch]);

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

    const done = items.filter(i => i.status === 'done');
    const uploading = items.filter(i => i.status === 'uploading');
    const totalSize = items.reduce((sum, i) => sum + (i.file?.size || 0), 0);
    const bgActiveCount = eventBatches.reduce((sum, b) =>
        sum + b.items.filter(i => i.status === 'queued' || i.status === 'uploading').length, 0
    );

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
                <div className="gallery-upload-limit">Supports JPG, PNG, WebP, GIF, MP4, WebM, MOV. Videos up to 200MB (chunked upload).</div>
                <div className="mt-3">
                    <button type="button" className="btn btn-sm btn-outline-primary" onClick={(e) => { e.stopPropagation(); setShowMediaPicker(true); }}>
                        <i className="bi bi-images me-1"></i>Select from Media Library
                    </button>
                </div>
            </div>

            {items.length > 0 && (
                <>
                    <div className="gallery-upload-summary">
                        <span><strong>{done.length}</strong> file{done.length !== 1 ? 's' : ''}</span>
                        {uploading.length > 0 && <span><i className="bi bi-arrow-repeat spin me-1"></i>{uploading.length} uploading...</span>}
                        {bgActiveCount > 0 && <span className="text-info"><i className="bi bi-hourglass-split me-1"></i>{bgActiveCount} processing in background</span>}
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
                                onMove={handleMove}
                            />
                        ))}
                    </div>
                </>
            )}

            <MediaPicker 
                show={showMediaPicker} 
                onClose={() => setShowMediaPicker(false)} 
                onSelect={handleMediaSelect} 
                multiSelect={true} 
            />
        </div>
    );
}
