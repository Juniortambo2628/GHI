import AdminResourceIndex from '../../../Components/Shared/AdminResourceIndex';
import AdminCrudModalForm from '../../../Components/Shared/AdminCrudModalForm';
import StatusBadge from '../../../Components/Shared/StatusBadge';
import { useForm } from '@inertiajs/react';
import ImageUploadField from '../../../Components/Shared/ImageUploadField';
import RichTextField from '../../../Components/Shared/RichTextField';
import { mediaUrl } from '../../../Components/Shared/ImageUploadField';

const stripHtml = (html) => html ? html.replace(/<[^>]+>/g, '') : '';

const viewFields = [
    { label: 'Image', key: 'image', isImage: true, getSrc: item => mediaUrl(item.image), col: 'col-12', render: () => null },
    { label: 'Title', key: 'title' },
    { label: 'Status', render: item => <StatusBadge status={item.status} /> },
    { label: 'People Affected', render: item => item.people_affected?.toLocaleString() || 0 },
    { label: 'Location', key: 'location' },
    { label: 'Activity Date', render: item => item.activity_date ? new Date(item.activity_date).toLocaleDateString() : 'N/A' },
    { label: 'Description', col: 'col-12', render: item => item.description ? <p className="mt-1 mb-0">{stripHtml(item.description)}</p> : 'N/A' },
    { label: 'Outcome Summary', col: 'col-12', render: item => item.outcome_summary ? <p className="mt-1 mb-0">{stripHtml(item.outcome_summary)}</p> : 'N/A' },
];

function ImpactForm({ mode, item, onClose, onSuccess, events }) {
    const isEdit = mode === 'edit';
    const { data, setData, post, put, processing, errors } = useForm({
        title: item?.title || '', description: item?.description || '', event_id: item?.event_id || '',
        people_affected: item?.people_affected || '', activity_date: item?.activity_date || '',
        location: item?.location || '', outcome_summary: item?.outcome_summary || '',
        image: item?.image || '', status: item?.status || 'draft',
    });

    const submit = (e) => {
        e.preventDefault();
        if (isEdit) {
            put(`/admin/impact/${item.id}`, { onSuccess });
        } else {
            post('/admin/impact', { onSuccess });
        }
    };

    return (
        <AdminCrudModalForm entity="Impact Activity" mode={mode} data={data} setData={setData} processing={processing} errors={errors} onSubmit={submit} onCancel={onClose}>
            <div className="col-12">
                <RichTextField label="Description" value={data.description} onChange={value => setData('description', value)} />
            </div>
            <div className="col-md-4">
                <label className="form-label">Event</label>
                <select className="form-select" value={data.event_id} onChange={e => setData('event_id', e.target.value)}>
                    <option value="">-- Select Event --</option>
                    {events?.map(event => <option key={event.id} value={event.id}>{event.title}</option>)}
                </select>
            </div>
            <div className="col-md-4">
                <label className="form-label">People Affected</label>
                <input type="number" className="form-control" value={data.people_affected} onChange={e => setData('people_affected', e.target.value)} min="0" />
            </div>
            <div className="col-md-4">
                <label className="form-label">Activity Date</label>
                <input type="date" className="form-control" value={data.activity_date} onChange={e => setData('activity_date', e.target.value)} />
            </div>
            <div className="col-md-6">
                <label className="form-label">Location</label>
                <input type="text" className="form-control" value={data.location} onChange={e => setData('location', e.target.value)} />
            </div>
            <div className="col-md-6">
                <ImageUploadField name="image" value={data.image} onChange={value => setData('image', value)} />
            </div>
            <div className="col-12">
                <label className="form-label">Outcome Summary</label>
                <RichTextField label="Outcome Summary" value={data.outcome_summary} onChange={value => setData('outcome_summary', value)} />
            </div>
        </AdminCrudModalForm>
    );
}

export default function Index({ impacts, filters, events, statusOptions }) {
    return (
        <AdminResourceIndex
            title="Impact"
            description="Track measurable community outcomes."
            resource="/admin/impact"
            data={impacts}
            filters={filters}
            statusOptions={statusOptions}
            filterTypes={['search', 'status', 'dates']}
            createLabel="Add Impact"
            columns={[
                { header: 'Title', key: 'title' },
                { header: 'Date', key: 'activity_date' },
                { header: 'People Affected', key: 'people_affected' },
                { header: 'Status', render: item => <StatusBadge status={item.status} /> },
            ]}
            modalCrud
            entityName="Impact Activity"
            viewFields={viewFields}
            renderCreateContent={({ onClose, onSuccess }) => (
                <ImpactForm mode="create" onClose={onClose} onSuccess={onSuccess} events={events} />
            )}
            renderEditContent={({ item, onClose, onSuccess }) => (
                <ImpactForm mode="edit" item={item} onClose={onClose} onSuccess={onSuccess} events={events} />
            )}
        />
    );
}
