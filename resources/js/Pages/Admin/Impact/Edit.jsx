import AdminEntityForm from '../../../Components/Shared/AdminEntityForm';
import { useForm } from '@inertiajs/react';
import ImageUploadField from '../../../Components/Shared/ImageUploadField';
import RichTextField from '../../../Components/Shared/RichTextField';
import useAutosave from '../../../Hooks/useAutosave';

export default function Edit({ impact, events }) {
    const { data, setData, put, processing } = useForm({
        title: impact.title || '', description: impact.description || '', event_id: impact.event_id || '',
        people_affected: impact.people_affected || '', activity_date: impact.activity_date ? impact.activity_date.substring(0, 10) : '',
        location: impact.location || '', outcome_summary: impact.outcome_summary || '',
        image: impact.image || '', status: impact.status || 'draft'
    });

    const autosave = useAutosave({ formKey: `impact-edit-${impact.id}`, data, enabled: true });

    const submit = e => {
        e.preventDefault();
        put(`/admin/impact/${impact.id}`, { onSuccess: () => autosave.deleteDraft() });
    };

    return (
        <AdminEntityForm
            entity="Impact Activity"
            mode="edit"
            entityId={impact.id}
            data={data}
            setData={setData}
            processing={processing}
            submit={submit}
            breadcrumbs={[{ label: 'Dashboard', href: '/admin' }, { label: 'Impact', href: '/admin/impact' }, { label: 'Edit' }]}
            formKey={`impact-edit-${impact.id}`}
            autosave={autosave}
        >
            <div className="col-12">
                <RichTextField label="Description" value={data.description} onChange={value => setData('description', value)} />
            </div>
            <div className="col-md-4">
                <label className="form-label">Event</label>
                <select className="form-select" value={data.event_id} onChange={e => setData('event_id', e.target.value)}>
                    <option value="">-- Select Event --</option>
                    {events?.map(event => (
                        <option key={event.id} value={event.id}>{event.title}</option>
                    ))}
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
        </AdminEntityForm>
    );
}
