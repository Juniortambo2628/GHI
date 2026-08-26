import AdminResourceIndex from '../../../Components/Shared/AdminResourceIndex';
import StatusBadge from '../../../Components/Shared/StatusBadge';
import { useForm } from '@inertiajs/react';

const STATUS_OPTIONS = [
    { value: 'new', label: 'New' },
    { value: 'read', label: 'Read' },
    { value: 'replied', label: 'Replied' },
];

const viewFields = [
    { label: 'Name', render: item => `${item.firstname} ${item.lastname}` },
    { label: 'Email', key: 'email' },
    { label: 'Subject', render: item => item.subject || '-' },
    { label: 'Status', render: item => <StatusBadge status={item.status} /> },
    { label: 'Message', col: 'col-12', render: item => <p className="mt-1 mb-0">{item.message}</p> },
];

function ContactView({ item, onClose }) {
    const { data, setData, put, processing } = useForm({ status: item?.status || 'new' });

    if (!item) return null;

    const handleStatusUpdate = (e) => {
        e.preventDefault();
        put(`/admin/contacts/${item.id}`, { onSuccess: onClose });
    };

    return (
        <div>
            <div className="content-card mb-3">
                <div className="card-body">
                    <div className="row g-3">
                        {viewFields.map((field, idx) => (
                            <div key={idx} className={field.col || 'col-md-6'}>
                                <strong>{field.label}:</strong>{' '}
                                {field.render ? field.render(item) : (item[field.key] || 'N/A')}
                            </div>
                        ))}
                    </div>
                </div>
            </div>
            <form onSubmit={handleStatusUpdate} className="d-flex align-items-center gap-2">
                <label className="form-label mb-0 me-2">Status:</label>
                <select className="form-select form-select-sm" style={{ width: 'auto' }} value={data.status} onChange={e => setData('status', e.target.value)}>
                    {STATUS_OPTIONS.map(s => <option key={s.value} value={s.value}>{s.label}</option>)}
                </select>
                <button type="submit" className="btn btn-sm btn-primary" disabled={processing}>Update</button>
            </form>
        </div>
    );
}

export default function Index({ contacts, filters }) {
    return (
        <AdminResourceIndex
            title="Contacts"
            description="Review and manage incoming messages."
            resource="/admin/contacts"
            data={contacts}
            filters={filters}
            filterTypes={['search', 'status']}
            statusOptions={STATUS_OPTIONS}
            columns={[
                { header: 'Name', render: item => `${item.firstname} ${item.lastname}` },
                { header: 'Email', key: 'email' },
                { header: 'Subject', render: item => item.subject || '-' },
                { header: 'Status', render: item => <StatusBadge status={item.status} /> },
            ]}
            canDelete
            modalCrud
            entityName="Contact"
            viewFields={viewFields}
            renderCreateContent={null}
            renderEditContent={({ item, onClose }) => (
                <ContactView item={item} onClose={onClose} />
            )}
        />
    );
}
