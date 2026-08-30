import { useState } from 'react';
import Modal from './Modal';
import GalleryLightbox from './GalleryLightbox';
import sanitizeHtml from './sanitizeHtml';
import stripHtml from './stripHtml';
import mediaUrl from './mediaUrl';

export default function ListingCard({ image, imageAlt, badges = [], title, description, meta = [], images = [], buttonText = 'View Details', onButtonClick, link, detailContent, index = 0 }) {
    const [showModal, setShowModal] = useState(false);
    const [lightboxIndex, setLightboxIndex] = useState(null);
    const sortedImages = [...images].sort((a, b) => a.sort_order - b.sort_order);

    const truncate = (text, len = 100) => {
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
            <div className="mb-0 listing-row-modal-text" dangerouslySetInnerHTML={{ __html: sanitizeHtml(description || '') }}></div>
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
        const sorted = [...images].sort((a, b) => a.sort_order - b.sort_order);
        const galleryContent = (
            <div className="modal-gallery-grid">
                {sorted.map((img, idx) => (
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
        <div className={`col-md-6 col-lg-4 listing-item mb-4`}>
            <div className="card h-100">
                <div className="card-img-wrapper" style={!image ? { display: 'flex', alignItems: 'center', justifyContent: 'center', background: '#f8f9fa' } : {}}>
                    <img src={image || '/Logo/Square-White-BG.png'} className="card-img-top" alt={imageAlt || 'Placeholder Logo'} loading="lazy" width="400" height="300" style={!image ? { objectFit: 'contain', height: '100%', padding: '2rem' } : {}} />
                </div>
                <div className="card-body d-flex flex-column">
                    {badges.length > 0 && (
                        <div className="card-badges">
                            {badges.map((badge, idx) => (
                                <span key={idx} className={`glass-pill ${badge.className || 'text-primary'}`}>{stripHtml(badge.text)}</span>
                            ))}
                        </div>
                    )}
                    <h5 className="card-title">{title}</h5>
                    {meta.map((item, idx) => (
                        <small key={idx} className="mb-1 d-inline-flex align-items-center">
                            <span className="glass-pill-sm me-1"><i className={`${item.icon}`}></i></span>
                            <span className="card-meta-text">{stripHtml(item.text)}</span>
                        </small>
                    ))}
                    <p className="card-text">{truncate(description)}</p>
                    <div className="mt-auto d-flex gap-2">
                        {link ? <a href={link} className="btn btn-primary btn-sm flex-grow-1">{buttonText}</a> : onButtonClick && <button className="btn btn-primary btn-sm flex-grow-1" onClick={onButtonClick}>{buttonText}</button>}
                        <button className="btn btn-outline-primary btn-sm" onClick={() => setShowModal(true)} title="More Information">
                            <i className="bi bi-info-circle"></i>
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
