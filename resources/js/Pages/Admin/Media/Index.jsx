import { Head, router, usePage } from '@inertiajs/react';
import { useState, useRef } from 'react';
import AdminLayout from '../../../Layouts/AdminLayout';
import AdminConfirm from '../../../Components/Shared/AdminConfirm';
import AdminViewToggle from '../../../Components/Shared/AdminViewToggle';
import Pagination from '../../../Components/Shared/Pagination';
import mediaUrl from '../../../Components/Shared/mediaUrl';

function formatSize(bytes) {
    if (!bytes) return '0 B';
    const units = ['B', 'KB', 'MB', 'GB'];
    const index = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
    return `${(bytes / 1024 ** index).toFixed(index ? 1 : 0)} ${units[index]}`;
}

export default function Index({ media, filters, groups = [] }) {
    const { csrf_token: csrfToken } = usePage().props;
    const [selected, setSelected] = useState(new Set());
    const [view, setView] = useState('grid');
    const [editing, setEditing] = useState(null);
    const [editForm, setEditForm] = useState({ alt_text: '', caption: '', group: '' });
    const fileInputRef = useRef(null);
    const [uploading, setUploading] = useState(false);

    const update = (event) => router.get('/admin/media', { ...filters, [event.target.name]: event.target.value }, { preserveState: true, replace: true });

    const toggleSelect = (id) => {
        setSelected((prev) => {
            const next = new Set(prev);
            next.has(id) ? next.delete(id) : next.add(id);
            return next;
        });
    };

    const toggleSelectAll = () => {
        if (selected.size === media.data.length) {
            setSelected(new Set());
        } else {
            setSelected(new Set(media.data.map(f => f.id)));
        }
    };

    const bulkDelete = () => {
        if (!selected.size) return;
        router.delete('/admin/media/bulk', { data: { ids: Array.from(selected) }, onSuccess: () => setSelected(new Set()) });
    };

    const startEdit = (item) => {
        setEditing(item.id);
        setEditForm({ alt_text: item.alt_text || '', caption: item.caption || '', group: item.group || '' });
    };

    const saveEdit = () => {
        router.put(`/admin/media/${editing}`, editForm, { onSuccess: () => setEditing(null) });
    };

    const remove = (id) => router.delete(`/admin/media/${id}`);

    const uploadFiles = async (fileList) => {
        setUploading(true);
        for (const file of fileList) {
            const formData = new FormData();
            formData.append('file', file);
            try {
                await fetch('/admin/media', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || '', Accept: 'application/json' },
                    body: formData,
                });
            } catch (e) { /* ignore */ }
        }
        setUploading(false);
        router.reload({ only: ['media'] });
    };

    const filterBar = (
        <div className="d-flex align-items-center gap-2 flex-wrap">
            <div className="admin-filter-row">
                <input name="search" defaultValue={filters.search || ''} onBlur={update} placeholder="Search media" />
                <select name="type" defaultValue={filters.type || ''} onChange={update}>
                    <option value="">All types</option>
                    <option value="images">Images</option>
                    <option value="videos">Videos</option>
                </select>
                {groups.length > 0 && (
                    <select name="group" defaultValue={filters.group || ''} onChange={update}>
                        <option value="">All groups</option>
                        {groups.map(g => <option key={g} value={g}>{g}</option>)}
                    </select>
                )}
            </div>
            <input ref={fileInputRef} type="file" accept="image/*" multiple style={{ display: 'none' }} onChange={(e) => { if (e.target.files.length) uploadFiles(e.target.files); e.target.value = ''; }} />
            <button type="button" className="btn btn-sm btn-primary" onClick={() => fileInputRef.current?.click()} disabled={uploading}>
                <i className="bi bi-upload me-1"></i>{uploading ? 'Uploading...' : 'Upload'}
            </button>
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
        <AdminLayout title="Media Library" description="Browse, upload, organize, and remove uploaded assets." toolbarLeft={filterBar} toolbarRight={bulkActions}>
            <Head title="Media Library - Admin" />

            {view === 'grid' ? (
                <div className="media-grid">
                    {media.data.map((item) => (
                        <article className={`media-item ${selected.has(item.id) ? 'border-primary' : ''}`} key={item.id}>
                            <div className="media-item-select">
                                <input type="checkbox" className="form-check-input" checked={selected.has(item.id)} onChange={() => toggleSelect(item.id)} aria-label={`Select ${item.original_name}`} />
                            </div>
                            {item.mime_type?.startsWith('image/') ? (
                                <img src={mediaUrl(item.path)} alt={item.alt_text || item.original_name} loading="lazy" />
                            ) : item.mime_type?.startsWith('video/') ? (
                                <div className="media-file-icon"><i className="bi bi-play-circle"></i></div>
                            ) : (
                                <div className="media-file-icon"><i className="bi bi-file-earmark"></i></div>
                            )}
                            <div className="media-item-body">
                                {editing === item.id ? (
                                    <div className="d-flex flex-column gap-1">
                                        <input type="text" className="form-control form-control-sm" placeholder="Alt text" value={editForm.alt_text} onChange={e => setEditForm({ ...editForm, alt_text: e.target.value })} />
                                        <input type="text" className="form-control form-control-sm" placeholder="Caption" value={editForm.caption} onChange={e => setEditForm({ ...editForm, caption: e.target.value })} />
                                        <input type="text" className="form-control form-control-sm" placeholder="Group" value={editForm.group} onChange={e => setEditForm({ ...editForm, group: e.target.value })} />
                                        <div className="d-flex gap-1">
                                            <button type="button" className="btn btn-sm btn-primary" onClick={saveEdit}><i className="bi bi-check"></i></button>
                                            <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => setEditing(null)}><i className="bi bi-x"></i></button>
                                        </div>
                                    </div>
                                ) : (
                                    <>
                                        <strong title={item.original_name}>{item.original_name}</strong>
                                        <small>{formatSize(item.file_size)}{item.width && item.height ? ` · ${item.width}×${item.height}` : ''}{item.group ? ` · ${item.group}` : ''}</small>
                                    </>
                                )}
                                <div className="media-item-actions">
                                    <button type="button" className="btn btn-sm btn-outline-primary" onClick={() => startEdit(item)} title="Edit details"><i className="bi bi-pencil"></i></button>
                                    <a href={mediaUrl(item.path)} download={item.original_name} className="btn btn-sm btn-outline-secondary" title="Download"><i className="bi bi-download"></i></a>
                                    <AdminConfirm message={`Delete ${item.original_name}?`} onConfirm={() => remove(item.id)}>
                                        <button type="button" className="btn btn-sm btn-outline-danger" title="Delete"><i className="bi bi-trash"></i></button>
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
                                    <th>Preview</th><th>Name</th><th>Size</th><th>Type</th><th>Group</th><th></th>
                                </tr>
                            </thead>
                            <tbody>
                                {media.data.map(item => (
                                    <tr key={item.id}>
                                        <td><input type="checkbox" className="form-check-input" checked={selected.has(item.id)} onChange={() => toggleSelect(item.id)} /></td>
                                        <td>{item.mime_type?.startsWith('image/') && <img src={mediaUrl(item.path)} alt="" style={{ width: 48, height: 36, objectFit: 'cover', borderRadius: 4 }} />}</td>
                                        <td>{editing === item.id ? (
                                            <div className="d-flex gap-1">
                                                <input type="text" className="form-control form-control-sm" value={editForm.alt_text} onChange={e => setEditForm({ ...editForm, alt_text: e.target.value })} placeholder="Alt text" />
                                                <button type="button" className="btn btn-sm btn-primary" onClick={saveEdit}><i className="bi bi-check"></i></button>
                                                <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => setEditing(null)}><i className="bi bi-x"></i></button>
                                            </div>
                                        ) : item.original_name}</td>
                                        <td>{formatSize(item.file_size)}</td>
                                        <td>{item.mime_type?.split('/')[1] || item.mime_type}</td>
                                        <td>{item.group || <span className="text-muted">—</span>}</td>
                                        <td>
                                            <button type="button" className="btn btn-sm btn-outline-primary me-1" onClick={() => startEdit(item)}><i className="bi bi-pencil"></i></button>
                                            <a href={mediaUrl(item.path)} download={item.original_name} className="btn btn-sm btn-outline-secondary me-1"><i className="bi bi-download"></i></a>
                                            <AdminConfirm message={`Delete ${item.original_name}?`} onConfirm={() => remove(item.id)}>
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
