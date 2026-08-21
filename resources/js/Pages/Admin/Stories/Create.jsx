import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, useForm, Link } from '@inertiajs/react';

Create.layout = page => <AdminLayout title="Create Story">{page}</AdminLayout>;

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        title: '', content: '', author: '', category: '', image: '', featured_image: '', status: 'draft'
    });

    return (
        <>
            <Head title="Create Story - Admin" />
            <div className="content-card">
                <div className="card-body">
                    <form onSubmit={e => { e.preventDefault(); post('/admin/stories'); }}>
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
                                <label className="form-label">Content</label>
                                <textarea className="form-control" rows="5" value={data.content} onChange={e => setData('content', e.target.value)}></textarea>
                            </div>
                            <div className="col-md-4">
                                <label className="form-label">Author</label>
                                <input type="text" className="form-control" value={data.author} onChange={e => setData('author', e.target.value)} />
                            </div>
                            <div className="col-md-4">
                                <label className="form-label">Category</label>
                                <input type="text" className="form-control" value={data.category} onChange={e => setData('category', e.target.value)} />
                            </div>
                            <div className="col-md-4">
                                <label className="form-label">Image</label>
                                <input type="text" className="form-control" value={data.image} onChange={e => setData('image', e.target.value)} />
                            </div>
                            <div className="col-md-6">
                                <label className="form-label">Featured Image</label>
                                <input type="text" className="form-control" value={data.featured_image} onChange={e => setData('featured_image', e.target.value)} />
                            </div>
                            <div className="col-12">
                                <button type="submit" className="btn btn-primary" disabled={processing}>Create Story</button>
                                <Link href="/admin/stories" className="btn btn-outline-secondary ms-2">Cancel</Link>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}
