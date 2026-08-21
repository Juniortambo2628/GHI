import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, Link, router } from '@inertiajs/react';

Index.layout = page => <AdminLayout title="Initiatives">{page}</AdminLayout>;

export default function Index({ initiatives }) {
    const handleDelete = (id) => {
        if (confirm('Delete this initiative?')) {
            router.delete(`/admin/initiatives/${id}`);
        }
    };

    return (
        <>
            <Head title="Initiatives - Admin" />
            <div className="d-flex justify-content-between mb-4">
                <div></div>
                <Link href="/admin/initiatives/create" className="btn btn-primary"><i className="bi bi-plus-circle me-2"></i>Add Initiative</Link>
            </div>
            <div className="content-card">
                <div className="card-body p-0">
                    <table className="table table-hover mb-0">
                        <thead><tr><th>Title</th><th>Category</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            {initiatives?.data?.map(initiative => (
                                <tr key={initiative.id}>
                                    <td>{initiative.title}</td>
                                    <td>{initiative.category}</td>
                                    <td><span className={`badge bg-${initiative.status === 'published' ? 'success' : 'warning'}`}>{initiative.status}</span></td>
                                    <td>
                                        <Link href={`/admin/initiatives/${initiative.id}/edit`} className="btn btn-sm btn-outline-primary me-1"><i className="bi bi-pencil"></i></Link>
                                        <button onClick={() => handleDelete(initiative.id)} className="btn btn-sm btn-outline-danger"><i className="bi bi-trash"></i></button>
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
