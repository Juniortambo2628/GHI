import { useState } from 'react';
import Modal from './Modal';
import GalleryLightbox from './GalleryLightbox';
import sanitizeHtml from './sanitizeHtml';
import stripHtml from './stripHtml';
import mediaUrl from './mediaUrl';

export default function TimelineCard({ image, imageAlt, badges = [], title, description, meta = [], images = [], buttonText = 'View Details', onButtonClick, detailContent, side = 'left' }) {
    const [showModal, setShowModal] = useState(false);
    const [lightboxIndex, setLightboxIndex] = useState(null);
    const sortedImages = [...images].sort((a, b) => a.sort_order - b.sort_order);

    const truncate = (text, len = 150) => {
        if (!text) return '';
        const plain = stripHtml(text);
        return plain.length > len ? plain.substring(0, len) + '...' : plain;
    };

    const overviewContent = (
        <div>
            {image && <img src={image} className="img-fluid rounded mb-3 w-100 listing-row-modal-img" alt={imageAlt} width="800" height="400" />}
            {badges.length > 0 && (
                <div className="d-flex flex-wrap gap-2 mb-3">
                    {badges.map((badge, idx) => (
                        <span key={idx} className={`glass-pill ${badge.className || 'text-primary'}`}>{stripHtml(badge.text)}</span>
                    ))}
                </div>
            )}
            <div className="listing-row-modal-text mb-0" dangerouslySetInnerHTML={{ __html: sanitizeHtml(description || '') }}></div>
        </div>
    );

    const detailsContent = (
        <div>
            {meta.length > 0 ? (
                <div className="d-flex flex-column gap-3">
                    {meta.map((item, idx) => (
                        <div key={idx} className="d-flex align-items-center gap-3 p-3 rounded listing-row-detail-item">
                            <span className="glass-pill-sm"><i className={item.icon}></i></span>
                            <span className="listing-row-detail-text">{stripHtml(item.text)}</span>
                        </div>
                    ))}
                </div>
            ) : (
                <p className="text-muted mb-0">No additional details available.</p>
            )}
        </div>
    );

    const tabs = [];
    tabs.push({ label: 'Overview', content: overviewContent });
    if (meta.length > 0) {
        tabs.push({ label: 'Details', content: detailsContent });
    }
    if (images && images.length > 0) {
        const galleryContent = (
            <div className="modal-gallery-grid">
                {sortedImages.map((img, idx) => (
                    <div key={img.id} className="modal-gallery-thumb" onClick={() => setLightboxIndex(idx)}>
                        {img.type === 'video' ? (
                            <video src={mediaUrl(img.path)} muted loop playsInline preload="metadata" />
                        ) : (
                            <img src={mediaUrl(img.path)} alt={`Gallery ${idx + 1}`} />
                        )}
                        {img.type === 'video' && <div className="modal-gallery-video-badge"><i className="bi bi-play-circle"></i></div>}
                    </div>
                ))}
            </div>
        );
        tabs.push({ label: 'Gallery', content: galleryContent });
    }
    if (detailContent) {
        tabs.push({ label: 'In-Depth', content: <div dangerouslySetInnerHTML={{ __html: sanitizeHtml(detailContent) }}></div> });
    }

    return (
        <div className={`timeline-item timeline-${side}`}>
            <div className="timeline-dot"></div>
            <div className="timeline-card">
                <div className="timeline-card-img-wrapper" style={!image ? { display: 'flex', alignItems: 'center', justifyContent: 'center', background: '#f8f9fa' } : {}}>
                    <img src={image || '/Logo/Square-White-BG.png'} className="timeline-card-img" alt={imageAlt || 'Placeholder Logo'} loading="lazy" width="400" height="250" style={!image ? { objectFit: 'contain', height: '100%', padding: '2rem' } : {}} />
                </div>
                <div className="timeline-card-body">
                <div className="timeline-card-badges">
                    {badges.map((badge, idx) => (
                        <span key={idx} className={`glass-pill ${badge.className || 'text-primary'}`}>{stripHtml(badge.text)}</span>
                    ))}
                </div>
                    <h5 className="timeline-card-title">{title}</h5>
                    <div className="timeline-card-meta">
                        {meta.map((item, idx) => (
                            <small key={idx} className="d-inline-flex align-items-center me-3">
                                <span className="glass-pill-sm me-1"><i className={item.icon}></i></span>
                                <span className="card-meta-text">{stripHtml(item.text)}</span>
                            </small>
                        ))}
                    </div>
                    <p className="timeline-card-text">{truncate(description)}</p>
                    <div className="timeline-card-actions">
                        {onButtonClick && <button className="btn btn-primary btn-sm" onClick={onButtonClick}>{buttonText}</button>}
                        <button className="btn btn-outline-primary btn-sm" onClick={() => setShowModal(true)} title="More Information">
                            <i className="bi bi-info-circle"></i> Details
                        </button>
                    </div>
                </div>
            </div>
            <Modal show={showModal} onClose={() => setShowModal(false)} title={title} icon="bi bi-info-circle" tabs={tabs} wide={true} />
            {lightboxIndex !== null && (
                <GalleryLightbox images={sortedImages} startIndex={lightboxIndex} onClose={() => setLightboxIndex(null)} />
            )}
        </div>
    );
}
