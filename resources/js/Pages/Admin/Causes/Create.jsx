import AdminEntityForm from '../../../Components/Shared/AdminEntityForm';
import { useForm } from '@inertiajs/react';
import ImageUploadField from '../../../Components/Shared/ImageUploadField';
import RichTextField from '../../../Components/Shared/RichTextField';
import IconPicker from '../../../Components/Shared/IconPicker';
import useAutosave from '../../../Hooks/useAutosave';

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        title: '', description: '', status: 'draft', icon: '', image: '', display_order: 0, quote: ''
    });

    const autosave = useAutosave({ formKey: 'cause-create', data, enabled: true });

    const submit = e => {
        e.preventDefault();
        post('/admin/causes', { onSuccess: () => autosave.deleteDraft() });
    };

    return (
        <AdminEntityForm
            entity="Cause"
            mode="create"
            data={data}
            setData={setData}
            processing={processing}
            submit={submit}
            breadcrumbs={[{ label: 'Dashboard', href: '/admin' }, { label: 'Causes', href: '/admin/causes' }, { label: 'Create' }]}
            formKey="causes-create"
            autosave={autosave}
        >
            <div className="col-12">
                <RichTextField label="Description" value={data.description} onChange={value => setData('description', value)} />
            </div>
            <div className="col-md-4">
                <IconPicker value={data.icon} onChange={value => setData('icon', value)} />
            </div>
            <div className="col-md-4">
                <ImageUploadField name="image" value={data.image} onChange={value => setData('image', value)} />
            </div>
            <div className="col-md-4">
                <label className="form-label">Display Order</label>
                <input type="number" className="form-control" value={data.display_order} onChange={e => setData('display_order', e.target.value)} min="0" />
            </div>
            <div className="col-12">
                <label className="form-label">Quote</label>
                <RichTextField label="Quote" value={data.quote} onChange={value => setData('quote', value)} />
            </div>
        </AdminEntityForm>
    );
}
