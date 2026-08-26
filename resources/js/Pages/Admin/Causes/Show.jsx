import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, Link } from '@inertiajs/react';
import { mediaUrl } from '../../../Components/Shared/ImageUploadField';
import StatusBadge from '../../../Components/Shared/StatusBadge';

export default function Show({ cause, initiatives }) {
    return (
        <AdminLayout title="Cause Details" breadcrumbs={[{ label: 'Dashboard', href: '/admin' }, { label: 'Causes', href: '/admin/causes' }, { label: 'View' }]}>
            <Head title="Cause Details - Admin" />
            <div className="d-flex justify-content-between mb-4">
                <h4 className="mb-0">{cause.title}</h4>
                <div>
                    <Link href={`/admin/causes/${cause.id}/edit`} className="btn btn-outline-primary me-2"><i className="bi bi-pencil me-1"></i>Edit</Link>
                    <Link href="/admin/causes" className="btn btn-outline-secondary">Back to List</Link>
                </div>
            </div>
            <div className="content-card mb-4">
                <div className="card-body">
                    <div className="row g-3">
                        <div className="col-md-6">
                            <strong>Title:</strong> {cause.title}
                        </div>
                        <div className="col-md-6">
                            <strong>Status:</strong> <StatusBadge status={cause.status} />
                        </div>
                        <div className="col-md-6">
                            <strong>Slug:</strong> {cause.slug}
                        </div>
                        <div className="col-md-6">
                            <strong>Display Order:</strong> {cause.display_order}
                        </div>
                        {cause.icon && <div className="col-md-6"><strong>Icon:</strong> <i className={`bi bi-${cause.icon}`}></i> {cause.icon}</div>}
                        {cause.quote && <div className="col-12"><strong>Quote:</strong> <em>{cause.quote}</em></div>}
                        {cause.description && <div className="col-12"><strong>Description:</strong><p className="mt-1 mb-0">{cause.description}</p></div>}
                        {cause.image && <div className="col-12"><strong>Image:</strong><img src={mediaUrl(cause.image)} className="img-fluid mt-2 rounded admin-media-preview" alt={cause.title} /></div>}
                    </div>
                </div>
            </div>

            {initiatives && initiatives.data && initiatives.data.length > 0 && (
                <div className="content-card">
                    <div className="card-header"><h5 className="mb-0">Related Initiatives</h5></div>
                    <div className="card-body p-0">
                        <table className="table table-hover mb-0">
                            <thead><tr><th>Title</th><th>Category</th><th>Status</th></tr></thead>
                            <tbody>
                                {initiatives.data.map(init => (
                                    <tr key={init.id}>
                                        <td>{init.title}</td>
                                        <td>{init.category}</td>
                                        <td><StatusBadge status={init.status} /></td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            )}
        </AdminLayout>
    );
}
