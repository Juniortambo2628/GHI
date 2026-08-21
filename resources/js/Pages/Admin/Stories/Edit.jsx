import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, useForm, Link } from '@inertiajs/react';

Edit.layout = page => <AdminLayout title="Edit Story">{page}</AdminLayout>;

export default function Edit({ story }) {
    const { data, setData, put, processing } = useForm({
        title: story.title || '', content: story.content || '', author: story.author || '',
        category: story.category || '', image: story.image || '', featured_image: story.featured_image || '',
        status: story.status || 'draft'
    });

    return (
        <>
            <Head title="Edit Story - Admin" />
            <div className="content-card">
                <div className="card-body">
                    <form onSubmit={e => { e.preventDefault(); put(`/admin/stories/${story.id}`); }}>
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
                                <button type="submit" className="btn btn-primary" disabled={processing}>Update Story</button>
                                <Link href="/admin/stories" className="btn btn-outline-secondary ms-2">Cancel</Link>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </>
    );
}
