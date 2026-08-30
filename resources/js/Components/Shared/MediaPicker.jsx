import { useState, useEffect, useCallback } from 'react';
import { usePage } from '@inertiajs/react';
import mediaUrl from './mediaUrl';

export default function MediaPicker({ show, onClose, onSelect }) {
    const { csrf_token: csrfToken } = usePage().props;
    const [assets, setAssets] = useState([]);
    const [loading, setLoading] = useState(false);
    const [search, setSearch] = useState('');
    const [page, setPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [selectedId, setSelectedId] = useState(null);

    const fetchAssets = useCallback(async (p = 1, q = '') => {
        setLoading(true);
        try {
            const params = new URLSearchParams({ page: p, per_page: 24 });
            if (q) params.set('search', q);
            const res = await fetch(`/admin/media-picker?${params}`, {
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || '' },
            });
            const data = await res.json();
            setAssets(p === 1 ? data.data : prev => [...prev, ...data.data]);
            setLastPage(data.last_page);
            setPage(data.current_page);
        } catch (e) {
            console.error('Failed to load media assets', e);
        }
        setLoading(false);
    }, [csrfToken]);

    useEffect(() => {
        if (show) {
            setAssets([]);
            setPage(1);
            setSearch('');
            setSelectedId(null);
            fetchAssets(1, '');
        }
    }, [show, fetchAssets]);

    const handleSearch = (e) => {
        const val = e.target.value;
        setSearch(val);
        setAssets([]);
        fetchAssets(1, val);
    };

    const handleLoadMore = () => {
        if (page < lastPage && !loading) {
            fetchAssets(page + 1, search);
        }
    };

    const handleSelect = () => {
        const asset = assets.find(a => a.id === selectedId);
        if (asset && onSelect) {
            onSelect(asset.path);
        }
        onClose();
    };

    if (!show) return null;

    return (
        <div className="modal fade show d-block" tabIndex="-1" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }} onClick={onClose}>
            <div className="modal-dialog modal-lg modal-dialog-scrollable" onClick={e => e.stopPropagation()}>
                <div className="modal-content">
                    <div className="modal-header">
                        <h5 className="modal-title"><i className="bi bi-images me-2"></i>Select from Media Library</h5>
                        <button type="button" className="btn-close" onClick={onClose}></button>
                    </div>
                    <div className="modal-body">
                        <div className="mb-3">
                            <input type="text" className="form-control" placeholder="Search images by name or path..." value={search} onChange={handleSearch} autoFocus />
                        </div>
                        {assets.length === 0 && !loading && (
                            <div className="text-center text-muted py-5">
                                <i className="bi bi-image fs-1 d-block mb-2"></i>
                                <p>No images found. Upload images via the Media Library first.</p>
                            </div>
                        )}
                        <div className="row g-2">
                            {assets.map(asset => (
                                <div key={asset.id} className="col-4 col-md-3 col-lg-2">
                                    <div
                                        className={`media-picker-thumb position-relative ${selectedId === asset.id ? 'selected' : ''}`}
                                        onClick={() => setSelectedId(asset.id)}
                                        onDoubleClick={() => { onSelect(asset.path); onClose(); }}
                                        style={{ cursor: 'pointer', aspectRatio: '1', overflow: 'hidden', borderRadius: '6px', border: selectedId === asset.id ? '3px solid var(--bs-primary)' : '3px solid transparent', transition: 'border-color 0.15s' }}
                                    >
                                        <img src={mediaUrl(asset.path)} alt={asset.alt_text || asset.original_name || ''} style={{ width: '100%', height: '100%', objectFit: 'cover' }} loading="lazy" />
                                        {selectedId === asset.id && (
                                            <div className="position-absolute top-0 end-0 m-1">
                                                <i className="bi bi-check-circle-fill text-primary fs-5"></i>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            ))}
                        </div>
                        {page < lastPage && (
                            <div className="text-center mt-3">
                                <button type="button" className="btn btn-outline-secondary btn-sm" onClick={handleLoadMore} disabled={loading}>
                                    {loading ? 'Loading...' : 'Load More'}
                                </button>
                            </div>
                        )}
                    </div>
                    <div className="modal-footer">
                        <button type="button" className="btn btn-outline-secondary" onClick={onClose}>Cancel</button>
                        <button type="button" className="btn btn-primary" onClick={handleSelect} disabled={!selectedId}>
                            <i className="bi bi-check-lg me-1"></i>Select Image
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}
