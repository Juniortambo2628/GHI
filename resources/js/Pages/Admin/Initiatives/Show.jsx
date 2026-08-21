import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, Link } from '@inertiajs/react';

Show.layout = page => <AdminLayout title="View Initiative">{page}</AdminLayout>;

export default function Show({ initiative, events }) {
    return (
        <>
            <Head title="View Initiative - Admin" />
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
                            <strong>Status:</strong> <span className={`badge bg-${initiative.status === 'published' ? 'success' : 'warning'}`}>{initiative.status}</span>
                        </div>
                        <div className="col-md-6">
                            <strong>Category:</strong> {initiative.category}
                        </div>
                        <div className="col-md-6">
                            <strong>Cause:</strong> {initiative.cause?.title || 'N/A'}
                        </div>
                        {initiative.description && <div className="col-12"><strong>Description:</strong><p className="mt-1 mb-0">{initiative.description}</p></div>}
                        {initiative.content && <div className="col-12"><strong>Content:</strong><div className="mt-1" dangerouslySetInnerHTML={{ __html: initiative.content }}></div></div>}
                        {initiative.image && <div className="col-12"><strong>Image:</strong><img src={`/uploads/images/${initiative.image}`} className="img-fluid mt-2 rounded" style={{maxHeight: '200px'}} alt={initiative.title} /></div>}
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
                                        <td><span className={`badge bg-${event.status === 'published' ? 'success' : 'warning'}`}>{event.status}</span></td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            )}
        </>
    );
}
