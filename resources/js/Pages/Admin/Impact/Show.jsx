import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, Link } from '@inertiajs/react';

Show.layout = page => <AdminLayout title="View Impact Activity">{page}</AdminLayout>;

export default function Show({ impactActivity }) {
    return (
        <>
            <Head title="View Impact Activity - Admin" />
            <div className="d-flex justify-content-between mb-4">
                <h4 className="mb-0">{impactActivity.title}</h4>
                <div>
                    <Link href={`/admin/impact/${impactActivity.id}/edit`} className="btn btn-outline-primary me-2"><i className="bi bi-pencil me-1"></i>Edit</Link>
                    <Link href="/admin/impact" className="btn btn-outline-secondary">Back to List</Link>
                </div>
            </div>
            <div className="content-card">
                <div className="card-body">
                    <div className="row g-3">
                        <div className="col-md-6">
                            <strong>Title:</strong> {impactActivity.title}
                        </div>
                        <div className="col-md-6">
                            <strong>Status:</strong> <span className={`badge bg-${impactActivity.status === 'published' ? 'success' : 'warning'}`}>{impactActivity.status}</span>
                        </div>
                        <div className="col-md-6">
                            <strong>People Affected:</strong> {impactActivity.people_affected?.toLocaleString() || 0}
                        </div>
                        <div className="col-md-6">
                            <strong>Location:</strong> {impactActivity.location || 'N/A'}
                        </div>
                        {impactActivity.activity_date && <div className="col-md-6"><strong>Activity Date:</strong> {new Date(impactActivity.activity_date).toLocaleDateString()}</div>}
                        {impactActivity.metric_type && <div className="col-md-6"><strong>Metric:</strong> {impactActivity.metric_type}: {impactActivity.metric_value}</div>}
                        {impactActivity.description && <div className="col-12"><strong>Description:</strong><p className="mt-1 mb-0">{impactActivity.description}</p></div>}
                        {impactActivity.outcome_summary && <div className="col-12"><strong>Outcome Summary:</strong><p className="mt-1 mb-0">{impactActivity.outcome_summary}</p></div>}
                        {impactActivity.image && <div className="col-12"><strong>Image:</strong><img src={`/uploads/images/${impactActivity.image}`} className="img-fluid mt-2 rounded" style={{maxHeight: '200px'}} alt={impactActivity.title} /></div>}
                    </div>
                </div>
            </div>
        </>
    );
}
