import { statusOptions } from '../../Constants/statusOptions';

export default function AdminForm({ children, onSubmit, processing, submitText = 'Submit', cancelUrl }) {
    return (
        <div className="content-card">
            <div className="card-body">
                <form onSubmit={onSubmit}>
                    <div className="row g-3">
                        {children}
                        <div className="col-12">
                            <button type="submit" className="btn btn-primary" disabled={processing}>{submitText}</button>
                            {cancelUrl && <a href={cancelUrl} className="btn btn-outline-secondary ms-2">Cancel</a>}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    );
}

export function FormField({ label, required, children, className = 'col-md-6' }) {
    return (
        <div className={className}>
            <label className="form-label">{label} {required && '*'}</label>
            {children}
        </div>
    );
}
