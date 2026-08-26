import AdminEntityForm from '../../../Components/Shared/AdminEntityForm';
import { useForm } from '@inertiajs/react';
import ImageUploadField from '../../../Components/Shared/ImageUploadField';
import RichTextField from '../../../Components/Shared/RichTextField';
import useAutosave from '../../../Hooks/useAutosave';

export default function Create({ causes }) {
    const { data, setData, post, processing, errors } = useForm({
        title: '', description: '', content: '', category: 'education', cause_ids: [], image: '', status: 'draft'
    });

    const autosave = useAutosave({ formKey: 'initiative-create', data, enabled: true });

    const toggleCause = (causeId) => {
        const current = data.cause_ids || [];
        setData('cause_ids', current.includes(causeId) ? current.filter(id => id !== causeId) : [...current, causeId]);
    };

    const submit = e => {
        e.preventDefault();
        post('/admin/initiatives', { onSuccess: () => autosave.deleteDraft() });
    };

    return (
        <AdminEntityForm
            entity="Initiative"
            mode="create"
            data={data}
            setData={setData}
            processing={processing}
            submit={submit}
            breadcrumbs={[{ label: 'Dashboard', href: '/admin' }, { label: 'Initiatives', href: '/admin/initiatives' }, { label: 'Create' }]}
            formKey="initiatives-create"
            autosave={autosave}
        >
            <div className="col-12">
                <RichTextField label="Description" value={data.description} onChange={value => setData('description', value)} />
            </div>
            <div className="col-12">
                <RichTextField label="Content" value={data.content} onChange={value => setData('content', value)} />
            </div>
            <div className="col-md-6">
                <label className="form-label">Category</label>
                <select className="form-select" value={data.category} onChange={e => setData('category', e.target.value)}>
                    <option value="livelihood">Poverty Alleviation &amp; Livelihoods</option><option value="education">Education Access &amp; Youth Development</option><option value="health">Health &amp; Well-being</option><option value="empowerment">Community Empowerment</option><option value="partnerships">Global Partnerships &amp; Awareness</option>
                </select>
            </div>
            <div className="col-md-6">
                <label className="form-label">Causes</label>
                <div className="border rounded p-2" style={{ maxHeight: '120px', overflowY: 'auto' }}>
                    {causes?.map(cause => (
                        <div key={cause.id} className="form-check">
                            <input className="form-check-input" type="checkbox" id={`cause-${cause.id}`} checked={(data.cause_ids || []).includes(cause.id)} onChange={() => toggleCause(cause.id)} />
                            <label className="form-check-label" htmlFor={`cause-${cause.id}`}>{cause.title}</label>
                        </div>
                    ))}
                </div>
                {errors.cause_ids && <div className="text-danger small mt-1">{errors.cause_ids}</div>}
            </div>
            <div className="col-md-6">
                <ImageUploadField name="image" value={data.image} onChange={value => setData('image', value)} />
            </div>
        </AdminEntityForm>
    );
}
