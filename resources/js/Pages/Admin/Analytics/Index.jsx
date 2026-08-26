import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import AdminLayout from '../../../Layouts/AdminLayout';
import AnalyticsChart from '../../../Components/Shared/AnalyticsChart';

const summaryCards = [
    { key: 'content', label: 'Content', icon: 'bi-file-earmark-text', color: '#1a3a8f' },
    { key: 'views', label: 'Views', icon: 'bi-eye', color: '#2d5bd0' },
    { key: 'visitors', label: 'Visitors', icon: 'bi-people', color: '#000656' },
    { key: 'contacts', label: 'Contacts', icon: 'bi-envelope', color: '#1a3a8f' },
    { key: 'subscribers', label: 'Subscribers', icon: 'bi-person-check', color: '#2d5bd0' },
];

function GrowthBadge({ value }) {
    if (value === 0) return null;
    const isPositive = value > 0;
    return (
        <span style={{ fontSize: '0.75rem', fontWeight: 600, color: isPositive ? '#18794e' : '#dc3545' }}>
            <i className={`bi bi-arrow-${isPositive ? 'up' : 'down'}`}></i> {Math.abs(value)}%
        </span>
    );
}

export default function Index({ metrics, advancedMetrics, contentBreakdown, topPages, trafficByDay, views, filters }) {
    const [from, setFrom] = useState(filters?.from || '');
    const [to, setTo] = useState(filters?.to || '');

    const applyFilter = () => {
        const params = {};
        if (from) params.from = from;
        if (to) params.to = to;
        router.get('/admin/analytics', params, { preserveState: true });
    };

    const filterBar = (
        <div className="d-flex align-items-center gap-2 flex-nowrap">
            <div className="admin-filter-row">
                <input type="date" name="from" value={from} onChange={e => setFrom(e.target.value)} />
                <input type="date" name="to" value={to} onChange={e => setTo(e.target.value)} />
            </div>
            <button type="button" className="btn btn-sm btn-primary" onClick={applyFilter}>Apply</button>
            <a className="btn btn-sm btn-outline-secondary" href="/admin/analytics/report?type=overview">Generate Report</a>
        </div>
    );

    return (
        <AdminLayout title="Analytics" description="Operational performance and website visitor activity." toolbarLeft={filterBar}>
            <Head title="Analytics - Admin" />
            
            {/* Summary Cards */}
            <div className="row g-3 mb-4">
                {summaryCards.map(card => (
                    <div className="col-sm-6 col-lg-4 col-xl-2" key={card.key}>
                        <div className="content-card h-100">
                            <div className="card-body d-flex align-items-center gap-3">
                                <div style={{ display: 'grid', placeItems: 'center', width: '3rem', height: '3rem', borderRadius: 'var(--admin-radius)', background: `${card.color}12`, color: card.color, fontSize: '1.25rem', flexShrink: 0 }}>
                                    <i className={`bi ${card.icon}`}></i>
                                </div>
                                <div>
                                    <div style={{ color: 'var(--admin-muted)', fontSize: '0.78rem' }}>{card.label}</div>
                                    <div style={{ fontFamily: 'Jost, sans-serif', fontWeight: 600, fontSize: '1.5rem', lineHeight: 1.2 }}>
                                        {metrics[card.key] ?? 0}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                ))}
            </div>

            {/* Advanced Metrics */}
            <div className="row g-3 mb-4">
                <div className="col-sm-6 col-lg-3">
                    <div className="content-card h-100">
                        <div className="card-body">
                            <div className="d-flex align-items-center justify-content-between mb-1">
                                <span style={{ color: 'var(--admin-muted)', fontSize: '0.78rem' }}>View Growth</span>
                                <GrowthBadge value={advancedMetrics.viewGrowth} />
                            </div>
                            <div style={{ fontFamily: 'Jost, sans-serif', fontWeight: 600, fontSize: '1.25rem' }}>{advancedMetrics.viewGrowth}%</div>
                            <div style={{ color: 'var(--admin-muted)', fontSize: '0.72rem' }}>vs previous period</div>
                        </div>
                    </div>
                </div>
                <div className="col-sm-6 col-lg-3">
                    <div className="content-card h-100">
                        <div className="card-body">
                            <div className="d-flex align-items-center justify-content-between mb-1">
                                <span style={{ color: 'var(--admin-muted)', fontSize: '0.78rem' }}>Visitor Growth</span>
                                <GrowthBadge value={advancedMetrics.visitorGrowth} />
                            </div>
                            <div style={{ fontFamily: 'Jost, sans-serif', fontWeight: 600, fontSize: '1.25rem' }}>{advancedMetrics.visitorGrowth}%</div>
                            <div style={{ color: 'var(--admin-muted)', fontSize: '0.72rem' }}>vs previous period</div>
                        </div>
                    </div>
                </div>
                <div className="col-sm-6 col-lg-3">
                    <div className="content-card h-100">
                        <div className="card-body">
                            <div style={{ color: 'var(--admin-muted)', fontSize: '0.78rem', marginBottom: '0.25rem' }}>Engagement Rate</div>
                            <div style={{ fontFamily: 'Jost, sans-serif', fontWeight: 600, fontSize: '1.25rem' }}>{advancedMetrics.engagement}</div>
                            <div style={{ color: 'var(--admin-muted)', fontSize: '0.72rem' }}>views per visitor</div>
                        </div>
                    </div>
                </div>
                <div className="col-sm-6 col-lg-3">
                    <div className="content-card h-100">
                        <div className="card-body">
                            <div style={{ color: 'var(--admin-muted)', fontSize: '0.78rem', marginBottom: '0.25rem' }}>Conversion Rate</div>
                            <div style={{ fontFamily: 'Jost, sans-serif', fontWeight: 600, fontSize: '1.25rem' }}>{advancedMetrics.conversionRate}%</div>
                            <div style={{ color: 'var(--admin-muted)', fontSize: '0.72rem' }}>contacts / visitors</div>
                        </div>
                    </div>
                </div>
                <div className="col-sm-6 col-lg-3">
                    <div className="content-card h-100">
                        <div className="card-body">
                            <div style={{ color: 'var(--admin-muted)', fontSize: '0.78rem', marginBottom: '0.25rem' }}>Conversion Rate</div>
                            <div style={{ fontFamily: 'Jost, sans-serif', fontWeight: 600, fontSize: '1.25rem' }}>{advancedMetrics.conversionRate}%</div>
                            <div style={{ color: 'var(--admin-muted)', fontSize: '0.72rem' }}>contacts / visitors</div>
                        </div>
                    </div>
                </div>
                <div className="col-sm-6 col-lg-3">
                    <div className="content-card h-100">
                        <div className="card-body">
                            <div style={{ color: 'var(--admin-muted)', fontSize: '0.78rem', marginBottom: '0.25rem' }}>Content by Type</div>
                            <div style={{ fontSize: '0.82rem', lineHeight: 1.8 }}>
                                {Object.entries(contentBreakdown).map(([key, val]) => (
                                    <span key={key} className="me-2"><strong>{val}</strong> {key}</span>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Charts Row */}
            <div className="row g-4 mb-4">
                <div className="col-lg-8">
                    <div className="content-card h-100">
                        <div className="card-header"><h5 className="mb-0">Visitor Activity</h5></div>
                        <div className="card-body">
                            <AnalyticsChart views={views} />
                        </div>
                    </div>
                </div>
                <div className="col-lg-4">
                    <div className="content-card h-100">
                        <div className="card-header"><h5 className="mb-0">Traffic by Day</h5></div>
                        <div className="card-body">
                            {trafficByDay.length === 0 ? (
                                <p className="text-muted">No data available</p>
                            ) : (
                                <table className="table table-sm mb-0">
                                    <thead><tr><th>Day</th><th>Views</th></tr></thead>
                                    <tbody>
                                        {trafficByDay.map(d => {
                                            const days = ['', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                                            return <tr key={d.day}><td>{days[d.day]}</td><td>{d.views}</td></tr>;
                                        })}
                                    </tbody>
                                </table>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* Top Pages + Daily Detail */}
            <div className="row g-4">
                <div className="col-lg-6">
                    <div className="content-card h-100">
                        <div className="card-header"><h5 className="mb-0">Top Pages</h5></div>
                        <div className="card-body p-0">
                            <table className="table table-hover mb-0">
                                <thead><tr><th>Path</th><th>Views</th><th>Visitors</th></tr></thead>
                                <tbody>
                                    {topPages.length === 0 ? (
                                        <tr><td colSpan="3" className="text-muted">No data</td></tr>
                                    ) : topPages.map(p => (
                                        <tr key={p.path}><td><code>{p.path}</code></td><td>{p.views}</td><td>{p.visitors}</td></tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div className="col-lg-6">
                    <div className="content-card h-100">
                        <div className="card-header"><h5 className="mb-0">Daily Detail</h5></div>
                        <div className="card-body p-0">
                            <table className="table table-hover mb-0">
                                <thead><tr><th>Date</th><th>Views</th><th>Visitors</th></tr></thead>
                                <tbody>
                                    {views.length === 0 ? (
                                        <tr><td colSpan="3" className="text-muted">No data</td></tr>
                                    ) : views.map(day => (
                                        <tr key={day.date}><td>{day.date}</td><td>{day.views}</td><td>{day.visitors}</td></tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
