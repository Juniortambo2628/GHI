import AdminLayout from '../../Layouts/AdminLayout';
import { Head, Link } from '@inertiajs/react';

export default function AdminEntityForm({
    entity,
    mode,
    entityId,
    data,
    setData,
    processing,
    submit,
    breadcrumbs,
    formKey,
    autosave,
    children,
}) {
    const isEdit = mode === 'edit';
    const title = `${isEdit ? 'Edit' : 'Create'} ${entity}`;
    const submitLabel = `${isEdit ? 'Update' : 'Create'} ${entity}`;
    const cancelUrl = `/admin/${formKey.replace(/-create$|-edit-.*/, '')}`;

    return (
        <AdminLayout
            title={title}
            description={isEdit ? `Edit ${entity.toLowerCase()} details.` : `Add a new ${entity.toLowerCase()}.`}
            breadcrumbs={breadcrumbs}
            onSave={submit}
            saveLabel={submitLabel}
            saveProcessing={processing}
            unsavedChanges={autosave.hasUnsavedChanges}
            saveStatus={autosave.saveStatus}
        >
            <Head title={`${title} - Admin`} />
            <div className="content-card">
                <div className="card-body">
                    <form onSubmit={submit}>
                        <div className="row g-3">
                            <div className="col-md-8">
                                <label className="form-label">Title *</label>
                                <input type="text" className="form-control" value={data.title} onChange={e => setData('title', e.target.value)} required />
                            </div>
                            <div className="col-md-4">
                                <label className="form-label">Status</label>
                                <select className="form-select" value={data.status} onChange={e => setData('status', e.target.value)}>
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                            {children}
                            <div className="col-12">
                                <button type="submit" className="btn btn-primary" disabled={processing}>{submitLabel}</button>
                                <Link href={cancelUrl} className="btn btn-outline-secondary ms-2">Cancel</Link>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </AdminLayout>
    );
}
