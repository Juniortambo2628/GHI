import AdminLayout from '../../Layouts/AdminLayout';
import { Head, Link } from '@inertiajs/react';
import { useEffect, useRef } from 'react';

Dashboard.layout = page => <AdminLayout title="Dashboard" description="Overview of your organization's activity." breadcrumbs={[]}>{page}</AdminLayout>;

function MiniChart({ data, label, color = '#1a3a8f' }) {
    const canvasRef = useRef(null);
    useEffect(() => {
        if (!canvasRef.current || !data?.length) return;
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const dpr = window.devicePixelRatio || 1;
        const w = canvas.clientWidth;
        const h = canvas.clientHeight;
        canvas.width = w * dpr;
        canvas.height = h * dpr;
        ctx.scale(dpr, dpr);
        const values = data.map(d => d.visitors || d.views || 0);
        const max = Math.max(...values, 1);
        const step = w / Math.max(values.length - 1, 1);
        ctx.clearRect(0, 0, w, h);
        ctx.beginPath();
        values.forEach((v, i) => {
            const x = i * step;
            const y = h - (v / max) * (h - 4);
            i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
        });
        ctx.strokeStyle = color;
        ctx.lineWidth = 2;
        ctx.lineJoin = 'round';
        ctx.stroke();
        ctx.lineTo(w, h);
        ctx.lineTo(0, h);
        ctx.closePath();
        const grad = ctx.createLinearGradient(0, 0, 0, h);
        grad.addColorStop(0, color + '30');
        grad.addColorStop(1, color + '05');
        ctx.fillStyle = grad;
        ctx.fill();
    }, [data, color]);
    return (
        <div className="mini-chart-wrapper">
            {label && <span className="mini-chart-label">{label}</span>}
            <canvas ref={canvasRef} style={{ width: '100%', height: '40px' }}></canvas>
        </div>
    );
}

function QuickStat({ icon, label, value, sub, color }) {
    return (
        <div className="quick-stat-card">
            <div className="quick-stat-icon" style={{ background: color || 'var(--admin-gradient-blue-light)' }}>
                <i className={`bi ${icon}`}></i>
            </div>
            <div className="quick-stat-body">
                <span className="quick-stat-value">{value}</span>
                <span className="quick-stat-label">{label}</span>
                {sub && <span className="quick-stat-sub">{sub}</span>}
            </div>
        </div>
    );
}

function StatusDot({ status }) {
    const colors = { published: '#22c55e', draft: '#f59e0b', archived: '#94a3b8', new: '#3b82f6', active: '#22c55e', completed: '#22c55e', pending: '#f59e0b' };
    return <span className="status-dot" style={{ background: colors[status] || '#94a3b8' }}></span>;
}

