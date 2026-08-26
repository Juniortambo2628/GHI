export default function AdminCrudModalView({ entity, item, fields = [], onEdit, onDelete }) {
    if (!item) return null;

    const imageField = fields.find(f => f.isImage);
    const otherFields = fields.filter(f => !f.isImage);

    return (
        <div className="crud-view">
            {imageField && item[imageField.key] && (
                <div className="crud-view-hero mb-4">
                    <img
                        src={typeof imageField.getSrc === 'function' ? imageField.getSrc(item) : item[imageField.key]}
                        alt={item.title || entity}
                        className="crud-view-hero-img"
                    />
                </div>
            )}
            <div className="d-flex justify-content-between align-items-center mb-3">
                <h5 className="mb-0">{item.title || item.name || `${entity} #${item.id}`}</h5>
                <div>
                    {onEdit && <button type="button" className="btn btn-outline-primary btn-sm crud-btn-edit me-2" onClick={() => onEdit(item)}><i className="bi bi-pencil me-1"></i>Edit</button>}
                    {onDelete && <button type="button" className="btn btn-outline-danger btn-sm crud-btn-delete" onClick={() => onDelete(item.id)}><i className="bi bi-trash me-1"></i>Delete</button>}
                </div>
            </div>
            <div className="content-card">
                <div className="card-body">
                    <div className="row g-3">
                        {otherFields.map((field, idx) => (
                            <div key={idx} className={field.col || 'col-md-6'}>
                                <strong>{field.label}:</strong>{' '}
                                {field.render ? field.render(item) : (item[field.key] || 'N/A')}
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}
