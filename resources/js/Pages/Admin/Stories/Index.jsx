import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, Link, router } from '@inertiajs/react';

Index.layout = page => <AdminLayout title="Stories">{page}</AdminLayout>;

export default function Index({ stories }) {
    const handleDelete = (id) => {
        if (confirm('Delete this story?')) {
            router.delete(`/admin/stories/${id}`);
        }
    };

    return (
        <>
            <Head title="Stories - Admin" />
            <div className="d-flex justify-content-between mb-4">
                <div></div>
                <Link href="/admin/stories/create" className="btn btn-primary"><i className="bi bi-plus-circle me-2"></i>Add Story</Link>
            </div>
            <div className="content-card">
                <div className="card-body p-0">
                    <table className="table table-hover mb-0">
                        <thead><tr><th>Title</th><th>Author</th><th>Category</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            {stories?.data?.map(story => (
                                <tr key={story.id}>
                                    <td>{story.title}</td>
                                    <td>{story.author}</td>
                                    <td>{story.category}</td>
                                    <td><span className={`badge bg-${story.status === 'published' ? 'success' : 'warning'}`}>{story.status}</span></td>
                                    <td>
                                        <Link href={`/admin/stories/${story.id}/edit`} className="btn btn-sm btn-outline-primary me-1"><i className="bi bi-pencil"></i></Link>
                                        <button onClick={() => handleDelete(story.id)} className="btn btn-sm btn-outline-danger"><i className="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </>
    );
}
