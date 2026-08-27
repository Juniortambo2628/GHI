import AdminResourceIndex from '../../../Components/Shared/AdminResourceIndex';
import AdminCrudModalForm from '../../../Components/Shared/AdminCrudModalForm';
import StatusBadge from '../../../Components/Shared/StatusBadge';
import { useForm } from '@inertiajs/react';
import ImageUploadField from '../../../Components/Shared/ImageUploadField';
import RichTextField from '../../../Components/Shared/RichTextField';
import mediaUrl from '../../../Components/Shared/mediaUrl';
import sanitizeHtml from '../../../Components/Shared/sanitizeHtml';
import stripHtml from '../../../Components/Shared/stripHtml';

const viewFields = [
    { label: 'Image', key: 'image', isImage: true, getSrc: item => mediaUrl(item.image), col: 'col-12', render: () => null },
    { label: 'Title', key: 'title' },
    { label: 'Status', render: item => <StatusBadge status={item.status} /> },
    { label: 'Category', key: 'category' },
    { label: 'Causes', render: item => (item.causes || []).map(c => c.title).join(', ') || 'N/A' },
    { label: 'Description', col: 'col-12', render: item => item.description ? <p className="mt-1 mb-0">{stripHtml(item.description)}</p> : 'N/A' },
    { label: 'Content', col: 'col-12', render: item => item.content ? <div className="mt-1" dangerouslySetInnerHTML={{ __html: sanitizeHtml(item.content) }}></div> : 'N/A' },
];

function InitiativeForm({ mode, item, onClose, onSuccess, causes }) {
    const isEdit = mode === 'edit';
    const { data, setData, post, put, processing, errors } = useForm({
        title: item?.title || '', description: item?.description || '', content: item?.content || '',
        category: item?.category || 'education', cause_ids: item?.causes?.map(c => c.id) || [], image: item?.image || '', status: item?.status || 'draft',
    });

    const toggleCause = (causeId) => {
        const current = data.cause_ids || [];
        const next = current.includes(causeId) ? current.filter(id => id !== causeId) : [...current, causeId];
        setData('cause_ids', next);
    };

    const submit = (e) => {
        e.preventDefault();
        if (isEdit) {
            put(`/admin/initiatives/${item.id}`, { onSuccess });
        } else {
            post('/admin/initiatives', { onSuccess });
        }
    };

    return (
        <AdminCrudModalForm entity="Initiative" mode={mode} data={data} setData={setData} processing={processing} errors={errors} onSubmit={submit} onCancel={onClose}>
            <div className="col-12">
                <RichTextField label="Description" value={data.description} onChange={value => setData('description', value)} />
            </div>
            <div className="col-12">
                <RichTextField label="Content" value={data.content} onChange={value => setData('content', value)} />
            </div>
            <div className="col-md-6">
                <label className="form-label">Category</label>
                <select className="form-select" value={data.category} onChange={e => setData('category', e.target.value)}>
                    <option value="livelihood">Poverty Alleviation &amp; Livelihoods</option>
                    <option value="education">Education Access &amp; Youth Development</option>
                    <option value="health">Health &amp; Well-being</option>
                    <option value="empowerment">Community Empowerment</option>
                    <option value="partnerships">Global Partnerships &amp; Awareness</option>
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
        </AdminCrudModalForm>
    );
}

export default function Index({ initiatives, filters, causes, statusOptions, categoryOptions }) {
    return (
        <AdminResourceIndex
            title="Initiatives"
            description="Manage published and planned initiatives."
            resource="/admin/initiatives"
            data={initiatives}
            filters={filters}
            filterTypes={['search', 'status', 'category']}
            statusOptions={statusOptions}
            categoryOptions={categoryOptions}
            createLabel="Add Initiative"
            columns={[
                { header: 'Title', key: 'title' },
                { header: 'Category', key: 'category' },
                { header: 'Status', render: item => <StatusBadge status={item.status} /> },
            ]}
            modalCrud
            entityName="Initiative"
            viewFields={viewFields}
            renderCreateContent={({ onClose, onSuccess }) => (
                <InitiativeForm mode="create" onClose={onClose} onSuccess={onSuccess} causes={causes} />
            )}
            renderEditContent={({ item, onClose, onSuccess }) => (
                <InitiativeForm mode="edit" item={item} onClose={onClose} onSuccess={onSuccess} causes={causes} />
            )}
        />
    );
}
