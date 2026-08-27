import { useContext, useEffect } from 'react';
import { ModalFooterContext } from './AdminCrudModal';

export default function AdminCrudModalForm({ entity, mode, data, setData, processing, errors, onSubmit, onCancel, children }) {
    const isEdit = mode === 'edit';
    const submitLabel = isEdit ? `Update ${entity}` : `Create ${entity}`;

    const setFooterContent = useContext(ModalFooterContext);

    useEffect(() => {
        if (setFooterContent) {
            setFooterContent(
                <>
                    <button type="button" className="btn btn-outline-secondary" onClick={onCancel} data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="crud-form" className="btn btn-primary" disabled={processing}>
                        {processing ? 'Saving...' : submitLabel}
                    </button>
                </>
            );
            return () => setFooterContent(null);
        }
    }, [setFooterContent, onCancel, processing, submitLabel]);

    const handleSubmit = (e) => {
        e.preventDefault();
        onSubmit(e);
    };

    return (
        <form onSubmit={handleSubmit} id="crud-form">
            <div className="content-card">
                <div className="card-body">
                    <div className="row g-3">
                        <div className="col-md-8">
                            <label className="form-label">Title *</label>
                            <input type="text" className={`form-control ${errors?.title ? 'is-invalid' : ''}`} value={data.title || ''} onChange={e => setData('title', e.target.value)} required />
                            {errors?.title && <div className="invalid-feedback">{errors.title}</div>}
                        </div>
                        <div className="col-md-4">
                            <label className="form-label">Status</label>
                            <select className="form-select" value={data.status || 'draft'} onChange={e => setData('status', e.target.value)}>
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                        {children}
                    </div>
                </div>
            </div>
        </form>
    );
}
