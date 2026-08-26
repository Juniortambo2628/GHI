import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, useForm } from '@inertiajs/react';

export default function Show({ contact }) {
    const { data, setData, put, processing } = useForm({ status: contact.status });

    return (
        <AdminLayout title="Contact Details" breadcrumbs={[{ label: 'Dashboard', href: '/admin' }, { label: 'Contacts', href: '/admin/contacts' }, { label: 'View' }]}>
            <Head title="Contact Details - Admin" />
            <div className="content-card">
                <div className="card-body">
                    <h1>{contact.subject || 'Contact message'}</h1>
                    <p>{contact.firstname} {contact.lastname} · {contact.email}</p>
                    <p>{contact.message}</p>
                    <form onSubmit={event => { event.preventDefault(); put(`/admin/contacts/${contact.id}`); }}>
                        <select value={data.status} onChange={event => setData('status', event.target.value)}>
                            <option>new</option>
                            <option>read</option>
                            <option>replied</option>
                            <option>archived</option>
                        </select>
                        <button className="btn btn-primary" disabled={processing}>Update status</button>
                    </form>
                </div>
            </div>
        </AdminLayout>
    );
}
