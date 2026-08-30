import { useState } from 'react';
import { useUploads } from '../../Contexts/UploadContext';

function formatSize(bytes) {
    if (!bytes) return '';
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
}

export default function UploadProgressWidget() {
    const { batches, activeCount, totalDone, totalItems, retryItem, cancelBatch, dismissBatch } = useUploads();
    const [minimized, setMinimized] = useState(false);

    if (batches.length === 0) return null;

    const totalProgress = totalItems > 0 ? Math.round((totalDone / totalItems) * 100) : 0;

    return (
        <div className="upload-progress-widget" style={{
            position: 'fixed',
            bottom: '20px',
            right: '20px',
            zIndex: 9999,
            width: minimized ? '200px' : '340px',
            maxHeight: '400px',
            background: '#fff',
            borderRadius: '12px',
            boxShadow: '0 8px 32px rgba(0,6,86,0.25)',
            overflow: 'hidden',
            fontFamily: 'system-ui, -apple-system, sans-serif',
            transition: 'width 0.2s ease',
        }}>
            {/* Header */}
            <div style={{
                padding: '10px 14px',
                background: 'var(--ghi-primary, #000656)',
                color: '#fff',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'space-between',
                cursor: 'pointer',
            }} onClick={() => setMinimized(!minimized)}>
                <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                    {activeCount > 0 && (
                        <span className="upload-spinner" style={{
                            width: '14px',
                            height: '14px',
                            border: '2px solid rgba(255,255,255,0.3)',
                            borderTopColor: '#fff',
                            borderRadius: '50%',
                            animation: 'spin 0.8s linear infinite',
                        }} />
                    )}
                    <span style={{ fontWeight: 600, fontSize: '13px' }}>
                        {activeCount > 0
                            ? `Uploading ${activeCount} file${activeCount !== 1 ? 's' : ''}...`
                            : totalDone > 0
                                ? `Upload complete (${totalDone} file${totalDone !== 1 ? 's' : ''})`
                                : 'Uploads'
                        }
                    </span>
                </div>
                <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                    <span style={{ fontSize: '11px', opacity: 0.8 }}>{totalProgress}%</span>
                    <i className={`bi bi-chevron-${minimized ? 'up' : 'down'}`} style={{ fontSize: '11px' }}></i>
                </div>
            </div>

            {/* Overall progress bar */}
            {activeCount > 0 && (
                <div style={{ height: '3px', background: 'rgba(0,0,0,0.1)' }}>
                    <div style={{
                        height: '100%',
                        background: 'var(--ghi-secondary, #f1b829)',
                        width: `${totalProgress}%`,
                        transition: 'width 0.3s ease',
                    }} />
                </div>
            )}

            {/* Batch list */}
            {!minimized && (
                <div style={{ maxHeight: '300px', overflowY: 'auto' }}>
                    {batches.map(batch => {
                        const batchDone = batch.items.filter(i => i.status === 'done').length;
                        const batchActive = batch.items.filter(i => i.status === 'uploading' || i.status === 'queued').length;
                        const batchErrors = batch.items.filter(i => i.status === 'error').length;
                        const batchTotal = batch.items.length;

                        return (
                            <div key={batch.id} style={{
                                padding: '10px 14px',
                                borderBottom: '1px solid #eee',
                            }}>
                                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '6px' }}>
                                    <span style={{ fontWeight: 500, fontSize: '12px', color: '#333' }}>
                                        {batch.eventTitle || `Event #${batch.eventId || 'new'}`}
                                    </span>
                                    <div style={{ display: 'flex', gap: '4px' }}>
                                        {batchActive > 0 && (
                                            <button onClick={() => cancelBatch(batch.id)} style={{
                                                background: 'none', border: 'none', color: '#999', cursor: 'pointer', padding: '2px 4px', fontSize: '11px',
                                            }} title="Cancel">
                                                <i className="bi bi-x-lg"></i>
                                            </button>
                                        )}
                                        {batchActive === 0 && (
                                            <button onClick={() => dismissBatch(batch.id)} style={{
                                                background: 'none', border: 'none', color: '#999', cursor: 'pointer', padding: '2px 4px', fontSize: '11px',
                                            }} title="Dismiss">
                                                <i className="bi bi-x-lg"></i>
                                            </button>
                                        )}
                                    </div>
                                </div>

                                {/* Batch progress bar */}
                                <div style={{ height: '4px', background: '#eee', borderRadius: '2px', marginBottom: '4px' }}>
                                    <div style={{
                                        height: '100%',
                                        borderRadius: '2px',
                                        background: batchErrors > 0 && batchActive === 0 ? '#dc3545' : 'var(--ghi-secondary, #f1b829)',
                                        width: `${batchTotal > 0 ? (batchDone / batchTotal) * 100 : 0}%`,
                                        transition: 'width 0.3s ease',
                                    }} />
                                </div>

                                <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: '11px', color: '#888' }}>
                                    <span>{batchDone}/{batchTotal} done</span>
                                    <span style={{ display: 'flex', gap: '6px' }}>
                                        {batchActive > 0 && <span style={{ color: 'var(--ghi-primary, #000656)' }}>{batchActive} active</span>}
                                        {batchErrors > 0 && (
                                            <span style={{ color: '#dc3545' }}>
                                                {batchErrors} failed{' '}
                                                <button onClick={() => {
                                                    batch.items.filter(i => i.status === 'error').forEach(i => retryItem(batch.id, i.id));
                                                }} style={{
                                                    background: 'none', border: 'none', color: '#dc3545', cursor: 'pointer', padding: 0, fontSize: '11px', textDecoration: 'underline',
                                                }}>
                                                    retry all
                                                </button>
                                            </span>
                                        )}
                                    </span>
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}

            <style>{`@keyframes spin { to { transform: rotate(360deg); } }`}</style>
        </div>
    );
}
