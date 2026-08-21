import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, Link, router } from '@inertiajs/react';

Index.layout = page => <AdminLayout title="Events">{page}</AdminLayout>;

export default function Index({ events }) {
    const handleDelete = (id) => {
        if (confirm('Delete this event?')) {
            router.delete(`/admin/events/${id}`);
        }
    };

    return (
        <>
            <Head title="Events - Admin" />
            <div className="d-flex justify-content-between mb-4">
                <div></div>
                <Link href="/admin/events/create" className="btn btn-primary"><i className="bi bi-plus-circle me-2"></i>Add Event</Link>
            </div>
            <div className="content-card">
                <div className="card-body p-0">
                    <table className="table table-hover mb-0">
                        <thead><tr><th>Title</th><th>Date</th><th>Location</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            {events?.data?.map(event => (
                                <tr key={event.id}>
                                    <td>{event.title}</td>
                                    <td>{event.event_date}</td>
                                    <td>{event.location}</td>
                                    <td><span className={`badge bg-${event.status === 'published' ? 'success' : 'warning'}`}>{event.status}</span></td>
                                    <td>
                                        <Link href={`/admin/events/${event.id}/edit`} className="btn btn-sm btn-outline-primary me-1"><i className="bi bi-pencil"></i></Link>
                                        <button onClick={() => handleDelete(event.id)} className="btn btn-sm btn-outline-danger"><i className="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </>
    );
}
