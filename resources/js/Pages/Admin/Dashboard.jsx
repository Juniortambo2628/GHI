import AdminLayout from '../../Layouts/AdminLayout';
import { Head, Link } from '@inertiajs/react';

Dashboard.layout = page => <AdminLayout title="Dashboard">{page}</AdminLayout>;

export default function Dashboard({ stats, recentContacts }) {
    const statCards = [
        { key: 'causes', label: 'Causes', icon: 'bi-heart', color: '#e83e8c', url: '/admin/causes' },
        { key: 'initiatives', label: 'Initiatives', icon: 'bi-lightbulb', color: '#6f42c1', url: '/admin/initiatives' },
        { key: 'events', label: 'Events', icon: 'bi-calendar-event', color: '#007bff', url: '/admin/events' },
        { key: 'impact', label: 'Impact Activities', icon: 'bi-graph-up', color: '#28a745', url: '/admin/impact' },
        { key: 'contacts', label: 'New Contacts', icon: 'bi-envelope', color: '#fd7e14', url: '#' },
        { key: 'subscribers', label: 'Subscribers', icon: 'bi-mailbox', color: '#17a2b8', url: '#' },
    ];

    return (
        <>
            <Head title="Dashboard - Admin" />
            <div className="row g-4 mb-4">
                {statCards.map(card => (
                    <div key={card.key} className="col-sm-6 col-xl-3">
                        <Link href={card.url} className="text-decoration-none">
                            <div className="stat-card">
                                <div className="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div className="text-muted small mb-1">{card.label}</div>
                                        <h3 className="mb-0">{stats[card.key] ?? 0}</h3>
                                    </div>
                                    <div className="stat-icon" style={{background: card.color + '15', color: card.color}}>
                                        <i className={`bi ${card.icon}`}></i>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </div>
                ))}
            </div>
            <div className="content-card">
                <div className="card-header"><h5 className="mb-0">Recent Contacts</h5></div>
                <div className="card-body">
                    {recentContacts?.length > 0 ? (
                        <table className="table table-hover mb-0">
                            <thead><tr><th>Name</th><th>Email</th><th>Date</th></tr></thead>
                            <tbody>
                                {recentContacts.map((c, i) => (
                                    <tr key={i}><td>{c.firstname} {c.lastname}</td><td>{c.email}</td><td>{c.created_at}</td></tr>
                                ))}
                            </tbody>
                        </table>
                    ) : <p className="text-muted mb-0">No contact submissions yet.</p>}
                </div>
            </div>
        </>
    );
}
