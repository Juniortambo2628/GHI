import AdminResourceIndex from '../../../Components/Shared/AdminResourceIndex';
import StatusBadge from '../../../Components/Shared/StatusBadge';

const STATUS_OPTIONS = [
    { value: 'new', label: 'New' },
    { value: 'reviewed', label: 'Reviewed' },
    { value: 'contacted', label: 'Contacted' },
    { value: 'closed', label: 'Closed' },
];

const viewFields = [
    { label: 'Full Name', key: 'full_name' },
    { label: 'Email', key: 'email' },
    { label: 'Initiative', render: item => item.initiative?.title || '-' },
    { label: 'Status', render: item => <StatusBadge status={item.status} /> },
    { label: 'Message', col: 'col-12', render: item => <p className="mt-1 mb-0">{item.message}</p> },
];

export default function Index({ submissions, filters }) {
    return (
        <AdminResourceIndex
            title="Get Involved"
            description="Review and manage get involved submissions."
            resource="/admin/get-involved"
            data={submissions}
            filters={filters}
            filterTypes={['search', 'status']}
            statusOptions={STATUS_OPTIONS}
            columns={[
                { header: 'Full Name', key: 'full_name' },
                { header: 'Email', key: 'email' },
                { header: 'Initiative', render: item => item.initiative?.title || '-' },
                { header: 'Status', render: item => <StatusBadge status={item.status} /> },
                { header: 'Date', render: item => new Date(item.created_at).toLocaleDateString() },
            ]}
            canDelete
            modalCrud
            entityName="Submission"
            viewFields={viewFields}
            renderCreateContent={null}
            renderEditContent={null}
        />
    );
}
