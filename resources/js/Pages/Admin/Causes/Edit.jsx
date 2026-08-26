import AdminEntityForm from '../../../Components/Shared/AdminEntityForm';
import { useForm } from '@inertiajs/react';
import ImageUploadField from '../../../Components/Shared/ImageUploadField';
import RichTextField from '../../../Components/Shared/RichTextField';
import IconPicker from '../../../Components/Shared/IconPicker';
import useAutosave from '../../../Hooks/useAutosave';

export default function Edit({ cause }) {
    const { data, setData, put, processing } = useForm({
        title: cause.title || '', description: cause.description || '', status: cause.status || 'draft',
        icon: cause.icon || '', image: cause.image || '', display_order: cause.display_order || 0, quote: cause.quote || ''
    });

    const autosave = useAutosave({ formKey: `cause-edit-${cause.id}`, data, enabled: true });

    const submit = e => {
        e.preventDefault();
        put(`/admin/causes/${cause.id}`, { onSuccess: () => autosave.deleteDraft() });
    };

    return (
        <AdminEntityForm
            entity="Cause"
            mode="edit"
            entityId={cause.id}
            data={data}
            setData={setData}
            processing={processing}
            submit={submit}
            breadcrumbs={[{ label: 'Dashboard', href: '/admin' }, { label: 'Causes', href: '/admin/causes' }, { label: 'Edit' }]}
            formKey={`causes-edit-${cause.id}`}
            autosave={autosave}
        >
            <div className="col-12">
                <RichTextField label="Description" value={data.description} onChange={value => setData('description', value)} />
            </div>
            <div className="col-md-4"><IconPicker value={data.icon} onChange={value => setData('icon', value)} /></div>
            <div className="col-md-4"><ImageUploadField name="image" value={data.image} onChange={value => setData('image', value)} /></div>
            <div className="col-md-4"><label className="form-label">Display Order</label><input type="number" className="form-control" value={data.display_order} onChange={e => setData('display_order', e.target.value)} /></div>
            <div className="col-12">
                <label className="form-label">Quote</label>
                <RichTextField label="Quote" value={data.quote} onChange={value => setData('quote', value)} />
            </div>
        </AdminEntityForm>
    );
}
