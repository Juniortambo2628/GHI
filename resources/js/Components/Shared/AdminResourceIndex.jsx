import { Head, Link, router } from '@inertiajs/react';
import { useState, useCallback } from 'react';
import AdminDataTable from './AdminDataTable';
import AdminLayout from '../../Layouts/AdminLayout';
import AdminConfirm from './AdminConfirm';
import AdminViewToggle from './AdminViewToggle';
import AdminCrudModal from './AdminCrudModal';
import AdminCrudModalView from './AdminCrudModalView';
import Pagination from './Pagination';

const DEFAULT_STATUS_OPTIONS = [
    { value: 'draft', label: 'Draft' },
    { value: 'published', label: 'Published' },
    { value: 'archived', label: 'Archived' },
];

export default function AdminResourceIndex({
    title, description, resource, data, columns, createLabel, filters = {},
    filterTypes = ['search', 'status'], statusOptions, categoryOptions, canDelete = false,
    modalCrud = false, entityName, viewFields, renderCreateContent, renderEditContent,
    createDefaults = {}, editGetData, onCreateSuccess, onEditSuccess,
}) {
    const [view, setView] = useState('list');
    const [selected, setSelected] = useState(new Set());
    const [modalState, setModalState] = useState({ show: false, mode: 'view', item: null });
    const readOnly = !createLabel;
    const deleteRecord = id => router.delete(`${resource}/${id}`);
    const statuses = statusOptions || DEFAULT_STATUS_OPTIONS;

    const openModal = useCallback((mode, item = null) => {
        setModalState({ show: true, mode, item });
    }, []);

    const closeModal = useCallback(() => {
        setModalState({ show: false, mode: 'view', item: null });
    }, []);

    const handleView = useCallback((item) => {
        openModal('view', item);
    }, [openModal]);

    const handleEdit = useCallback((item) => {
        openModal('edit', item);
    }, [openModal]);

    const handleCreate = useCallback(() => {
        openModal('create', null);
    }, [openModal]);

    const handleDelete = useCallback((id) => {
        router.delete(`${resource}/${id}`, {
            onSuccess: () => {
                closeModal();
            }
        });
    }, [resource, closeModal]);

    const toggleSelect = (id) => {
        setSelected(prev => {
            const next = new Set(prev);
            next.has(id) ? next.delete(id) : next.add(id);
            return next;
        });
    };

    const toggleSelectAll = () => {
        const rows = data?.data || [];
        if (selected.size === rows.length) {
            setSelected(new Set());
        } else {
            setSelected(new Set(rows.map(r => r.id)));
        }
    };

    const bulkDelete = () => {
        if (!selected.size) return;
        const promises = Array.from(selected).map(id => router.delete(`${resource}/${id}`));
        Promise.all(promises).then(() => setSelected(new Set()));
    };

    const filterBar = (
        <div className="d-flex align-items-center gap-2 flex-nowrap" style={{ flexWrap: 'nowrap' }}>
            <div className="admin-filter-row">
                {filterTypes.includes('search') && <input name="search" defaultValue={filters.search || ''} onBlur={event => router.get(resource, { ...filters, search: event.target.value }, { preserveState: true })} placeholder={`Search ${title.toLowerCase()}`} />}
                {filterTypes.includes('status') && (
                    <select name="status" defaultValue={filters.status || ''} onChange={event => router.get(resource, { ...filters, status: event.target.value }, { preserveState: true })}>
                        <option value="">All statuses</option>
                        {statuses.map(s => <option key={s.value} value={s.value}>{s.label}</option>)}
                    </select>
                )}
                {filterTypes.includes('category') && categoryOptions ? (
                    <select name="category" defaultValue={filters.category || ''} onChange={event => router.get(resource, { ...filters, category: event.target.value }, { preserveState: true })}>
                        <option value="">All categories</option>
                        {categoryOptions.map(c => <option key={c.value} value={c.value}>{c.label}</option>)}
                    </select>
                ) : filterTypes.includes('category') && (
                    <input name="category" defaultValue={filters.category || ''} onBlur={event => router.get(resource, { ...filters, category: event.target.value }, { preserveState: true })} placeholder="Category" />
                )}
                {filterTypes.includes('dates') && <><input type="date" name="from" defaultValue={filters.from || ''} onChange={event => router.get(resource, { ...filters, from: event.target.value }, { preserveState: true })} /><input type="date" name="to" defaultValue={filters.to || ''} onChange={event => router.get(resource, { ...filters, to: event.target.value }, { preserveState: true })} /></>}
            </div>
            <a className="btn btn-sm btn-outline-secondary flex-shrink-0" href={`/admin/exports/${resource.split('/').pop()}`} aria-label={`Export ${title}`}><i className="bi bi-download"></i></a>
            <AdminViewToggle view={view} setView={setView} />
        </div>
    );

    const bulkActions = selected.size > 0 ? (
        <div className="d-flex align-items-center gap-2">
            <span className="text-muted small">{selected.size} selected</span>
            <AdminConfirm message={`Delete ${selected.size} record(s)?`} onConfirm={bulkDelete}>
                <button type="button" className="btn btn-sm btn-danger"><i className="bi bi-trash me-1"></i>Delete</button>
            </AdminConfirm>
            <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => setSelected(new Set())}>Clear</button>
        </div>
    ) : null;

    const toolbarRight = !readOnly ? (
        <div className="d-flex align-items-center gap-2 flex-shrink-0">
            {bulkActions}
            {modalCrud ? (
                <button type="button" className="btn btn-sm btn-primary text-nowrap flex-shrink-0" onClick={handleCreate}>
                    <i className="bi bi-plus-circle me-1"></i>{createLabel}
                </button>
            ) : (
                <Link href={`${resource}/create`} className="btn btn-sm btn-primary text-nowrap flex-shrink-0">
                    <i className="bi bi-plus-circle me-1"></i>{createLabel}
                </Link>
            )}
        </div>
    ) : bulkActions || null;

    const modalTabs = modalCrud ? (() => {
        const tabs = [];
        if (modalState.mode === 'view' || modalState.mode === 'edit') {
            tabs.push({
                key: 'view',
                label: 'Details',
                icon: 'eye',
                content: (
                    <AdminCrudModalView
                        entity={entityName || title}
                        item={modalState.item}
                        fields={viewFields || []}
                        onEdit={handleEdit}
                        onDelete={handleDelete}
                    />
                ),
            });
        }
        if (modalState.mode === 'edit') {
            tabs.push({
                key: 'edit',
                label: `Edit ${entityName || title}`,
                icon: 'pencil',
                content: renderEditContent ? renderEditContent({
                    item: modalState.item,
                    onClose: closeModal,
                    onSuccess: () => {
                        closeModal();
                        if (onEditSuccess) onEditSuccess();
                        else router.reload({ only: [resource.split('/').pop()] });
                    },
                }) : null,
            });
        }
        if (modalState.mode === 'create') {
            tabs.push({
                key: 'create',
                label: `New ${entityName || title}`,
                icon: 'plus-circle',
                content: renderCreateContent ? renderCreateContent({
                    onClose: closeModal,
                    onSuccess: () => {
                        closeModal();
                        if (onCreateSuccess) onCreateSuccess();
                        else router.reload({ only: [resource.split('/').pop()] });
                    },
                }) : null,
            });
        }
        return tabs;
    })() : [];

    return (
        <AdminLayout
            title={title}
            description={description}
            toolbarLeft={filterBar}
            toolbarRight={toolbarRight}
        >
            <Head title={`${title} - Admin`} />
            {view === 'list' ? (
                <AdminDataTable
                    columns={columns}
                    data={data}
                    baseUrl={resource}
                    deleteHandler={canDelete ? deleteRecord : null}
                    selected={selected}
                    onSelect={canDelete ? toggleSelect : null}
                    onSelectAll={canDelete ? toggleSelectAll : null}
                    viewOnly={readOnly && !canDelete}
                    onView={modalCrud ? handleView : null}
                    onEdit={modalCrud ? handleEdit : null}
                />
            ) : (
                <div className="admin-resource-grid">
                    {(data?.data || []).map(item => (
                        <article className="admin-resource-tile" key={item.id}>
                            <h2>{item.title || item.name || `Record ${item.id}`}</h2>
                            {columns.slice(1, 3).map(column => (
                                <p key={column.header}><strong>{column.header}:</strong> {column.render ? column.render(item) : item[column.key]}</p>
                            ))}
                            <div>
                                {modalCrud ? (
                                    <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => handleView(item)}>View</button>
                                ) : (
                                    <Link href={`${resource}/${item.id}`} className="btn btn-sm btn-outline-secondary">View</Link>
                                )}
                                {!readOnly && (modalCrud ? (
                                    <button type="button" className="btn btn-sm btn-outline-primary ms-2" onClick={() => handleEdit(item)}>Edit</button>
                                ) : (
                                    <Link href={`${resource}/${item.id}/edit`} className="btn btn-sm btn-outline-primary ms-2">Edit</Link>
                                ))}
                            </div>
                        </article>
                    ))}
                </div>
            )}
            <Pagination data={data} />

            {modalCrud && (
                <AdminCrudModal
                    show={modalState.show}
                    onClose={closeModal}
                    title={entityName || title}
                    icon="database"
                    activeView={modalState.mode}
                    tabs={modalTabs}
                />
            )}
        </AdminLayout>
    );
}

export { Link };
