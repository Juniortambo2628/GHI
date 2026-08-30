import { useCallback, useEffect, useRef, useState } from 'react';
import { usePage } from '@inertiajs/react';
import * as FilePond from 'filepond';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginImageResize from 'filepond-plugin-image-resize';
import Cropper from 'react-easy-crop';
import imageCompression from 'browser-image-compression';
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';
import mediaUrl from './mediaUrl';
import MediaPicker from './MediaPicker';

FilePond.registerPlugin(FilePondPluginFileValidateType, FilePondPluginImagePreview, FilePondPluginImageResize);

function getCroppedImg(imageSrc, pixelCrop) {
    const image = new Image();
    image.src = imageSrc;
    return new Promise((resolve) => {
        image.onload = () => {
            const canvas = document.createElement('canvas');
            canvas.width = pixelCrop.width;
            canvas.height = pixelCrop.height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(image, pixelCrop.x, pixelCrop.y, pixelCrop.width, pixelCrop.height, 0, 0, pixelCrop.width, pixelCrop.height);
            canvas.toBlob((blob) => resolve(blob), 'image/jpeg', 0.9);
        };
    });
}

export default function ImageUploadField({ name, value, onChange, label = 'Image', required = false, heroPreview = false }) {
    const containerRef = useRef(null);
    const pond = useRef(null);
    const destroyedRef = useRef(false);
    const { csrf_token: csrfToken } = usePage().props;
    const [previewUrl, setPreviewUrl] = useState(value ? mediaUrl(value) : null);
    const [mode, setMode] = useState('preview');
    const [cropData, setCropData] = useState({ x: 0, y: 0 });
    const [zoom, setZoom] = useState(1);
    const [aspect, setAspect] = useState(null);
    const [pickerOpen, setPickerOpen] = useState(false);

    useEffect(() => {
        setPreviewUrl(value ? mediaUrl(value) : null);
    }, [value]);

    useEffect(() => {
        if (!containerRef.current || pond.current) return undefined;
        destroyedRef.current = false;

        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.name = 'file';
        containerRef.current.appendChild(fileInput);

        pond.current = FilePond.create(fileInput, {
            name: 'file',
            acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp', 'image/gif'],
            allowImageResize: true,
            imageResizeTargetWidth: 1800,
            imageResizeTargetHeight: 1200,
            imageResizeMode: 'contain',
            imagePreviewHeight: 200,
            stylePanelLayout: 'compact',
            stylePanelAspectRatio: null,
            styleItemPanelMaxHeight: '200px',
            server: {
                process: {
                    url: '/upload/image',
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    onload: response => {
                        const payload = typeof response === 'string' ? JSON.parse(response) : response;
                        if (!payload?.success || !payload.path) throw new Error('Upload response did not contain a valid media path.');
                        onChange(payload.path);
                        setPreviewUrl(mediaUrl(payload.path));
                        return response;
                    },
                },
            },
            allowRevert: false,
            onaddfilestart: item => item.file instanceof File && imageCompression(item.file, { maxSizeMB: 18, maxWidthOrHeight: 1800, useWebWorker: true }).then(compressed => item.setMetadata('compressedSize', compressed.size)),
        });

        return () => {
            destroyedRef.current = true;
            try {
                pond.current?.destroy();
            } catch (e) {
                // suppress DOM errors during unmount
            }
            pond.current = null;
        };
    }, []);

    const removeImage = () => {
        if (pond.current) {
            try { pond.current.removeFiles(); } catch (e) { /* ignore */ }
        }
        onChange('');
        setPreviewUrl(null);
        setMode('preview');
    };

    const downloadImage = useCallback(() => {
        if (!previewUrl) return;
        const link = document.createElement('a');
        link.href = previewUrl;
        link.download = value?.split('/').pop() || 'image';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }, [previewUrl, value]);

    const applyCrop = useCallback(async () => {
        if (!previewUrl) return;
        try {
            const blob = await getCroppedImg(previewUrl, cropData);
            const formData = new FormData();
            formData.append('file', blob, 'cropped.jpg');
            const res = await fetch('/upload/image', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: formData,
            });
            const payload = await res.json();
            if (payload?.success && payload.path) {
                onChange(payload.path);
                setPreviewUrl(mediaUrl(payload.path));
            }
        } catch (e) {
            console.error('Crop failed', e);
        }
        setMode('preview');
    }, [previewUrl, cropData, csrfToken, onChange]);

    const handlePickerSelect = (path) => {
        onChange(path);
        setPreviewUrl(mediaUrl(path));
    };

    return (
        <div className="admin-upload-field">
            <label>{label}{required ? ' *' : ''}</label>
            {!previewUrl ? (
                <>
                    <div className="d-flex gap-2 mb-2">
                        <div ref={containerRef} className="flex-grow-1"></div>
                        <button type="button" className="btn btn-outline-primary btn-sm" onClick={() => setPickerOpen(true)} title="Pick from Media Library" style={{ whiteSpace: 'nowrap', alignSelf: 'flex-start', marginTop: '2px' }}>
                            <i className="bi bi-images me-1"></i>Library
                        </button>
                    </div>
                    <input type="hidden" name={name} value={value || ''} readOnly />
                </>
            ) : (
                <div className="admin-upload-preview">
                    {mode === 'crop' ? (
                        <div className="admin-crop-container">
                            <Cropper
                                image={previewUrl}
                                crop={cropData}
                                zoom={zoom}
                                aspect={aspect}
                                onCropChange={setCropData}
                                onZoomChange={setZoom}
                            />
                        </div>
                    ) : (
                        <img src={previewUrl} alt={label} draggable={mode === 'drag'} style={{ cursor: mode === 'drag' ? 'grab' : 'default', ...(heroPreview ? { objectFit: 'cover', width: '100%', height: '200px', objectPosition: 'center' } : {}) }} />
                    )}
                    <div className="admin-upload-preview-toolbar">
                        <button type="button" className={`admin-preview-btn ${mode === 'preview' ? 'active' : ''}`} onClick={() => setMode('preview')} title="Preview"><i className="bi bi-eye"></i></button>
                        <button type="button" className={`admin-preview-btn ${mode === 'crop' ? 'active' : ''}`} onClick={() => { setMode('crop'); setCropData({ x: 0, y: 0 }); setZoom(1); }} title="Crop"><i className="bi bi-crop"></i></button>
                        <button type="button" className={`admin-preview-btn ${mode === 'drag' ? 'active' : ''}`} onClick={() => setMode(mode === 'drag' ? 'preview' : 'drag')} title="Drag to reposition"><i className="bi bi-arrows-move"></i></button>
                        <span className="admin-preview-divider"></span>
                        <button type="button" className="admin-preview-btn" onClick={() => setPickerOpen(true)} title="Replace from Library"><i className="bi bi-images"></i></button>
                        <button type="button" className="admin-preview-btn" onClick={downloadImage} title="Download"><i className="bi bi-download"></i></button>
                        <button type="button" className="admin-preview-btn admin-preview-btn-danger" onClick={removeImage} title="Remove"><i className="bi bi-trash"></i></button>
                    </div>
                    {mode === 'crop' && (
                        <div className="admin-crop-controls">
                            <label>Zoom</label>
                            <input type="range" min="1" max="3" step="0.1" value={zoom} onChange={(e) => setZoom(Number(e.target.value))} />
                            <label>Aspect</label>
                            <select value={aspect || ''} onChange={(e) => setAspect(e.target.value ? parseFloat(e.target.value) : null)}>
                                <option value="">Free</option>
                                <option value="1">1:1</option>
                                <option value="1.3333">4:3</option>
                                <option value="1.7778">16:9</option>
                            </select>
                            <button type="button" className="btn btn-sm btn-primary" onClick={applyCrop}>Apply crop</button>
                            <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => setMode('preview')}>Cancel</button>
                        </div>
                    )}
                    <div ref={containerRef} style={{ display: 'none' }}></div>
                    <input type="hidden" name={name} value={value || ''} readOnly />
                </div>
            )}
            <MediaPicker show={pickerOpen} onClose={() => setPickerOpen(false)} onSelect={handlePickerSelect} />
        </div>
    );
}
