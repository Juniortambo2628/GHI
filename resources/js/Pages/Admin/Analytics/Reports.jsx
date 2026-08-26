import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import AdminLayout from '../../../Layouts/AdminLayout';

const reportTypes = [
    { value: 'overview', label: 'Overview', icon: 'bi-bar-chart-line' },
    { value: 'traffic', label: 'Traffic', icon: 'bi-graph-up' },
    { value: 'content', label: 'Content', icon: 'bi-file-earmark-text' },
];

function ReportTable({ data }) {
    if (!data || typeof data !== 'object') return null;

    if (data.summary) {
        return (
            <div className="row g-3 mb-4">
                {Object.entries(data.summary).map(([key, val]) => (
                    <div className="col-sm-6 col-lg-3" key={key}>
                        <div className="content-card h-100">
                            <div className="card-body">
                                <div style={{ color: 'var(--admin-muted)', fontSize: '0.78rem', textTransform: 'capitalize' }}>{key.replace(/_/g, ' ')}</div>
                                <div style={{ fontFamily: 'Jost, sans-serif', fontWeight: 600, fontSize: '1.25rem' }}>
                                    {typeof val === 'number' && key.includes('amount') || key.includes('total') || key.includes('average') || key.includes('completed') || key.includes('pending')
                                        ? `$${Number(val).toFixed(2)}` : val}
                                </div>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        );
    }
    return null;
}

function DataTable({ title, columns, rows }) {
    if (!rows || rows.length === 0) return null;
    return (
        <div className="content-card mb-4">
            <div className="card-header"><h5 className="mb-0">{title}</h5></div>
            <div className="card-body p-0">
                <div className="table-responsive">
                    <table className="table table-hover mb-0">
                        <thead><tr>{columns.map((c, i) => <th key={i}>{c.label}</th>)}</tr></thead>
                        <tbody>
                            {rows.map((row, i) => (
                                <tr key={i}>{columns.map((c, j) => <td key={j}>{c.render ? c.render(row) : row[c.key]}</td>)}</tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}

export default function Reports({ report, type, filters }) {
    const [from, setFrom] = useState(filters?.from || '');
    const [to, setTo] = useState(filters?.to || '');
    const [reportType, setReportType] = useState(type);

    const generate = () => {
        const params = { type: reportType };
        if (from) params.from = from;
        if (to) params.to = to;
        router.get('/admin/analytics/report', params, { preserveState: true });
    };

    const downloadCSV = () => {
        if (!report?.daily || report.daily.length === 0) return;
        const headers = Object.keys(report.daily[0]);
        const csv = [headers.join(','), ...report.daily.map(row => headers.map(h => row[h]).join(','))].join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${reportType}-report-${from || 'all'}-${to || 'all'}.csv`;
        a.click();
        URL.revokeObjectURL(url);
    };

    const filterBar = (
        <div className="d-flex align-items-center gap-2 flex-nowrap">
            <div className="admin-filter-row">
                <select className="form-select" value={reportType} onChange={e => setReportType(e.target.value)} style={{ height: '2.25rem', padding: '0.4rem 1rem', borderRadius: '999px', fontSize: '0.82rem' }}>
                    {reportTypes.map(t => <option key={t.value} value={t.value}>{t.label}</option>)}
                </select>
                <input type="date" value={from} onChange={e => setFrom(e.target.value)} />
                <input type="date" value={to} onChange={e => setTo(e.target.value)} />
            </div>
            <button type="button" className="btn btn-sm btn-primary" onClick={generate}>Generate</button>
            <button type="button" className="btn btn-sm btn-outline-secondary" onClick={downloadCSV}><i className="bi bi-download me-1"></i>CSV</button>
        </div>
    );

    return (
        <AdminLayout title="Reports" description="Generate and export analytics reports." toolbarLeft={filterBar}>
            <Head title="Reports - Admin" />
            
            <ReportTable data={report} />

            {report?.daily && (
                <DataTable
                    title={`${report.title || 'Report'} - Daily Breakdown`}
                    columns={[
                        { label: 'Date', key: 'date' },
                        { label: 'Views', key: 'views' },
                        { label: 'Visitors', key: 'visitors' },
                    ]}
                    rows={report.daily}
                />
            )}

            {report?.top_pages && (
                <DataTable
                    title="Top Pages"
                    columns={[
                        { label: 'Path', key: 'path' },
                        { label: 'Views', key: 'views' },
                        { label: 'Visitors', key: 'visitors' },
                    ]}
                    rows={report.top_pages}
                />
            )}

            {report?.by_hour && (
                <DataTable
                    title="Traffic by Hour"
                    columns={[
                        { label: 'Hour', render: r => `${r.hour}:00` },
                        { label: 'Views', key: 'views' },
                    ]}
                    rows={report.by_hour}
                />
            )}

            {report?.counts && (
                <div className="content-card">
                    <div className="card-header"><h5 className="mb-0">Content Inventory</h5></div>
                    <div className="card-body">
                        <div className="row g-3">
                            {Object.entries(report.counts).map(([key, val]) => (
                                <div className="col-sm-6 col-lg-2" key={key}>
                                    <div className="text-center">
                                        <div style={{ fontFamily: 'Jost, sans-serif', fontWeight: 600, fontSize: '1.5rem' }}>{val}</div>
                                        <div style={{ color: 'var(--admin-muted)', fontSize: '0.82rem', textTransform: 'capitalize' }}>{key}</div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            )}

            {report?.recent_content && (
                <div className="row g-4">
                    {Object.entries(report.recent_content).map(([key, items]) => (
                        <div className="col-lg-4" key={key}>
                            <div className="content-card h-100">
                                <div className="card-header"><h5 className="mb-0" style={{ textTransform: 'capitalize' }}>Recent {key}</h5></div>
                                <div className="card-body p-0">
                                    <table className="table table-sm mb-0">
                                        <thead><tr><th>Title</th><th>Status</th><th>Date</th></tr></thead>
                                        <tbody>
                                            {items.length === 0 ? (
                                                <tr><td colSpan="3" className="text-muted">None</td></tr>
                                            ) : items.map(item => (
                                                <tr key={item.id}><td>{item.title}</td><td><span className={`status-badge status-${item.status}`}>{item.status}</span></td><td>{item.created_at?.split(' ')[0]}</td></tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </AdminLayout>
    );
}
