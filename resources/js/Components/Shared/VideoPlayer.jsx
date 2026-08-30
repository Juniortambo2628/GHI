import { useState, useRef, useCallback, useEffect } from 'react';
import mediaUrl from './mediaUrl';

export default function VideoPlayer({ videos = [], startIndex = 0, onClose }) {
    const [current, setCurrent] = useState(startIndex);
    const videoRef = useRef(null);
    const [playing, setPlaying] = useState(false);

    const video = videos[current];

    const goTo = useCallback((idx) => {
        if (idx >= 0 && idx < videos.length) {
            setCurrent(idx);
            setPlaying(false);
        }
    }, [videos.length]);

    const prev = useCallback(() => goTo(current - 1), [goTo, current]);
    const next = useCallback(() => goTo(current + 1), [goTo, current]);

    useEffect(() => {
        if (videoRef.current) {
            videoRef.current.load();
            if (playing) videoRef.current.play().catch(() => {});
        }
    }, [current, playing]);

    useEffect(() => {
        const handleKey = (e) => {
            if (e.key === 'Escape') onClose?.();
            if (e.key === 'ArrowLeft') prev();
            if (e.key === 'ArrowRight') next();
            if (e.key === ' ' || e.key === 'k') {
                e.preventDefault();
                if (videoRef.current) {
                    if (videoRef.current.paused) {
                        videoRef.current.play();
                        setPlaying(true);
                    } else {
                        videoRef.current.pause();
                        setPlaying(false);
                    }
                }
            }
        };
        document.addEventListener('keydown', handleKey);
        return () => document.removeEventListener('keydown', handleKey);
    }, [onClose, prev, next]);

    if (!video) return null;

    const src = typeof video === 'string' ? mediaUrl(video) : mediaUrl(video.path);

    return (
        <div className="video-player-overlay" style={{
            position: 'fixed', inset: 0, zIndex: 10000,
            background: 'rgba(0,0,0,0.95)', display: 'flex',
            fontFamily: 'system-ui, -apple-system, sans-serif',
        }}>
            {/* Main player area */}
            <div style={{ flex: 1, display: 'flex', flexDirection: 'column', minWidth: 0 }}>
                {/* Top bar */}
                <div style={{ padding: '12px 20px', display: 'flex', alignItems: 'center', justifyContent: 'space-between', borderBottom: '1px solid rgba(255,255,255,0.1)' }}>
                    <div style={{ color: '#fff', fontWeight: 600, fontSize: '15px', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
                        {video.title || video.name || `Video ${current + 1}`}
                    </div>
                    <button onClick={onClose} style={{ background: 'none', border: 'none', color: '#fff', fontSize: '20px', cursor: 'pointer', padding: '4px 8px' }}>
                        <i className="bi bi-x-lg"></i>
                    </button>
                </div>

                {/* Video */}
                <div style={{ flex: 1, display: 'flex', alignItems: 'center', justifyContent: 'center', padding: '20px', background: '#000' }}>
                    <video
                        ref={videoRef}
                        src={src}
                        controls
                        autoPlay={playing}
                        style={{ maxWidth: '100%', maxHeight: '100%', borderRadius: '8px' }}
                        onPlay={() => setPlaying(true)}
                        onPause={() => setPlaying(false)}
                        onEnded={() => { if (current < videos.length - 1) next(); }}
                    />
                </div>

                {/* Bottom nav */}
                {videos.length > 1 && (
                    <div style={{ padding: '10px 20px', display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '16px', borderTop: '1px solid rgba(255,255,255,0.1)' }}>
                        <button onClick={prev} disabled={current === 0} style={{ background: 'none', border: 'none', color: current === 0 ? '#555' : '#fff', fontSize: '18px', cursor: current === 0 ? 'default' : 'pointer' }}>
                            <i className="bi bi-skip-start-fill"></i>
                        </button>
                        <span style={{ color: '#aaa', fontSize: '13px' }}>{current + 1} / {videos.length}</span>
                        <button onClick={next} disabled={current === videos.length - 1} style={{ background: 'none', border: 'none', color: current === videos.length - 1 ? '#555' : '#fff', fontSize: '18px', cursor: current === videos.length - 1 ? 'default' : 'pointer' }}>
                            <i className="bi bi-skip-end-fill"></i>
                        </button>
                    </div>
                )}
            </div>

            {/* Playlist sidebar */}
            {videos.length > 1 && (
                <div style={{ width: '320px', borderLeft: '1px solid rgba(255,255,255,0.1)', display: 'flex', flexDirection: 'column', background: '#111' }}>
                    <div style={{ padding: '14px 16px', borderBottom: '1px solid rgba(255,255,255,0.1)', fontWeight: 600, fontSize: '14px', color: '#fff' }}>
                        Playlist ({videos.length})
                    </div>
                    <div style={{ flex: 1, overflowY: 'auto' }}>
                        {videos.map((v, idx) => {
                            const thumb = v.thumbnail || v.image || '';
                            const title = v.title || v.name || `Video ${idx + 1}`;
                            const isActive = idx === current;
                            return (
                                <div
                                    key={v.id || idx}
                                    onClick={() => goTo(idx)}
                                    style={{
                                        display: 'flex', gap: '10px', padding: '10px 14px', cursor: 'pointer',
                                        background: isActive ? 'rgba(241,184,41,0.15)' : 'transparent',
                                        borderLeft: isActive ? '3px solid var(--ghi-secondary, #f1b829)' : '3px solid transparent',
                                        transition: 'background 0.15s',
                                    }}
                                    onMouseEnter={(e) => { if (!isActive) e.currentTarget.style.background = 'rgba(255,255,255,0.05)'; }}
                                    onMouseLeave={(e) => { if (!isActive) e.currentTarget.style.background = 'transparent'; }}
                                >
                                    <div style={{ width: '120px', height: '68px', borderRadius: '6px', overflow: 'hidden', flexShrink: 0, background: '#222', position: 'relative' }}>
                                        {thumb ? (
                                            <img src={mediaUrl(thumb)} alt="" style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                                        ) : (
                                            <div style={{ width: '100%', height: '100%', display: 'flex', alignItems: 'center', justifyContent: 'center', color: '#555' }}>
                                                <i className="bi bi-play-circle" style={{ fontSize: '24px' }}></i>
                                            </div>
                                        )}
                                        {isActive && (
                                            <div style={{ position: 'absolute', inset: 0, display: 'flex', alignItems: 'center', justifyContent: 'center', background: 'rgba(0,0,0,0.3)' }}>
                                                <i className="bi bi-play-fill" style={{ color: '#fff', fontSize: '20px' }}></i>
                                            </div>
                                        )}
                                        {v.duration && (
                                            <div style={{ position: 'absolute', bottom: '4px', right: '4px', background: 'rgba(0,0,0,0.8)', color: '#fff', fontSize: '10px', padding: '1px 5px', borderRadius: '3px' }}>
                                                {v.duration}
                                            </div>
                                        )}
                                    </div>
                                    <div style={{ flex: 1, minWidth: 0 }}>
                                        <div style={{ color: isActive ? '#f1b829' : '#ddd', fontSize: '13px', fontWeight: 500, overflow: 'hidden', textOverflow: 'ellipsis', display: '-webkit-box', WebkitLineClamp: 2, WebkitBoxOrient: 'vertical' }}>
                                            {title}
                                        </div>
                                        {v.event_title && (
                                            <div style={{ color: '#888', fontSize: '11px', marginTop: '3px', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
                                                {v.event_title}
                                            </div>
                                        )}
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            )}
        </div>
    );
}
