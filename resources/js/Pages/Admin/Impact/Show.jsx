import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, Link } from '@inertiajs/react';
import mediaUrl from '../../../Components/Shared/mediaUrl';
import StatusBadge from '../../../Components/Shared/StatusBadge';

export default function Show({ impactActivity }) {
    return (
        <AdminLayout title="Impact Details" breadcrumbs={[{ label: 'Dashboard', href: '/admin' }, { label: 'Impact', href: '/admin/impact' }, { label: 'View' }]}>
            <Head title="Impact Details - Admin" />
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
                            <strong>Status:</strong> <StatusBadge status={impactActivity.status} />
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
                        {impactActivity.image && <div className="col-12"><strong>Image:</strong><img src={mediaUrl(impactActivity.image)} className="img-fluid mt-2 rounded admin-media-preview" alt={impactActivity.title} /></div>}
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
