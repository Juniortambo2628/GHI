export default function StatusBadge({ status }) {
    const variant = status === 'published' ? 'success' : status === 'archived' ? 'secondary' : 'warning';
    return <span className={`badge bg-${variant}`}>{status}</span>;
}
