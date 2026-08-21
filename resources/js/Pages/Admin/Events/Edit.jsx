import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, useForm, Link } from '@inertiajs/react';

Edit.layout = page => <AdminLayout title="Edit Event">{page}</AdminLayout>;

export default function Edit({ event, initiatives }) {
    const { data, setData, put, processing } = useForm({
        title: event.title || '', description: event.description || '', content: event.content || '',
        event_date: event.event_date || '', location: event.location || '', initiative_id: event.initiative_id || '',
        image: event.image || '', status: event.status || 'draft'
    });

    return (
        <>
            <Head title="Edit Event - Admin" />
            <div className="content-card">
                <div className="card-body">
                    <form onSubmit={e => { e.preventDefault(); put(`/admin/events/${event.id}`); }}>
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
                            <div className="col-12">
                                <label className="form-label">Content</label>
                                <textarea className="form-control" rows="5" value={data.content} onChange={e => setData('content', e.target.value)}></textarea>
                            </div>
                            <div className="col-md-4">
                                <label className="form-label">Event Date</label>
                                <input type="date" className="form-control" value={data.event_date} onChange={e => setData('event_date', e.target.value)} />
                            </div>
                            <div className="col-md-4">
                                <label className="form-label">Location</label>
                                <input type="text" className="form-control" value={data.location} onChange={e => setData('location', e.target.value)} />
                            </div>
                            <div className="col-md-4">
                                <label className="form-label">Initiative</label>
                                <select className="form-select" value={data.initiative_id} onChange={e => setData('initiative_id', e.target.value)}>
                                    <option value="">-- Select Initiative --</option>
                                    {initiatives?.map(initiative => (
                                        <option key={initiative.id} value={initiative.id}>{initiative.title}</option>
                                    ))}
                                </select>
                            </div>
                            <div className="col-md-6">
                                <label className="form-label">Image</label>
                                <input type="text" className="form-control" value={data.image} onChange={e => setData('image', e.target.value)} />
                            </div>
                            <div className="col-12">
                                <button type="submit" className="btn btn-primary" disabled={processing}>Update Event</button>
                                <Link href="/admin/events" className="btn btn-outline-secondary ms-2">Cancel</Link>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}
