import { useState, useEffect, useCallback } from 'react';
import mediaUrl from './mediaUrl';

export default function GalleryLightbox({ images, startIndex = 0, onClose }) {
    const [current, setCurrent] = useState(startIndex);

    const prev = useCallback(() => setCurrent(i => (i > 0 ? i - 1 : images.length - 1)), [images.length]);
    const next = useCallback(() => setCurrent(i => (i < images.length - 1 ? i + 1 : 0)), [images.length]);

    useEffect(() => {
        const handleKey = (e) => {
            if (e.key === 'Escape') onClose();
            if (e.key === 'ArrowLeft') prev();
            if (e.key === 'ArrowRight') next();
        };
        document.addEventListener('keydown', handleKey);
        return () => document.removeEventListener('keydown', handleKey);
    }, [onClose, prev, next]);

    const img = images[current];
    if (!img) return null;

    const src = typeof img === 'string' ? mediaUrl(img) : mediaUrl(img.path);
    const isVideo = img.type === 'video';

    return (
        <div className="gallery-lightbox-overlay" onClick={onClose}>
            <button type="button" className="gallery-lightbox-close" onClick={onClose} aria-label="Close">
                <i className="bi bi-x-lg"></i>
            </button>

            {images.length > 1 && (
                <button type="button" className="gallery-lightbox-nav gallery-lightbox-prev" onClick={(e) => { e.stopPropagation(); prev(); }} aria-label="Previous">
                    <i className="bi bi-chevron-left"></i>
                </button>
            )}

            <div className="gallery-lightbox-content" onClick={(e) => e.stopPropagation()}>
                {isVideo ? (
                    <video src={src} controls autoPlay className="gallery-lightbox-media" />
                ) : (
                    <img src={src} alt={`Gallery ${current + 1}`} className="gallery-lightbox-media" />
                )}
                <div className="gallery-lightbox-caption">
                    <span>{current + 1} / {images.length}</span>
                </div>
            </div>

            {images.length > 1 && (
                <button type="button" className="gallery-lightbox-nav gallery-lightbox-next" onClick={(e) => { e.stopPropagation(); next(); }} aria-label="Next">
                    <i className="bi bi-chevron-right"></i>
                </button>
            )}
        </div>
    );
}
