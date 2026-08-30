import { Link } from '@inertiajs/react';
import AdminConfirm from './AdminConfirm';

export default function AdminDataTable({ columns, data, baseUrl, deleteHandler, view = true, selected, onSelect, onSelectAll, viewOnly = false, onView, onEdit }) {
    const rows = data?.data || [];
    const hasSelection = !viewOnly && typeof onSelect === 'function';
    const allSelected = hasSelection && rows.length > 0 && selected?.size === rows.length;
    const showActions = viewOnly ? view : (view || deleteHandler);
    const useModal = typeof onView === 'function' || typeof onEdit === 'function';

    return (
        <div className="content-card">
            <div className="card-body p-0">
                <table className="table table-hover mb-0">
                    <thead>
                        <tr>
                            {hasSelection && (
                                <th style={{ width: '2.5rem' }}>
                                    <input
                                        type="checkbox"
                                        className="form-check-input"
                                        checked={allSelected}
                                        onChange={onSelectAll}
                                        aria-label="Select all"
                                    />
                                </th>
                            )}
                            {columns.map((col, idx) => (
                                <th key={idx}>{col.header}</th>
                            ))}
                            {showActions && <th>Actions</th>}
                        </tr>
                    </thead>
                    <tbody>
                        {rows.length === 0 && <tr><td className="admin-empty-state" colSpan={columns.length + (hasSelection ? 2 : 1)}>No records found.</td></tr>}
                        {rows.map(item => (
                            <tr key={item.id}>
                                {hasSelection && (
                                    <td>
                                        <input
                                            type="checkbox"
                                            className="form-check-input"
                                            checked={selected?.has(item.id) || false}
                                            onChange={() => onSelect(item.id)}
                                            aria-label={`Select ${item.title || item.name || item.id}`}
                                        />
                                    </td>
                                )}
                                {columns.map((col, idx) => (
                                    <td key={idx}>{col.render ? col.render(item) : item[col.key]}</td>
                                ))}
                                {showActions && <td>
                                    {view && (useModal && onView ? (
                                        <button type="button" className="btn btn-sm btn-action-view me-1" aria-label="View" onClick={() => onView(item)}><i className="bi bi-eye"></i></button>
                                    ) : (
                                        <Link href={`${baseUrl}/${item.id}`} className="btn btn-sm btn-action-view me-1" aria-label="View"><i className="bi bi-eye"></i></Link>
                                    ))}
                                    {!viewOnly && (useModal && onEdit ? (
                                        <button type="button" className="btn btn-sm btn-action-edit me-1" aria-label="Edit" onClick={() => onEdit(item)}><i className="bi bi-pencil"></i></button>
                                    ) : (
                                        <Link href={`${baseUrl}/${item.id}/edit`} className="btn btn-sm btn-action-edit me-1" aria-label="Edit"><i className="bi bi-pencil"></i></Link>
                                    ))}
                                    {!viewOnly && deleteHandler && <AdminConfirm message="Delete this record?" onConfirm={() => deleteHandler(item.id)}><button className="btn btn-sm btn-outline-danger" aria-label="Delete"><i className="bi bi-trash"></i></button></AdminConfirm>}
                                </td>}
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
