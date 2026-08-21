import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, useForm, Link } from '@inertiajs/react';

Create.layout = page => <AdminLayout title="Create Cause">{page}</AdminLayout>;

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        title: '', description: '', status: 'draft', icon: '', image: '', display_order: 0, quote: ''
    });

    return (
        <>
            <Head title="Create Cause - Admin" />
            <div className="content-card">
                <div className="card-body">
                    <form onSubmit={e => { e.preventDefault(); post('/admin/causes'); }}>
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
                                <label className="form-label">Icon</label>
                                <input type="text" className="form-control" value={data.icon} onChange={e => setData('icon', e.target.value)} placeholder="e.g. globe2" />
                            </div>
                            <div className="col-md-4">
                                <label className="form-label">Image</label>
                                <input type="text" className="form-control" value={data.image} onChange={e => setData('image', e.target.value)} />
                            </div>
                            <div className="col-md-4">
                                <label className="form-label">Display Order</label>
                                <input type="number" className="form-control" value={data.display_order} onChange={e => setData('display_order', e.target.value)} min="0" />
                            </div>
                            <div className="col-12">
                                <label className="form-label">Quote</label>
                                <textarea className="form-control" rows="2" value={data.quote} onChange={e => setData('quote', e.target.value)}></textarea>
                            </div>
                            <div className="col-12">
                                <button type="submit" className="btn btn-primary" disabled={processing}>Create Cause</button>
                                <Link href="/admin/causes" className="btn btn-outline-secondary ms-2">Cancel</Link>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}
