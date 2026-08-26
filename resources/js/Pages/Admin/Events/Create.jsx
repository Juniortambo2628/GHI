import AdminEntityForm from '../../../Components/Shared/AdminEntityForm';
import { useForm, router } from '@inertiajs/react';
import ImageUploadField from '../../../Components/Shared/ImageUploadField';
import RichTextField from '../../../Components/Shared/RichTextField';
import GalleryUpload from '../../../Components/Shared/GalleryUpload';
import useAutosave from '../../../Hooks/useAutosave';
import { useState, useCallback } from 'react';

export default function Create({ initiatives }) {
    const { data, setData, post, processing, errors } = useForm({
        title: '', description: '', content: '', event_date: '', location: '', initiative_id: '', image: '', status: 'draft'
    });
    const [galleryImages, setGalleryImages] = useState([]);

    const autosave = useAutosave({ formKey: 'event-create', data, enabled: true });

    const submit = useCallback((e) => {
        e.preventDefault();
        post('/admin/events', {
            preserveState: true,
            onSuccess: (page) => {
                autosave.deleteDraft();
                const eventId = page.props.event?.id;
                if (eventId && galleryImages.length > 0) {
                    syncGallery(eventId, galleryImages);
                }
            },
        });
    }, [post, galleryImages, autosave]);

    const syncGallery = (eventId, images) => {
        router.post(`/admin/events/${eventId}/images`, {
            images: images.map((img, idx) => ({ path: img.path, sort_order: idx })),
        }, { preserveState: true });
    };

    return (
        <AdminEntityForm
            entity="Event"
            mode="create"
            data={data}
            setData={setData}
            processing={processing}
            submit={submit}
            breadcrumbs={[{ label: 'Dashboard', href: '/admin' }, { label: 'Events', href: '/admin/events' }, { label: 'Create' }]}
            formKey="events-create"
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
                <GalleryUpload eventId={null} images={galleryImages} onImagesChange={setGalleryImages} />
            </div>
        </AdminEntityForm>
    );
}
