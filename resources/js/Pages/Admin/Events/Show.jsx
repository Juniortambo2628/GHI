import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, Link } from '@inertiajs/react';

Show.layout = page => <AdminLayout title="View Event">{page}</AdminLayout>;

export default function Show({ event, impactActivities }) {
    return (
        <>
            <Head title="View Event - Admin" />
            <div className="d-flex justify-content-between mb-4">
                <h4 className="mb-0">{event.title}</h4>
                <div>
                    <Link href={`/admin/events/${event.id}/edit`} className="btn btn-outline-primary me-2"><i className="bi bi-pencil me-1"></i>Edit</Link>
                    <Link href="/admin/events" className="btn btn-outline-secondary">Back to List</Link>
                </div>
            </div>
            <div className="content-card mb-4">
                <div className="card-body">
                    <div className="row g-3">
                        <div className="col-md-6">
                            <strong>Title:</strong> {event.title}
                        </div>
                        <div className="col-md-6">
                            <strong>Status:</strong> <span className={`badge bg-${event.status === 'published' ? 'success' : 'warning'}`}>{event.status}</span>
                        </div>
                        <div className="col-md-6">
                            <strong>Date:</strong> {new Date(event.event_date).toLocaleString()}
                        </div>
                        <div className="col-md-6">
                            <strong>Location:</strong> {event.location || 'N/A'}
                        </div>
                        <div className="col-md-6">
                            <strong>Initiative:</strong> {event.initiative?.title || 'N/A'}
                        </div>
                        {event.description && <div className="col-12"><strong>Description:</strong><p className="mt-1 mb-0">{event.description}</p></div>}
                        {event.content && <div className="col-12"><strong>Content:</strong><div className="mt-1" dangerouslySetInnerHTML={{ __html: event.content }}></div></div>}
                        {event.image && <div className="col-12"><strong>Image:</strong><img src={`/uploads/images/${event.image}`} className="img-fluid mt-2 rounded" style={{maxHeight: '200px'}} alt={event.title} /></div>}
                    </div>
                </div>
            </div>

            {impactActivities && impactActivities.data && impactActivities.data.length > 0 && (
                <div className="content-card">
                    <div className="card-header"><h5 className="mb-0">Impact Activities</h5></div>
                    <div className="card-body p-0">
                        <table className="table table-hover mb-0">
                            <thead><tr><th>Title</th><th>People Affected</th><th>Status</th></tr></thead>
                            <tbody>
                                {impactActivities.data.map(impact => (
                                    <tr key={impact.id}>
                                        <td>{impact.title}</td>
                                        <td>{impact.people_affected}</td>
                                        <td><span className={`badge bg-${impact.status === 'published' ? 'success' : 'warning'}`}>{impact.status}</span></td>
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
