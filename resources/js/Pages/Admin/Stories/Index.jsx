import AdminResourceIndex from '../../../Components/Shared/AdminResourceIndex';
import AdminCrudModalForm from '../../../Components/Shared/AdminCrudModalForm';
import StatusBadge from '../../../Components/Shared/StatusBadge';
import { useForm } from '@inertiajs/react';
import ImageUploadField from '../../../Components/Shared/ImageUploadField';
import RichTextField from '../../../Components/Shared/RichTextField';
import { mediaUrl } from '../../../Components/Shared/ImageUploadField';

const stripHtml = (html) => html ? html.replace(/<[^>]+>/g, '') : '';

const viewFields = [
    { label: 'Image', key: 'image', isImage: true, getSrc: item => mediaUrl(item.image), col: 'col-12', render: () => null },
    { label: 'Title', key: 'title' },
    { label: 'Status', render: item => <StatusBadge status={item.status} /> },
    { label: 'Author', key: 'author' },
    { label: 'Category', key: 'category' },
    { label: 'Content', col: 'col-12', render: item => item.content ? <p className="mt-1 mb-0">{stripHtml(item.content)}</p> : 'N/A' },
    { label: 'Featured Image', col: 'col-12', render: item => item.featured_image ? <img src={mediaUrl(item.featured_image)} className="img-fluid mt-2 rounded admin-media-preview" alt={item.title} style={{ maxWidth: '300px' }} /> : 'N/A' },
];

function StoryForm({ mode, item, onClose, onSuccess }) {
    const isEdit = mode === 'edit';
    const { data, setData, post, put, processing, errors } = useForm({
        title: item?.title || '', content: item?.content || '', author: item?.author || '',
        category: item?.category || '', image: item?.image || '', featured_image: item?.featured_image || '',
        status: item?.status || 'draft',
    });

    const submit = (e) => {
        e.preventDefault();
        if (isEdit) {
            put(`/admin/stories/${item.id}`, { onSuccess });
        } else {
            post('/admin/stories', { onSuccess });
        }
    };

    return (
        <AdminCrudModalForm entity="Story" mode={mode} data={data} setData={setData} processing={processing} errors={errors} onSubmit={submit} onCancel={onClose}>
            <div className="col-12">
                <RichTextField label="Content" value={data.content} onChange={value => setData('content', value)} />
            </div>
            <div className="col-md-4">
                <label className="form-label">Author</label>
                <input type="text" className="form-control" value={data.author} onChange={e => setData('author', e.target.value)} />
            </div>
            <div className="col-md-4">
                <label className="form-label">Category</label>
                <input type="text" className="form-control" value={data.category} onChange={e => setData('category', e.target.value)} />
            </div>
            <div className="col-md-4">
                <ImageUploadField name="image" value={data.image} onChange={value => setData('image', value)} />
            </div>
            <div className="col-md-6">
                <ImageUploadField name="featured_image" value={data.featured_image} onChange={value => setData('featured_image', value)} label="Featured Image" />
            </div>
        </AdminCrudModalForm>
    );
}

export default function Index({ stories, filters, statusOptions }) {
    return (
        <AdminResourceIndex
            title="Stories"
            description="Manage stories and public impact updates."
            resource="/admin/stories"
            data={stories}
            filters={filters}
            statusOptions={statusOptions}
            filterTypes={['search', 'status', 'category']}
            createLabel="Add Story"
            columns={[
                { header: 'Title', key: 'title' },
                { header: 'Author', key: 'author' },
                { header: 'Category', key: 'category' },
                { header: 'Status', render: item => <StatusBadge status={item.status} /> },
            ]}
            modalCrud
            entityName="Story"
            viewFields={viewFields}
            renderCreateContent={({ onClose, onSuccess }) => (
                <StoryForm mode="create" onClose={onClose} onSuccess={onSuccess} />
            )}
            renderEditContent={({ item, onClose, onSuccess }) => (
                <StoryForm mode="edit" item={item} onClose={onClose} onSuccess={onSuccess} />
            )}
        />
    );
}
