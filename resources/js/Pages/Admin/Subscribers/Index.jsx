import AdminResourceIndex from '../../../Components/Shared/AdminResourceIndex';
import { useForm } from '@inertiajs/react';

const STATUS_OPTIONS = [
    { value: 'active', label: 'Active' },
    { value: 'unsubscribed', label: 'Unsubscribed' },
];

function StatusControl({ subscriber }) {
    const { data, setData, put, processing } = useForm({ status: subscriber.status });
    return (
        <select
            value={data.status}
            disabled={processing}
            onChange={event => { setData('status', event.target.value); put(`/admin/subscribers/${subscriber.id}`); }}
            aria-label={`Status for ${subscriber.email}`}
        >
            <option value="active">Active</option>
            <option value="unsubscribed">Unsubscribed</option>
        </select>
    );
}

export default function Index({ subscribers, filters }) {
    return (
        <AdminResourceIndex
            title="Subscribers"
            description="Manage the people receiving updates."
            resource="/admin/subscribers"
            data={subscribers}
            filters={filters}
            filterTypes={['search', 'status']}
            statusOptions={STATUS_OPTIONS}
            columns={[
                { header: 'Name', render: item => item.name || '-' },
                { header: 'Email', key: 'email' },
                { header: 'Status', render: item => <StatusControl subscriber={item} /> },
                { header: 'Subscribed', key: 'subscribed_at' },
            ]}
        />
    );
}
