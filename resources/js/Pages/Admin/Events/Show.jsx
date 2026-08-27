import AdminLayout from '../../../Layouts/AdminLayout';
import sanitizeHtml from '../../../Components/Shared/sanitizeHtml';
import { Head, Link } from '@inertiajs/react';
import mediaUrl from '../../../Components/Shared/mediaUrl';
import StatusBadge from '../../../Components/Shared/StatusBadge';

export default function Show({ event, impactActivities }) {
    return (
        <AdminLayout title="Event Details" breadcrumbs={[{ label: 'Dashboard', href: '/admin' }, { label: 'Events', href: '/admin/events' }, { label: 'View' }]}>
            <Head title="Event Details - Admin" />
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
                            <strong>Status:</strong> <StatusBadge status={event.status} />
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
                        {event.content && <div className="col-12"><strong>Content:</strong><div className="mt-1" dangerouslySetInnerHTML={{ __html: sanitizeHtml(event.content) }}></div></div>}
                        {event.image && <div className="col-12"><strong>Image:</strong><img src={mediaUrl(event.image)} className="img-fluid mt-2 rounded admin-media-preview" alt={event.title} /></div>}
                    </div>
                </div>
            </div>

            {event.images && event.images.length > 0 && (
                <div className="content-card mb-4">
                    <div className="card-header"><h5 className="mb-0"><i className="bi bi-images me-2"></i>Activity Gallery ({event.images.length} media)</h5></div>
                    <div className="card-body">
                        <div className="gallery-grid">
                            {event.images.sort((a, b) => a.sort_order - b.sort_order).map((img, idx) => (
                                <div key={img.id} className="gallery-item-card">
                                    {img.type === 'video' ? (
                                        <video src={mediaUrl(img.path)} muted loop playsInline preload="metadata" />
                                    ) : (
                                        <img src={mediaUrl(img.path)} alt={`Gallery ${idx + 1}`} />
                                    )}
                                    {img.type === 'video' && <div className="gallery-item-video-badge"><i className="bi bi-play-circle"></i></div>}
                                    <div className="gallery-item-counter">{idx + 1}/{event.images.length}</div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            )}

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
                                        <td><StatusBadge status={impact.status} /></td>
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
