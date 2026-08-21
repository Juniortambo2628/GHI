import { statusOptions } from '../../Constants/statusOptions';

export default function StatusDropdown({ value, onChange }) {
    return (
        <div className="col-md-4">
            <label className="form-label">Status</label>
            <select className="form-select" value={value} onChange={e => onChange(e.target.value)}>
                {statusOptions.map(opt => (
                    <option key={opt.value} value={opt.value}>{opt.label}</option>
                ))}
            </select>
        </div>
    );
}
