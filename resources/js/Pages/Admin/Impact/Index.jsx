import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, Link, router } from '@inertiajs/react';

Index.layout = page => <AdminLayout title="Impact">{page}</AdminLayout>;

export default function Index({ impacts }) {
    const handleDelete = (id) => {
        if (confirm('Delete this impact activity?')) {
            router.delete(`/admin/impact/${id}`);
        }
    };

    return (
        <>
            <Head title="Impact - Admin" />
            <div className="d-flex justify-content-between mb-4">
                <div></div>
                <Link href="/admin/impact/create" className="btn btn-primary"><i className="bi bi-plus-circle me-2"></i>Add Impact</Link>
            </div>
            <div className="content-card">
                <div className="card-body p-0">
                    <table className="table table-hover mb-0">
                        <thead><tr><th>Title</th><th>Date</th><th>People Affected</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            {impacts?.data?.map(impact => (
                                <tr key={impact.id}>
                                    <td>{impact.title}</td>
                                    <td>{impact.activity_date}</td>
                                    <td>{impact.people_affected}</td>
                                    <td><span className={`badge bg-${impact.status === 'published' ? 'success' : 'warning'}`}>{impact.status}</span></td>
                                    <td>
                                        <Link href={`/admin/impact/${impact.id}/edit`} className="btn btn-sm btn-outline-primary me-1"><i className="bi bi-pencil"></i></Link>
                                        <button onClick={() => handleDelete(impact.id)} className="btn btn-sm btn-outline-danger"><i className="bi bi-trash"></i></button>
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
