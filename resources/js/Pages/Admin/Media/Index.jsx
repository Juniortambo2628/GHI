import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import AdminLayout from '../../../Layouts/AdminLayout';
import AdminConfirm from '../../../Components/Shared/AdminConfirm';
import AdminViewToggle from '../../../Components/Shared/AdminViewToggle';
import Pagination from '../../../Components/Shared/Pagination';

function formatSize(bytes) {
    if (!bytes) return '0 B';
    const units = ['B', 'KB', 'MB', 'GB'];
    const index = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
    return `${(bytes / 1024 ** index).toFixed(index ? 1 : 0)} ${units[index]}`;
}

export default function Index({ media, filters }) {
    const [selected, setSelected] = useState(new Set());
    const [renaming, setRenaming] = useState(null);
    const [renameValue, setRenameValue] = useState('');
    const [view, setView] = useState('grid');

    const update = (event) => router.get('/admin/media', { ...filters, [event.target.name]: event.target.value }, { preserveState: true });

    const toggleSelect = (path) => {
        setSelected((prev) => {
            const next = new Set(prev);
            next.has(path) ? next.delete(path) : next.add(path);
            return next;
        });
    };

    const toggleSelectAll = () => {
        if (selected.size === media.data.length) {
            setSelected(new Set());
        } else {
            setSelected(new Set(media.data.map(f => f.path)));
        }
    };

    const bulkDelete = () => {
        if (!selected.size) return;
        router.delete('/admin/media/bulk', { data: { paths: Array.from(selected) }, onSuccess: () => setSelected(new Set()) });
    };

    const startRename = (item) => {
        setRenaming(item.path);
        setRenameValue(item.name);
    };

    const submitRename = () => {
        if (!renameValue || renameValue === renaming) { setRenaming(null); return; }
        router.put('/admin/media/rename', { path: renaming, new_name: renameValue }, { onSuccess: () => setRenaming(null) });
    };

    const remove = (path) => router.delete('/admin/media', { data: { path } });

    const filterBar = (
        <div className="d-flex align-items-center gap-2 flex-wrap">
            <div className="admin-filter-row">
                <input name="search" defaultValue={filters.search || ''} onBlur={update} placeholder="Search files" />
                <select name="type" defaultValue={filters.type || ''} onChange={update}>
                    <option value="">All types</option>
                    <option value="images">Images</option>
                    <option value="documents">Documents</option>
                    <option value="files">Files</option>
                </select>
            </div>
            <AdminViewToggle view={view} setView={setView} />
        </div>
    );

    const bulkActions = selected.size > 0 ? (
        <div className="d-flex align-items-center gap-2">
            <span className="text-muted small">{selected.size} selected</span>
            <AdminConfirm message={`Delete ${selected.size} file(s)?`} onConfirm={bulkDelete}>
                <button type="button" className="btn btn-sm btn-danger"><i className="bi bi-trash me-1"></i>Delete</button>
            </AdminConfirm>
            <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => setSelected(new Set())}>Clear</button>
        </div>
    ) : null;

    return (
        <AdminLayout title="Media Library" description="Browse, download, rename, and remove uploaded assets." toolbarLeft={filterBar} toolbarRight={bulkActions}>
            <Head title="Media Library - Admin" />

            {view === 'grid' ? (
                <div className="media-grid">
                    {media.data.map((item) => (
                        <article className={`media-item ${selected.has(item.path) ? 'border-primary' : ''}`} key={item.path}>
                            <div className="media-item-select">
                                <input
                                    type="checkbox"
                                    className="form-check-input"
                                    checked={selected.has(item.path)}
                                    onChange={() => toggleSelect(item.path)}
                                    aria-label={`Select ${item.name}`}
                                />
                            </div>
                            {item.type === 'images' ? (
                                <img src={item.url} alt={item.name} loading="lazy" />
                            ) : (
                                <div className="media-file-icon"><i className="bi bi-file-earmark"></i></div>
                            )}
                            <div className="media-item-body">
                                {renaming === item.path ? (
                                    <div className="d-flex gap-1">
                                        <input
                                            type="text"
                                            className="form-control form-control-sm"
                                            value={renameValue}
                                            onChange={(e) => setRenameValue(e.target.value)}
                                            onKeyDown={(e) => { if (e.key === 'Enter') submitRename(); if (e.key === 'Escape') setRenaming(null); }}
                                            autoFocus
                                        />
                                        <button type="button" className="btn btn-sm btn-primary" onClick={submitRename}><i className="bi bi-check"></i></button>
                                        <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => setRenaming(null)}><i className="bi bi-x"></i></button>
                                    </div>
                                ) : (
                                    <>
                                        <strong title={item.name}>{item.name}</strong>
                                        <small>{formatSize(item.size)}</small>
                                    </>
                                )}
                                <div className="media-item-actions">
                                    <a href={item.url} download={item.name} className="btn btn-sm btn-outline-secondary" aria-label={`Download ${item.name}`}><i className="bi bi-download"></i></a>
                                    <button type="button" className="btn btn-sm btn-outline-primary" onClick={() => startRename(item)} aria-label={`Rename ${item.name}`}><i className="bi bi-pencil"></i></button>
                                    <AdminConfirm message={`Delete ${item.name}?`} onConfirm={() => remove(item.path)}>
                                        <button type="button" className="btn btn-sm btn-outline-danger" aria-label={`Delete ${item.name}`}><i className="bi bi-trash"></i></button>
                                    </AdminConfirm>
                                </div>
                            </div>
                        </article>
                    ))}
                </div>
            ) : (
                <div className="content-card">
                    <div className="table-responsive">
                        <table className="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style={{ width: '2.5rem' }}>
                                        <input type="checkbox" className="form-check-input" checked={selected.size === media.data.length && media.data.length > 0} onChange={toggleSelectAll} aria-label="Select all" />
                                    </th>
                                    <th>Name</th><th>Size</th><th>Type</th><th></th>
                                </tr>
                            </thead>
                            <tbody>
                                {media.data.map(item => (
                                    <tr key={item.path}>
                                        <td><input type="checkbox" className="form-check-input" checked={selected.has(item.path)} onChange={() => toggleSelect(item.path)} aria-label={`Select ${item.name}`} /></td>
                                        <td>{renaming === item.path ? (
                                            <div className="d-flex gap-1">
                                                <input type="text" className="form-control form-control-sm" value={renameValue} onChange={(e) => setRenameValue(e.target.value)} onKeyDown={(e) => { if (e.key === 'Enter') submitRename(); if (e.key === 'Escape') setRenaming(null); }} autoFocus />
                                                <button type="button" className="btn btn-sm btn-primary" onClick={submitRename}><i className="bi bi-check"></i></button>
                                                <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => setRenaming(null)}><i className="bi bi-x"></i></button>
                                            </div>
                                        ) : <>{item.name}</>}</td>
                                        <td>{formatSize(item.size)}</td>
                                        <td>{item.type}</td>
                                        <td>
                                            <a href={item.url} download={item.name} className="btn btn-sm btn-outline-secondary me-1"><i className="bi bi-download"></i></a>
                                            {renaming !== item.path && <button type="button" className="btn btn-sm btn-outline-primary me-1" onClick={() => startRename(item)}><i className="bi bi-pencil"></i></button>}
                                            <AdminConfirm message={`Delete ${item.name}?`} onConfirm={() => remove(item.path)}>
                                                <button type="button" className="btn btn-sm btn-outline-danger"><i className="bi bi-trash"></i></button>
                                            </AdminConfirm>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            )}
            <Pagination data={media} />
        </AdminLayout>
    );
}
