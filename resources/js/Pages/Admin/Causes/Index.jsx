import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, Link, router } from '@inertiajs/react';

Index.layout = page => <AdminLayout title="Causes">{page}</AdminLayout>;

export default function Index({ causes }) {
    const handleDelete = (id) => {
        if (confirm('Delete this cause?')) {
            router.delete(`/admin/causes/${id}`);
        }
    };

    return (
        <>
            <Head title="Causes - Admin" />
            <div className="d-flex justify-content-between mb-4">
                <div></div>
                <Link href="/admin/causes/create" className="btn btn-primary"><i className="bi bi-plus-circle me-2"></i>Add Cause</Link>
            </div>
            <div className="content-card">
                <div className="card-body p-0">
                    <table className="table table-hover mb-0">
                        <thead><tr><th>Title</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            {causes?.data?.map(cause => (
                                <tr key={cause.id}>
                                    <td>{cause.title}</td>
                                    <td><span className={`badge bg-${cause.status === 'published' ? 'success' : 'warning'}`}>{cause.status}</span></td>
                                    <td>
                                        <Link href={`/admin/causes/${cause.id}/edit`} className="btn btn-sm btn-outline-primary me-1"><i className="bi bi-pencil"></i></Link>
                                        <button onClick={() => handleDelete(cause.id)} className="btn btn-sm btn-outline-danger"><i className="bi bi-trash"></i></button>
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
