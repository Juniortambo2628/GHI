import AdminEntityForm from '../../../Components/Shared/AdminEntityForm';
import { useForm } from '@inertiajs/react';
import ImageUploadField from '../../../Components/Shared/ImageUploadField';
import RichTextField from '../../../Components/Shared/RichTextField';
import useAutosave from '../../../Hooks/useAutosave';

export default function Create({ events = [] }) {
    const { data, setData, post, processing, errors } = useForm({
        title: '', content: '', author: '', category: '', image: '', featured_image: '', status: 'draft', event_id: ''
    });

    const autosave = useAutosave({ formKey: 'story-create', data, enabled: true });

    const submit = e => {
        e.preventDefault();
        post('/admin/stories', { onSuccess: () => autosave.deleteDraft() });
    };

    return (
        <AdminEntityForm
            entity="Story"
            mode="create"
            data={data}
            setData={setData}
            processing={processing}
            submit={submit}
            breadcrumbs={[{ label: 'Dashboard', href: '/admin' }, { label: 'Stories', href: '/admin/stories' }, { label: 'Create' }]}
            formKey="stories-create"
            autosave={autosave}
        >
            <div className="col-12"><RichTextField label="Content" value={data.content} onChange={value => setData('content', value)} /></div>
            <div className="col-md-4">
                <label className="form-label">Author</label>
                <input type="text" className="form-control" value={data.author} onChange={e => setData('author', e.target.value)} />
            </div>
            <div className="col-md-4">
                <label className="form-label">Category</label>
                <input type="text" className="form-control" value={data.category} onChange={e => setData('category', e.target.value)} />
            </div>
            <div className="col-md-4">
                <label className="form-label">Related Event</label>
                <select className="form-select" value={data.event_id} onChange={e => setData('event_id', e.target.value)}>
                    <option value="">None</option>
                    {events.map(event => (
                        <option key={event.id} value={event.id}>{event.title}</option>
                    ))}
                </select>
            </div>
            <div className="col-md-4"><ImageUploadField name="image" value={data.image} onChange={value => setData('image', value)} /></div>
            <div className="col-md-6"><ImageUploadField name="featured_image" value={data.featured_image} onChange={value => setData('featured_image', value)} label="Featured Image" /></div>
        </AdminEntityForm>
    );
}
