import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, useForm, Link } from '@inertiajs/react';

Create.layout = page => <AdminLayout title="Create Impact">{page}</AdminLayout>;

export default function Create({ events }) {
    const { data, setData, post, processing, errors } = useForm({
        title: '', description: '', event_id: '', people_affected: '', activity_date: '', location: '', outcome_summary: '', image: '', status: 'draft'
    });

    return (
        <>
            <Head title="Create Impact - Admin" />
            <div className="content-card">
                <div className="card-body">
                    <form onSubmit={e => { e.preventDefault(); post('/admin/impact'); }}>
                        <div className="row g-3">
                            <div className="col-md-8">
                                <label className="form-label">Title *</label>
                                <input type="text" className="form-control" value={data.title} onChange={e => setData('title', e.target.value)} required />
                            </div>
                            <div className="col-md-4">
                                <label className="form-label">Status</label>
                                <select className="form-select" value={data.status} onChange={e => setData('status', e.target.value)}>
                                    <option value="draft">Draft</option><option value="published">Published</option><option value="archived">Archived</option>
                                </select>
                            </div>
                            <div className="col-12">
                                <label className="form-label">Description</label>
                                <textarea className="form-control" rows="3" value={data.description} onChange={e => setData('description', e.target.value)}></textarea>
                            </div>
                            <div className="col-md-4">
                                <label className="form-label">Event</label>
                                <select className="form-select" value={data.event_id} onChange={e => setData('event_id', e.target.value)}>
                                    <option value="">-- Select Event --</option>
                                    {events?.map(event => (
                                        <option key={event.id} value={event.id}>{event.title}</option>
                                    ))}
                                </select>
                            </div>
                            <div className="col-md-4">
                                <label className="form-label">People Affected</label>
                                <input type="number" className="form-control" value={data.people_affected} onChange={e => setData('people_affected', e.target.value)} min="0" />
                            </div>
                            <div className="col-md-4">
                                <label className="form-label">Activity Date</label>
                                <input type="date" className="form-control" value={data.activity_date} onChange={e => setData('activity_date', e.target.value)} />
                            </div>
                            <div className="col-md-6">
                                <label className="form-label">Location</label>
                                <input type="text" className="form-control" value={data.location} onChange={e => setData('location', e.target.value)} />
                            </div>
                            <div className="col-md-6">
                                <label className="form-label">Image</label>
                                <input type="text" className="form-control" value={data.image} onChange={e => setData('image', e.target.value)} />
                            </div>
                            <div className="col-12">
                                <label className="form-label">Outcome Summary</label>
                                <textarea className="form-control" rows="3" value={data.outcome_summary} onChange={e => setData('outcome_summary', e.target.value)}></textarea>
                            </div>
                            <div className="col-12">
                                <button type="submit" className="btn btn-primary" disabled={processing}>Create Impact</button>
                                <Link href="/admin/impact" className="btn btn-outline-secondary ms-2">Cancel</Link>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}
