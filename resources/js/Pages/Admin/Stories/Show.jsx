import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, Link } from '@inertiajs/react';

Show.layout = page => <AdminLayout title="View Story">{page}</AdminLayout>;

export default function Show({ story }) {
    return (
        <>
            <Head title="View Story - Admin" />
            <div className="d-flex justify-content-between mb-4">
                <h4 className="mb-0">{story.title}</h4>
                <div>
                    <Link href={`/admin/stories/${story.id}/edit`} className="btn btn-outline-primary me-2"><i className="bi bi-pencil me-1"></i>Edit</Link>
                    <Link href="/admin/stories" className="btn btn-outline-secondary">Back to List</Link>
                </div>
            </div>
            <div className="content-card">
                <div className="card-body">
                    <div className="row g-3">
                        <div className="col-md-6">
                            <strong>Title:</strong> {story.title}
                        </div>
                        <div className="col-md-6">
                            <strong>Status:</strong> <span className={`badge bg-${story.status === 'published' ? 'success' : 'warning'}`}>{story.status}</span>
                        </div>
                        <div className="col-md-6">
                            <strong>Author:</strong> {story.author || 'N/A'}
                        </div>
                        <div className="col-md-6">
                            <strong>Category:</strong> {story.category || 'N/A'}
                        </div>
                        {story.content && <div className="col-12"><strong>Content:</strong><p className="mt-1 mb-0">{story.content}</p></div>}
                        {story.image && <div className="col-12"><strong>Image:</strong><img src={`/uploads/images/${story.image}`} className="img-fluid mt-2 rounded" style={{maxHeight: '200px'}} alt={story.title} /></div>}
                        {story.featured_image && <div className="col-12"><strong>Featured Image:</strong><img src={`/uploads/images/${story.featured_image}`} className="img-fluid mt-2 rounded" style={{maxHeight: '200px'}} alt={story.title} /></div>}
                    </div>
                </div>
            </div>
        </>
    );
}
