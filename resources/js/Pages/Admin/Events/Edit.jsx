import AdminEntityForm from '../../../Components/Shared/AdminEntityForm';
import { useForm, router } from '@inertiajs/react';
import ImageUploadField from '../../../Components/Shared/ImageUploadField';
import RichTextField from '../../../Components/Shared/RichTextField';
import GalleryUpload from '../../../Components/Shared/GalleryUpload';
import useAutosave from '../../../Hooks/useAutosave';
import { useState, useCallback } from 'react';

export default function Edit({ event, initiatives }) {
    const { data, setData, put, processing } = useForm({
        title: event.title || '', description: event.description || '', content: event.content || '',
        event_date: event.event_date ? event.event_date.split('T')[0] : '', location: event.location || '', initiative_id: event.initiative_id || '',
        image: event.image || '', status: event.status || 'draft'
    });
    const [galleryImages, setGalleryImages] = useState(
        (event.images || []).map(img => ({
            id: img.id,
            path: img.path,
            sort_order: img.sort_order,
        }))
    );

    const autosave = useAutosave({ formKey: `event-edit-${event.id}`, data, enabled: true });

    const submit = useCallback((e) => {
        e.preventDefault();
        put(`/admin/events/${event.id}`, {
            preserveState: true,
            onSuccess: () => {
                autosave.deleteDraft();
                syncGallery(event.id, galleryImages);
            },
        });
    }, [put, event.id, galleryImages, autosave]);

    const syncGallery = (eventId, images) => {
        router.post(`/admin/events/${eventId}/images`, {
            images: images.map((img, idx) => ({
                id: img.id || null,
                path: img.path,
                sort_order: idx,
            })),
        }, { preserveState: true });
    };

    return (
        <AdminEntityForm
            entity="Event"
            mode="edit"
            entityId={event.id}
            data={data}
            setData={setData}
            processing={processing}
            submit={submit}
            breadcrumbs={[{ label: 'Dashboard', href: '/admin' }, { label: 'Events', href: '/admin/events' }, { label: 'Edit' }]}
            formKey={`events-edit-${event.id}`}
            autosave={autosave}
        >
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
                    {initiatives?.map(initiative => (
                        <option key={initiative.id} value={initiative.id}>{initiative.title}</option>
                    ))}
                </select>
            </div>
            <div className="col-md-6">
                <ImageUploadField name="image" value={data.image} onChange={value => setData('image', value)} />
            </div>
            <div className="col-12">
                <label className="form-label fw-semibold">Activity Gallery Images</label>
                <p className="text-muted small mb-2">Upload images from this event. These will appear in the homepage Activity Gallery.</p>
                <GalleryUpload eventId={event.id} eventTitle={event.title} images={galleryImages} onImagesChange={setGalleryImages} />
            </div>
        </AdminEntityForm>
    );
}
