import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, useForm, Link } from '@inertiajs/react';

Create.layout = page => <AdminLayout title="Create Event">{page}</AdminLayout>;

export default function Create({ initiatives }) {
    const { data, setData, post, processing, errors } = useForm({
        title: '', description: '', content: '', event_date: '', location: '', initiative_id: '', image: '', status: 'draft'
    });

    return (
        <>
            <Head title="Create Event - Admin" />
            <div className="content-card">
                <div className="card-body">
                    <form onSubmit={e => { e.preventDefault(); post('/admin/events'); }}>
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
                                <button type="submit" className="btn btn-primary" disabled={processing}>Create Event</button>
                                <Link href="/admin/events" className="btn btn-outline-secondary ms-2">Cancel</Link>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}
