import { Head } from '@inertiajs/react';
import AdminLayout from '../../../Layouts/AdminLayout';

function StatusBadge({ status }) {
    const colors = { healthy: '#18794e', warning: '#946200', critical: '#dc3545' };
    const icons = { healthy: 'bi-check-circle-fill', warning: 'bi-exclamation-triangle-fill', critical: 'bi-x-circle-fill' };
    return (
        <span style={{ color: colors[status] || '#667085', fontSize: '0.85rem' }}>
            <i className={`bi ${icons[status] || 'bi-circle'}`}></i> {status}
        </span>
    );
}

export default function SystemStatus({ checks, logs, queueStats, phpVersion, laravelVersion, environment }) {
    const healthyCount = checks.filter(c => c.status === 'healthy').length;
    const totalChecks = checks.length;

    return (
        <AdminLayout title="System Status" description="API health checks, system diagnostics, and logs.">
            <Head title="System Status - Admin" />

            {/* System Info */}
            <div className="row g-3 mb-4">
                <div className="col-sm-6 col-lg-2">
                    <div className="content-card h-100">
                        <div className="card-body">
                            <div style={{ color: 'var(--admin-muted)', fontSize: '0.78rem' }}>Environment</div>
                            <div style={{ fontFamily: 'Jost, sans-serif', fontWeight: 600, fontSize: '1rem' }}>{environment}</div>
                        </div>
                    </div>
                </div>
                <div className="col-sm-6 col-lg-2">
                    <div className="content-card h-100">
                        <div className="card-body">
                            <div style={{ color: 'var(--admin-muted)', fontSize: '0.78rem' }}>PHP</div>
                            <div style={{ fontFamily: 'Jost, sans-serif', fontWeight: 600, fontSize: '1rem' }}>{phpVersion}</div>
                        </div>
                    </div>
                </div>
                <div className="col-sm-6 col-lg-2">
                    <div className="content-card h-100">
                        <div className="card-body">
                            <div style={{ color: 'var(--admin-muted)', fontSize: '0.78rem' }}>Laravel</div>
                            <div style={{ fontFamily: 'Jost, sans-serif', fontWeight: 600, fontSize: '1rem' }}>{laravelVersion}</div>
                        </div>
                    </div>
                </div>
                <div className="col-sm-6 col-lg-2">
                    <div className="content-card h-100">
                        <div className="card-body">
                            <div style={{ color: 'var(--admin-muted)', fontSize: '0.78rem' }}>Queue Pending</div>
                            <div style={{ fontFamily: 'Jost, sans-serif', fontWeight: 600, fontSize: '1rem' }}>{queueStats.pending}</div>
                        </div>
                    </div>
                </div>
                <div className="col-sm-6 col-lg-2">
                    <div className="content-card h-100">
                        <div className="card-body">
                            <div style={{ color: 'var(--admin-muted)', fontSize: '0.78rem' }}>Failed Jobs</div>
                            <div style={{ fontFamily: 'Jost, sans-serif', fontWeight: 600, fontSize: '1rem', color: queueStats.failed > 0 ? '#dc3545' : 'inherit' }}>{queueStats.failed}</div>
                        </div>
                    </div>
                </div>
                <div className="col-sm-6 col-lg-2">
                    <div className="content-card h-100">
                        <div className="card-body">
                            <div style={{ color: 'var(--admin-muted)', fontSize: '0.78rem' }}>Health</div>
                            <div style={{ fontFamily: 'Jost, sans-serif', fontWeight: 600, fontSize: '1rem', color: healthyCount === totalChecks ? '#18794e' : '#946200' }}>
                                {healthyCount}/{totalChecks} Passing
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Health Checks */}
            <div className="content-card mb-4">
                <div className="card-header d-flex justify-content-between align-items-center">
                    <h5 className="mb-0">Health Checks</h5>
                    <a href="/admin/system-status" className="btn btn-sm btn-outline-primary"><i className="bi bi-arrow-clockwise me-1"></i>Refresh</a>
                </div>
                <div className="card-body p-0">
                    <table className="table table-hover mb-0">
                        <thead><tr><th>Service</th><th>Status</th><th>Message</th></tr></thead>
                        <tbody>
                            {checks.map(check => (
                                <tr key={check.name}>
                                    <td><strong>{check.name}</strong></td>
                                    <td><StatusBadge status={check.status} /></td>
                                    <td style={{ color: 'var(--admin-muted)', fontSize: '0.85rem' }}>{check.message}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* Logs */}
            <div className="content-card">
                <div className="card-header"><h5 className="mb-0">Recent Logs</h5></div>
                <div className="card-body">
                    {logs.length === 0 ? (
                        <p className="text-muted mb-0">No recent log entries.</p>
                    ) : (
                        <pre style={{ background: '#1a1a2e', color: '#e0e0e0', padding: '1rem', borderRadius: 'var(--admin-radius)', fontSize: '0.75rem', maxHeight: '400px', overflow: 'auto', whiteSpace: 'pre-wrap', wordBreak: 'break-all', marginBottom: 0 }}>
                            {logs.join('\n')}
                        </pre>
                    )}
                </div>
            </div>
        </AdminLayout>
    );
}
