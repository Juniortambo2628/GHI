import { forwardRef } from 'react';

const STATUS_CLASSES = {
    published: 'status-published',
    active: 'status-active',
    completed: 'status-completed',
    replied: 'status-replied',
    draft: 'status-draft',
    archived: 'status-archived',
    pending: 'status-pending',
    new: 'status-new',
    read: 'status-read',
    unsubscribed: 'status-unsubscribed',
};

const StatusBadge = forwardRef(function StatusBadge({ status, className = '' }, ref) {
    const cls = STATUS_CLASSES[status] || 'status-draft';
    return (
        <span ref={ref} className={`status-badge ${cls} ${className}`.trim()}>
            {status}
        </span>
    );
});

export default StatusBadge;
