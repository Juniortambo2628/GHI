import AdminLayout from '../../../Layouts/AdminLayout';
import sanitizeHtml from '../../../Components/Shared/sanitizeHtml';
import { Head, Link } from '@inertiajs/react';
import mediaUrl from '../../../Components/Shared/mediaUrl';
import StatusBadge from '../../../Components/Shared/StatusBadge';

export default function Show({ initiative, events }) {
    return (
        <AdminLayout title="Initiative Details" breadcrumbs={[{ label: 'Dashboard', href: '/admin' }, { label: 'Initiatives', href: '/admin/initiatives' }, { label: 'View' }]}>
            <Head title="Initiative Details - Admin" />
            <div className="d-flex justify-content-between mb-4">
                <h4 className="mb-0">{initiative.title}</h4>
                <div>
                    <Link href={`/admin/initiatives/${initiative.id}/edit`} className="btn btn-outline-primary me-2"><i className="bi bi-pencil me-1"></i>Edit</Link>
                    <Link href="/admin/initiatives" className="btn btn-outline-secondary">Back to List</Link>
                </div>
            </div>
            <div className="content-card mb-4">
                <div className="card-body">
                    <div className="row g-3">
                        <div className="col-md-6">
                            <strong>Title:</strong> {initiative.title}
                        </div>
                        <div className="col-md-6">
                            <strong>Status:</strong> <StatusBadge status={initiative.status} />
                        </div>
                        <div className="col-md-6">
                            <strong>Category:</strong> {initiative.category}
                        </div>
                        <div className="col-md-6">
                            <strong>Cause:</strong> {initiative.cause?.title || 'N/A'}
                        </div>
                        {initiative.description && <div className="col-12"><strong>Description:</strong><p className="mt-1 mb-0">{initiative.description}</p></div>}
                        {initiative.content && <div className="col-12"><strong>Content:</strong><div className="mt-1" dangerouslySetInnerHTML={{ __html: sanitizeHtml(initiative.content) }}></div></div>}
                        {initiative.image && <div className="col-12"><strong>Image:</strong><img src={mediaUrl(initiative.image)} className="img-fluid mt-2 rounded admin-media-preview" alt={initiative.title} /></div>}
                    </div>
                </div>
            </div>

            {events && events.data && events.data.length > 0 && (
                <div className="content-card">
                    <div className="card-header"><h5 className="mb-0">Related Events</h5></div>
                    <div className="card-body p-0">
                        <table className="table table-hover mb-0">
                            <thead><tr><th>Title</th><th>Date</th><th>Location</th><th>Status</th></tr></thead>
                            <tbody>
                                {events.data.map(event => (
                                    <tr key={event.id}>
                                        <td>{event.title}</td>
                                        <td>{new Date(event.event_date).toLocaleDateString()}</td>
                                        <td>{event.location}</td>
                                        <td><StatusBadge status={event.status} /></td>
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
