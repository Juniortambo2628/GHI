import AdminResourceIndex from '../../../Components/Shared/AdminResourceIndex';
import AdminCrudModalForm from '../../../Components/Shared/AdminCrudModalForm';
import StatusBadge from '../../../Components/Shared/StatusBadge';
import { useForm } from '@inertiajs/react';
import ImageUploadField from '../../../Components/Shared/ImageUploadField';
import RichTextField from '../../../Components/Shared/RichTextField';
import IconPicker from '../../../Components/Shared/IconPicker';
import mediaUrl from '../../../Components/Shared/mediaUrl';
import stripHtml from '../../../Components/Shared/stripHtml';

const viewFields = [
    { label: 'Image', key: 'image', isImage: true, getSrc: item => mediaUrl(item.image), col: 'col-12', render: () => null },
    { label: 'Title', key: 'title' },
    { label: 'Status', render: item => <StatusBadge status={item.status} /> },
    { label: 'Slug', key: 'slug' },
    { label: 'Display Order', key: 'display_order' },
    { label: 'Icon', render: item => item.icon ? <><i className={`bi bi-${item.icon}`}></i> {item.icon}</> : 'N/A' },
    { label: 'Quote', col: 'col-12', render: item => item.quote ? <em>{stripHtml(item.quote)}</em> : 'N/A' },
    { label: 'Description', col: 'col-12', render: item => item.description ? <p className="mt-1 mb-0">{stripHtml(item.description)}</p> : 'N/A' },
];

function CauseForm({ mode, item, onClose, onSuccess }) {
    const isEdit = mode === 'edit';
    const { data, setData, post, put, processing, errors } = useForm({
        title: item?.title || '', description: item?.description || '', status: item?.status || 'draft',
        icon: item?.icon || '', image: item?.image || '', display_order: item?.display_order || 0, quote: item?.quote || '',
    });

    const submit = (e) => {
        e.preventDefault();
        if (isEdit) {
            put(`/admin/causes/${item.id}`, { onSuccess });
        } else {
            post('/admin/causes', { onSuccess });
        }
    };

    return (
        <AdminCrudModalForm
            entity="Cause"
            mode={mode}
            data={data}
            setData={setData}
            processing={processing}
            errors={errors}
            onSubmit={submit}
            onCancel={onClose}
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
        </AdminCrudModalForm>
    );
}

export default function Index({ causes, filters, statusOptions }) {
    return (
        <AdminResourceIndex
            title="Causes"
            description="Manage the organization's public causes."
            resource="/admin/causes"
            data={causes}
            filters={filters}
            statusOptions={statusOptions}
            createLabel="Add Cause"
            columns={[
                { header: 'Title', key: 'title' },
                { header: 'Status', render: item => <StatusBadge status={item.status} /> },
            ]}
            modalCrud
            entityName="Cause"
            viewFields={viewFields}
            renderCreateContent={({ onClose, onSuccess }) => (
                <CauseForm mode="create" onClose={onClose} onSuccess={onSuccess} />
            )}
            renderEditContent={({ item, onClose, onSuccess }) => (
                <CauseForm mode="edit" item={item} onClose={onClose} onSuccess={onSuccess} />
            )}
        />
    );
}
