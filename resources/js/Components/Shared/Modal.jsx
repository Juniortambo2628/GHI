import { useState, useEffect, useRef } from 'react';

export default function Modal({ show, onClose, title, icon, tabs = [], children, wide = false }) {
    const modalRef = useRef(null);
    const [activeTab, setActiveTab] = useState(0);
    const bsModalRef = useRef(null);

    useEffect(() => {
        if (show && modalRef.current) {
            bsModalRef.current = new window.bootstrap.Modal(modalRef.current);
            bsModalRef.current.show();
            modalRef.current.addEventListener('hidden.bs.modal', () => {
                setActiveTab(0);
                onClose();
            });
            return () => {
                if (bsModalRef.current) bsModalRef.current.dispose();
            };
        }
    }, [show, onClose]);

    useEffect(() => {
        if (show) setActiveTab(0);
    }, [show]);

    if (!show) return null;

    const hasTabs = tabs.length > 0;
    const totalSteps = hasTabs ? tabs.length : 0;
    const isGalleryTab = hasTabs && tabs[activeTab]?.label === 'Gallery';

    return (
        <div className="modal fade ghi-modal" ref={modalRef} tabIndex="-1" aria-hidden="true">
            <div className={`modal-dialog modal-dialog-centered ${wide ? 'modal-xl' : 'modal-lg'}`}>
                <div className="modal-content">
                    <div className="ghi-modal-layout">
                        {hasTabs && (
                            <div className="ghi-modal-sidebar">
                                <div className="ghi-modal-sidebar-header">
                                    {icon && <span className="ghi-modal-icon"><i className={icon}></i></span>}
                                    <h5 className="ghi-modal-title">{title}</h5>
                                </div>
                                <nav className="ghi-modal-tabs">
                                    {tabs.map((tab, idx) => (
                                        <button
                                            key={idx}
                                            className={`ghi-modal-tab ${idx === activeTab ? 'active' : ''} ${idx < activeTab ? 'completed' : ''}`}
                                            onClick={() => setActiveTab(idx)}
                                        >
                                            <span className="ghi-modal-tab-number">{idx + 1}</span>
                                            <span className="ghi-modal-tab-label">{tab.label}</span>
                                        </button>
                                    ))}
                                </nav>
                                <div className="ghi-modal-sidebar-footer">
                                    Step {activeTab + 1} of {totalSteps}
                                </div>
                            </div>
                        )}
                        <div className={`ghi-modal-body ${isGalleryTab ? 'ghi-modal-body-scrollable' : ''}`}>
                            <div className="ghi-modal-body-header">
                                <h4 className="ghi-modal-body-title">
                                    {hasTabs ? tabs[activeTab]?.label : title}
                                </h4>
                                <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div className={`ghi-modal-body-content ${isGalleryTab ? 'ghi-modal-body-content-scrollable' : ''}`}>
                                {hasTabs ? tabs[activeTab]?.content : children}
                            </div>
                            <div className="ghi-modal-body-footer">
                                <button type="button" className="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" className="btn btn-primary" data-bs-dismiss="modal">Done</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
