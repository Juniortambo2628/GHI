import { useState } from 'react';
import Modal from './Modal';
import sanitizeHtml from './sanitizeHtml';
import stripHtml from './stripHtml';

export default function TimelineCard({ image, imageAlt, badges = [], title, description, meta = [], buttonText = 'View Details', onButtonClick, side = 'left' }) {
    const [showModal, setShowModal] = useState(false);

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
                        <span key={idx} className={`glass-pill ${badge.className || ''}`}>{stripHtml(badge.text)}</span>
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

    const tabs = meta.length > 0
        ? [{ label: 'Overview', content: overviewContent }, { label: 'Details', content: detailsContent }]
        : [{ label: 'Overview', content: overviewContent }];

    return (
        <div className={`timeline-item timeline-${side}`}>
            <div className="timeline-dot"></div>
            <div className="timeline-card">
                {image && (
                    <div className="timeline-card-img-wrapper">
                        <img src={image} className="timeline-card-img" alt={imageAlt} loading="lazy" width="400" height="250" />
                    </div>
                )}
                <div className="timeline-card-body">
                <div className="timeline-card-badges">
                    {badges.map((badge, idx) => (
                        <span key={idx} className={`glass-pill ${badge.className || ''}`}>{stripHtml(badge.text)}</span>
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
            <Modal show={showModal} onClose={() => setShowModal(false)} title={title} icon="bi bi-info-circle" tabs={tabs} />
        </div>
    );
}
