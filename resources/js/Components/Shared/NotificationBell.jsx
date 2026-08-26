import { useState, useEffect, useRef } from 'react';
import AdminDropdown from './AdminDropdown';

function timeAgo(dateStr) {
    if (!dateStr) return '';
    const diff = Date.now() - new Date(dateStr).getTime();
    const mins = Math.floor(diff / 60000);
    if (mins < 1) return 'Just now';
    if (mins < 60) return `${mins}m ago`;
    const hours = Math.floor(mins / 60);
    if (hours < 24) return `${hours}h ago`;
    const days = Math.floor(hours / 24);
    return `${days}d ago`;
}

const typeIcons = {
    contact: 'bi-envelope',
    subscriber: 'bi-people',
    system: 'bi-info-circle',
};

export default function NotificationBell() {
    const [notifications, setNotifications] = useState([]);
    const [unreadCount, setUnreadCount] = useState(0);
    const [loading, setLoading] = useState(false);
    const intervalRef = useRef(null);

    const fetchNotifications = () => {
        fetch('/admin/notifications', {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(data => {
                setNotifications(data.data || []);
                setUnreadCount(data.unread_count || 0);
            })
            .catch(() => {});
    };

    useEffect(() => {
        fetchNotifications();
        intervalRef.current = setInterval(fetchNotifications, 60000);
        return () => clearInterval(intervalRef.current);
    }, []);

    const markAllRead = () => {
        setLoading(true);
        fetch('/admin/notifications/read-all', {
            method: 'PUT',
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
        })
            .then(() => { fetchNotifications(); setLoading(false); })
            .catch(() => setLoading(false));
    };

    return (
        <AdminDropdown
            trigger={
                <div className="admin-notification-bell">
                    <i className="bi bi-bell"></i>
                    {unreadCount > 0 && <span className="admin-notification-badge">{unreadCount > 99 ? '99+' : unreadCount}</span>}
                </div>
            }
        >
            <div className="admin-notification-header">
                <h6>Notifications</h6>
                {unreadCount > 0 && (
                    <button type="button" className="btn btn-sm btn-outline-primary" onClick={markAllRead} disabled={loading}>
                        Mark all read
                    </button>
                )}
            </div>
            {notifications.length === 0 ? (
                <div className="admin-search-empty">No notifications yet</div>
            ) : (
                notifications.slice(0, 10).map(notif => (
                    <a key={notif.id} href={notif.url || '#'} className={`admin-notification-item ${!notif.read_at ? 'unread' : ''}`}>
                        <div className="notif-icon"><i className={`bi ${typeIcons[notif.type] || 'bi-bell'}`}></i></div>
                        <div className="notif-body">
                            <div className="notif-title">{notif.title}</div>
                            <div className="notif-message">{notif.message}</div>
                            <div className="notif-time">{timeAgo(notif.created_at)}</div>
                        </div>
                    </a>
                ))
            )}
        </AdminDropdown>
    );
}
