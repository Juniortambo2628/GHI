import AdminResourceIndex from '../../../Components/Shared/AdminResourceIndex';
import AdminCrudModalForm from '../../../Components/Shared/AdminCrudModalForm';
import StatusBadge from '../../../Components/Shared/StatusBadge';
import { useForm, router } from '@inertiajs/react';
import ImageUploadField from '../../../Components/Shared/ImageUploadField';
import RichTextField from '../../../Components/Shared/RichTextField';
import GalleryUpload from '../../../Components/Shared/GalleryUpload';
import mediaUrl from '../../../Components/Shared/mediaUrl';
import sanitizeHtml from '../../../Components/Shared/sanitizeHtml';
import stripHtml from '../../../Components/Shared/stripHtml';
import { useState, useCallback, useRef, useEffect } from 'react';

const viewFields = [
    { label: 'Image', key: 'image', isImage: true, getSrc: item => mediaUrl(item.image), col: 'col-12', render: () => null },
    { label: 'Title', key: 'title' },
    { label: 'Status', render: item => <StatusBadge status={item.status} /> },
    { label: 'Date', render: item => item.event_date ? new Date(item.event_date).toLocaleString() : 'N/A' },
    { label: 'Location', key: 'location' },
    { label: 'Initiative', render: item => item.initiative?.title || 'N/A' },
    { label: 'Description', col: 'col-12', render: item => item.description ? <p className="mt-1 mb-0">{stripHtml(item.description)}</p> : 'N/A' },
    { label: 'Content', col: 'col-12', render: item => item.content ? <div className="mt-1" dangerouslySetInnerHTML={{ __html: sanitizeHtml(item.content) }}></div> : 'N/A' },
];

function EventForm({ mode, item, onClose, onSuccess, initiatives }) {
    const isEdit = mode === 'edit';

    const { data, setData, post, put, processing, errors } = useForm({
        title: item?.title || '', description: item?.description || '', content: item?.content || '',
        event_date: item?.event_date ? item.event_date.split('T')[0] : '', location: item?.location || '',
        initiative_id: item?.initiative_id || '', image: item?.image || '', status: item?.status || 'draft',
        images: isEdit && item?.images ? item.images.map(img => ({ id: img.id, path: img.path, sort_order: img.sort_order, type: img.type || 'image' })) : []
    });

    const submit = useCallback((e) => {
        e.preventDefault();
        if (isEdit) {
            put(`/admin/events/${item.id}`, {
                onSuccess: () => onSuccess(),
            });
        } else {
            post('/admin/events', {
                onSuccess: () => onSuccess(),
            });
        }
    }, [post, put, isEdit, item, onSuccess]);

    return (
        <AdminCrudModalForm entity="Event" mode={mode} data={data} setData={setData} processing={processing} errors={errors} onSubmit={submit} onCancel={onClose}>
            <div className="col-12">
                <RichTextField label="Description" value={data.description} onChange={value => setData('description', value)} />
            </div>
            <div className="col-12">
                <RichTextField label="Content" value={data.content} onChange={value => setData('content', value)} />
            </div>
            <div className="col-md-4">
                <label className="form-label">Event Date</label>
                <input type="date" className="form-control" value={data.event_date} onChange={e => setData('event_date', e.target.value)} />
            </div>
            <div className="col-md-4">
                <label className="form-label">Location</label>
                <input type="text" className="form-control" value={data.location} onChange={e => setData('location', e.target.value)} />
            </div>
            <div className="col-md-4">
                <label className="form-label">Initiative</label>
                <select className="form-select" value={data.initiative_id} onChange={e => setData('initiative_id', e.target.value)}>
                    <option value="">-- Select Initiative --</option>
                    {initiatives?.map(initiative => <option key={initiative.id} value={initiative.id}>{initiative.title}</option>)}
                </select>
            </div>
            <div className="col-md-6">
                <ImageUploadField name="image" value={data.image} onChange={value => setData('image', value)} />
            </div>
            <div className="col-12">
                <label className="form-label fw-semibold">Activity Gallery Images</label>
                <p className="text-muted small mb-2">Upload images from this event.</p>
                <GalleryUpload eventId={isEdit ? item?.id : null} images={data.images} onImagesChange={imgs => setData('images', imgs)} />
            </div>
        </AdminCrudModalForm>
    );
}

export default function Index({ events, filters, initiatives, statusOptions }) {
    return (
        <AdminResourceIndex
            title="Events"
            description="Manage upcoming and completed events."
            resource="/admin/events"
            data={events}
            filters={filters}
            statusOptions={statusOptions}
            filterTypes={['search', 'status', 'dates']}
            createLabel="Add Event"
            columns={[
                { header: 'Title', key: 'title' },
                { header: 'Date', render: item => item.event_date ? new Date(item.event_date).toLocaleDateString() : 'N/A' },
                { header: 'Location', key: 'location' },
                { header: 'Status', render: item => <StatusBadge status={item.status} /> },
            ]}
            modalCrud
            entityName="Event"
            viewFields={viewFields}
            renderCreateContent={({ onClose, onSuccess }) => (
                <EventForm mode="create" onClose={onClose} onSuccess={onSuccess} initiatives={initiatives} />
            )}
            renderEditContent={({ item, onClose, onSuccess }) => (
                <EventForm mode="edit" item={item} onClose={onClose} onSuccess={onSuccess} initiatives={initiatives} />
            )}
        />
    );
}
