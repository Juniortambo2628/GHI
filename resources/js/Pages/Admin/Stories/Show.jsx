import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, Link } from '@inertiajs/react';
import mediaUrl from '../../../Components/Shared/mediaUrl';
import StatusBadge from '../../../Components/Shared/StatusBadge';

export default function Show({ story }) {
    return (
        <AdminLayout title="Story Details" breadcrumbs={[{ label: 'Dashboard', href: '/admin' }, { label: 'Stories', href: '/admin/stories' }, { label: 'View' }]}>
            <Head title="Story Details - Admin" />
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
                            <strong>Status:</strong> <StatusBadge status={story.status} />
                        </div>
                        <div className="col-md-6">
                            <strong>Author:</strong> {story.author || 'N/A'}
                        </div>
                        <div className="col-md-6">
                            <strong>Category:</strong> {story.category || 'N/A'}
                        </div>
                        {story.content && <div className="col-12"><strong>Content:</strong><p className="mt-1 mb-0">{story.content}</p></div>}
                        {story.image && <div className="col-12"><strong>Image:</strong><img src={mediaUrl(story.image)} className="img-fluid mt-2 rounded admin-media-preview" alt={story.title} /></div>}
                        {story.featured_image && <div className="col-12"><strong>Featured Image:</strong><img src={mediaUrl(story.featured_image)} className="img-fluid mt-2 rounded admin-media-preview" alt={story.title} /></div>}
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
