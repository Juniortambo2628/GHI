import { useState, useEffect, useRef, useCallback } from 'react';

export default function AdminCrudModal({ show, onClose, title, icon, activeView = 'view', tabs, children }) {
    const modalRef = useRef(null);
    const bsModalRef = useRef(null);
    const [currentTab, setCurrentTab] = useState(activeView);

    useEffect(() => {
        setCurrentTab(activeView);
    }, [activeView, show]);

    useEffect(() => {
        if (!show) return;

        const el = modalRef.current;
        if (!el) return;

        const existing = window.bootstrap.Modal.getInstance(el);
        if (existing) existing.dispose();

        bsModalRef.current = new window.bootstrap.Modal(el);
        bsModalRef.current.show();

        const handleHidden = () => {
            setCurrentTab('view');
            onClose();
        };
        el.addEventListener('hidden.bs.modal', handleHidden);

        return () => {
            el.removeEventListener('hidden.bs.modal', handleHidden);
            const inst = window.bootstrap.Modal.getInstance(el);
            if (inst) inst.dispose();
            bsModalRef.current = null;
        };
    }, [show, onClose]);

    const switchTab = useCallback((tab) => {
        setCurrentTab(tab);
    }, []);

    if (!show) return null;

    const hasTabs = tabs && tabs.length > 0;

    return (
        <div className="modal fade ghi-modal ghi-modal-crud" ref={modalRef} tabIndex="-1" aria-hidden="true">
            <div className="modal-dialog modal-dialog-centered modal-xl ghi-modal-crud-dialog">
                <div className="modal-content">
                    <div className="ghi-modal-layout">
                        {hasTabs && (
                            <div className="ghi-modal-sidebar">
                                <div className="ghi-modal-sidebar-header">
                                    {icon && <span className="ghi-modal-icon"><i className={icon}></i></span>}
                                    <h5 className="ghi-modal-title">{title}</h5>
                                </div>
                                <nav className="ghi-modal-tabs">
                                    {tabs.map((tab) => (
                                        <button
                                            key={tab.key}
                                            className={`ghi-modal-tab ${currentTab === tab.key ? 'active' : ''}`}
                                            onClick={() => switchTab(tab.key)}
                                            type="button"
                                        >
                                            {tab.icon && <i className={`bi bi-${tab.icon} me-2`}></i>}
                                            <span className="ghi-modal-tab-label">{tab.label}</span>
                                        </button>
                                    ))}
                                </nav>
                                <div className="ghi-modal-sidebar-footer">
                                    {tabs.find(t => t.key === currentTab)?.label || ''}
                                </div>
                            </div>
                        )}
                        <div className="ghi-modal-body">
                            <div className="ghi-modal-body-header">
                                <h4 className="ghi-modal-body-title">
                                    {hasTabs ? tabs.find(t => t.key === currentTab)?.label : title}
                                </h4>
                                <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div className="ghi-modal-body-content ghi-modal-body-scroll">
                                {hasTabs ? tabs.find(t => t.key === currentTab)?.content : children}
                            </div>
                            <div className="ghi-modal-body-footer">
                                <button type="button" className="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
