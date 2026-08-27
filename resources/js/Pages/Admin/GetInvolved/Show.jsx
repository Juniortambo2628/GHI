import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, useForm } from '@inertiajs/react';

const STATUS_OPTIONS = [
    { value: 'new', label: 'New' },
    { value: 'reviewed', label: 'Reviewed' },
    { value: 'contacted', label: 'Contacted' },
    { value: 'closed', label: 'Closed' },
];

export default function Show({ submission }) {
    const { data, setData, put, processing } = useForm({ status: submission.status });

    return (
        <AdminLayout title="Submission Details" breadcrumbs={[{ label: 'Dashboard', href: '/admin' }, { label: 'Get Involved', href: '/admin/get-involved' }, { label: 'View' }]}>
            <Head title="Submission Details - Admin" />
            <div className="content-card">
                <div className="card-body">
                    <h1>{submission.full_name}</h1>
                    <p className="text-muted">{submission.email}</p>
                    {submission.initiative && <p><strong>Initiative:</strong> {submission.initiative.title}</p>}
                    <p>{submission.message}</p>
                    <form onSubmit={event => { event.preventDefault(); put(`/admin/get-involved/${submission.id}`); }} className="d-flex align-items-center gap-2">
                        <label className="form-label mb-0 me-2">Status:</label>
                        <select className="form-select form-select-sm" style={{ width: 'auto' }} value={data.status} onChange={event => setData('status', event.target.value)}>
                            {STATUS_OPTIONS.map(s => <option key={s.value} value={s.value}>{s.label}</option>)}
                        </select>
                        <button className="btn btn-primary" disabled={processing}>Update status</button>
                    </form>
                </div>
            </div>
        </AdminLayout>
    );
}