export default function Dashboard({ stats, recentContacts, recentSubscribers, publishedCounts, upcomingEvents, recentStories, topPages, visitorsByDay, recentNotifications }) {
    return (
        <>
            <Head title="Dashboard - Admin" />

            <div className="dashboard-grid dashboard-grid-4">
                <QuickStat icon="bi-file-earmark" label="Causes" value={stats.causes} sub={`${publishedCounts.causes} published`} color="var(--admin-gradient-blue)" />
                <QuickStat icon="bi-rocket" label="Initiatives" value={stats.initiatives} sub={`${publishedCounts.initiatives} published`} color="var(--admin-gradient-blue-light)" />
                <QuickStat icon="bi-calendar-event" label="Events" value={stats.events} sub={`${publishedCounts.events} published`} color="linear-gradient(135deg, #059669, #10b981)" />
                <QuickStat icon="bi-bar-chart" label="Impact" value={stats.impact} sub={`${publishedCounts.impact} published`} color="linear-gradient(135deg, #7c3aed, #a78bfa)" />
            </div>

            <div className="dashboard-grid dashboard-grid-3 mt-3">
                <QuickStat icon="bi-people" label="New Contacts" value={stats.contacts} sub={`${stats.contacts_total} total`} color="linear-gradient(135deg, #ea580c, #f97316)" />
                <QuickStat icon="bi-envelope-check" label="Subscribers" value={stats.subscribers} sub={`${stats.subscribers_total} total`} color="linear-gradient(135deg, #0891b2, #22d3ee)" />
            </div>

            <div className="dashboard-grid dashboard-grid-2 mt-3">
                <div className="content-card">
                    <div className="card-header d-flex justify-content-between align-items-center">
                        <h6 className="mb-0"><i className="bi bi-globe me-2"></i>Visitor Trend (14 days)</h6>
                        <span className="badge" style={{ background: 'var(--admin-primary)', color: '#fff' }}>{stats.visitors} this month</span>
                    </div>
                    <div className="card-body">
                        <div className="d-flex align-items-center gap-3 mb-2">
                            <span className="quick-stat-value" style={{ fontSize: '1.5rem' }}>{stats.visitors_today}</span>
                            <span className="text-muted small">visitors today</span>
                        </div>
                        <MiniChart data={visitorsByDay} color="#1a3a8f" />
                    </div>
                </div>
                <div className="content-card">
                    <div className="card-header d-flex justify-content-between align-items-center">
                        <h6 className="mb-0"><i className="bi bi-graph-up me-2"></i>Top Pages (30 days)</h6>
                        <Link href="/admin/analytics" className="btn btn-sm btn-outline-primary">View All</Link>
                    </div>
                    <div className="card-body p-0">
                        <table className="table table-hover mb-0">
                            <thead><tr><th>Page</th><th className="text-end">Views</th></tr></thead>
                            <tbody>
                                {topPages.length > 0 ? topPages.map((p, i) => (
                                    <tr key={i}><td className="text-truncate" style={{ maxWidth: '200px' }}>{p.path}</td><td className="text-end fw-semibold">{p.views}</td></tr>
                                )) : <tr><td colSpan="2" className="text-muted text-center">No data yet</td></tr>}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div className="dashboard-grid dashboard-grid-2 mt-3">
                <div className="content-card">
                    <div className="card-header d-flex justify-content-between align-items-center">
                        <h6 className="mb-0"><i className="bi bi-calendar-event me-2"></i>Upcoming Events</h6>
                        <Link href="/admin/events" className="btn btn-sm btn-outline-primary">All Events</Link>
                    </div>
                    <div className="card-body p-0">
                        {upcomingEvents.length > 0 ? (
                            <table className="table table-hover mb-0">
                                <thead><tr><th>Event</th><th>Date</th><th>Location</th></tr></thead>
                                <tbody>
                                    {upcomingEvents.map(e => (
                                        <tr key={e.id}>
                                            <td><Link href={`/admin/events/${e.id}`} className="text-decoration-none fw-semibold">{e.title}</Link></td>
                                            <td className="text-nowrap">{new Date(e.event_date).toLocaleDateString()}</td>
                                            <td className="text-truncate" style={{ maxWidth: '120px' }}>{e.location || '—'}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        ) : <div className="card-body text-muted text-center">No upcoming events.</div>}
                    </div>
                </div>
                <div className="content-card">
                    <div className="card-header d-flex justify-content-between align-items-center">
                        <h6 className="mb-0"><i className="bi bi-journal-text me-2"></i>Recent Stories</h6>
                        <Link href="/admin/stories" className="btn btn-sm btn-outline-primary">All Stories</Link>
                    </div>
                    <div className="card-body p-0">
                        {recentStories.length > 0 ? (
                            <table className="table table-hover mb-0">
                                <thead><tr><th>Title</th><th>Author</th><th>Status</th></tr></thead>
                                <tbody>
                                    {recentStories.map(s => (
                                        <tr key={s.id}>
                                            <td><Link href={`/admin/stories/${s.id}`} className="text-decoration-none fw-semibold">{s.title}</Link></td>
                                            <td>{s.author || '—'}</td>
                                            <td><StatusDot status={s.status} /> {s.status}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        ) : <div className="card-body text-muted text-center">No stories yet.</div>}
                    </div>
                </div>
            </div>

            <div className="dashboard-grid dashboard-grid-2 mt-3">
                <div className="content-card">
                    <div className="card-header d-flex justify-content-between align-items-center">
                        <h6 className="mb-0"><i className="bi bi-envelope me-2"></i>Recent Contacts</h6>
                        <Link href="/admin/contacts" className="btn btn-sm btn-outline-primary">All Contacts</Link>
                    </div>
                    <div className="card-body p-0">
                        {recentContacts.length > 0 ? (
                            <table className="table table-hover mb-0">
                                <thead><tr><th>Name</th><th>Email</th><th>Date</th></tr></thead>
                                <tbody>
                                    {recentContacts.map((c, i) => (
                                        <tr key={i}>
                                            <td><Link href={`/admin/contacts/${c.id}`} className="text-decoration-none">{c.firstname} {c.lastname}</Link></td>
                                            <td>{c.email}</td>
                                            <td className="text-nowrap">{new Date(c.created_at).toLocaleDateString()}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        ) : <div className="card-body text-muted text-center">No contacts yet.</div>}
                    </div>
                </div>
            </div>

            <div className="dashboard-grid dashboard-grid-2 mt-3">
                <div className="content-card">
                    <div className="card-header d-flex justify-content-between align-items-center">
                        <h6 className="mb-0"><i className="bi bi-envelope-check me-2"></i>Recent Subscribers</h6>
                        <Link href="/admin/subscribers" className="btn btn-sm btn-outline-primary">All Subscribers</Link>
                    </div>
                    <div className="card-body p-0">
                        {recentSubscribers.length > 0 ? (
                            <table className="table table-hover mb-0">
                                <thead><tr><th>Email</th><th>Name</th><th>Status</th></tr></thead>
                                <tbody>
                                    {recentSubscribers.map((s, i) => (
                                        <tr key={i}>
                                            <td>{s.email}</td>
                                            <td>{s.name || '—'}</td>
                                            <td><StatusDot status={s.status} /> {s.status}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        ) : <div className="card-body text-muted text-center">No subscribers yet.</div>}
                    </div>
                </div>
                <div className="content-card">
                    <div className="card-header d-flex justify-content-between align-items-center">
                        <h6 className="mb-0"><i className="bi bi-bell me-2"></i>Notifications</h6>
                        {recentNotifications.length > 0 && <span className="badge bg-primary">{recentNotifications.length} unread</span>}
                    </div>
                    <div className="card-body">
                        {recentNotifications.length > 0 ? (
                            <div className="d-flex flex-column gap-2">
                                {recentNotifications.map(n => (
                                    <div key={n.id} className="d-flex align-items-start gap-2 p-2 rounded" style={{ background: 'var(--admin-page)' }}>
                                        <i className="bi bi-info-circle text-primary mt-1"></i>
                                        <div>
                                            <div className="fw-semibold small">{n.title}</div>
                                            <div className="text-muted" style={{ fontSize: '0.78rem' }}>{n.message}</div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : <div className="text-muted text-center">No unread notifications.</div>}
                    </div>
                </div>
            </div>

            <div className="mt-3">
                <div className="content-card">
                    <div className="card-header"><h6 className="mb-0"><i className="bi bi-lightning me-2"></i>Quick Actions</h6></div>
                    <div className="card-body d-flex flex-wrap gap-2">
                        <Link href="/admin/causes/create" className="btn btn-sm btn-primary"><i className="bi bi-plus-circle me-1"></i>New Cause</Link>
                        <Link href="/admin/initiatives/create" className="btn btn-sm btn-primary"><i className="bi bi-plus-circle me-1"></i>New Initiative</Link>
                        <Link href="/admin/events/create" className="btn btn-sm btn-primary"><i className="bi bi-plus-circle me-1"></i>New Event</Link>
                        <Link href="/admin/stories/create" className="btn btn-sm btn-primary"><i className="bi bi-plus-circle me-1"></i>New Story</Link>
                        <Link href="/admin/impact/create" className="btn btn-sm btn-primary"><i className="bi bi-plus-circle me-1"></i>New Impact</Link>
                        <Link href="/admin/analytics" className="btn btn-sm btn-outline-primary"><i className="bi bi-bar-chart me-1"></i>Analytics</Link>
                        <Link href="/admin/settings" className="btn btn-sm btn-outline-secondary"><i className="bi bi-gear me-1"></i>Settings</Link>
                    </div>
                </div>
            </div>
        </>
    );
}
