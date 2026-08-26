import { Link, usePage } from '@inertiajs/react';
import { RESOURCE_ICONS, RESOURCE_URLS, RESOURCE_LABELS } from '../../Constants/resources';

const CARDS = [
    { key: 'causes', resource: 'causes' },
    { key: 'initiatives', resource: 'initiatives' },
    { key: 'events', resource: 'events' },
    { key: 'stories', resource: 'stories' },
    { key: 'impact', resource: 'impact' },
    { key: 'contacts', resource: 'contacts' },
    { key: 'visitors', resource: 'analytics', label: 'Visitors' },
];

export default function AdminSummaryCards() {
    const { admin_stats } = usePage().props;
    const currentUrl = usePage().url;
    if (!admin_stats) return null;

    return (
        <div className="admin-summary-cards">
            {CARDS.map(card => {
                const url = RESOURCE_URLS[card.resource];
                const isActive = currentUrl === url || currentUrl.startsWith(url + '/');
                return (
                    <Link key={card.key} href={url} className={`admin-summary-card${isActive ? ' active' : ''}`}>
                        <div className="admin-summary-card-icon">
                            <i className={`bi ${RESOURCE_ICONS[card.resource]}`}></i>
                        </div>
                        <div className="admin-summary-card-info">
                            <span className="admin-summary-card-value">{admin_stats[card.key] ?? 0}</span>
                            <span className="admin-summary-card-label">{card.label || RESOURCE_LABELS[card.resource]}</span>
                        </div>
                    </Link>
                );
            })}
        </div>
    );
}
