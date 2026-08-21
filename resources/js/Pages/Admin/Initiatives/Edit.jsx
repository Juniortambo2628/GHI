import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, useForm, Link } from '@inertiajs/react';

Edit.layout = page => <AdminLayout title="Edit Initiative">{page}</AdminLayout>;

export default function Edit({ initiative, causes }) {
    const { data, setData, put, processing } = useForm({
        title: initiative.title || '', description: initiative.description || '', content: initiative.content || '',
        category: initiative.category || 'education', cause_id: initiative.cause_id || '', image: initiative.image || '', status: initiative.status || 'draft'
    });

    return (
        <>
            <Head title="Edit Initiative - Admin" />
            <div className="content-card">
                <div className="card-body">
                    <form onSubmit={e => { e.preventDefault(); put(`/admin/initiatives/${initiative.id}`); }}>
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
                            <div className="col-md-6">
                                <label className="form-label">Category</label>
                                <select className="form-select" value={data.category} onChange={e => setData('category', e.target.value)}>
                                    <option value="livelihood">Poverty Alleviation &amp; Livelihoods</option><option value="education">Education Access &amp; Youth Development</option><option value="health">Health &amp; Well-being</option><option value="empowerment">Community Empowerment</option><option value="partnerships">Global Partnerships &amp; Awareness</option>
                                </select>
                            </div>
                            <div className="col-md-6">
                                <label className="form-label">Cause</label>
                                <select className="form-select" value={data.cause_id} onChange={e => setData('cause_id', e.target.value)}>
                                    <option value="">-- Select Cause --</option>
                                    {causes?.map(cause => (
                                        <option key={cause.id} value={cause.id}>{cause.title}</option>
                                    ))}
                                </select>
                            </div>
                            <div className="col-md-6">
                                <label className="form-label">Image</label>
                                <input type="text" className="form-control" value={data.image} onChange={e => setData('image', e.target.value)} />
                            </div>
                            <div className="col-12">
                                <button type="submit" className="btn btn-primary" disabled={processing}>Update Initiative</button>
                                <Link href="/admin/initiatives" className="btn btn-outline-secondary ms-2">Cancel</Link>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}
