import { useState, useEffect, useRef } from 'react';
import { router } from '@inertiajs/react';

export default function SearchModal({ open, onClose }) {
    const [query, setQuery] = useState('');
    const [results, setResults] = useState([]);
    const [loading, setLoading] = useState(false);
    const inputRef = useRef(null);
    const timerRef = useRef(null);

    useEffect(() => {
        if (open) {
            setQuery('');
            setResults([]);
            setTimeout(() => inputRef.current?.focus(), 100);
        }
    }, [open]);

    useEffect(() => {
        if (!open) return;
        const handler = (e) => { if (e.key === 'Escape') onClose(); };
        document.addEventListener('keydown', handler);
        return () => document.removeEventListener('keydown', handler);
    }, [open, onClose]);

    useEffect(() => {
        if (timerRef.current) clearTimeout(timerRef.current);
        if (!query.trim()) { setResults([]); return; }
        setLoading(true);
        timerRef.current = setTimeout(() => {
            router.get('/search', { q: query }, {
                preserveState: true,
                preserveScroll: true,
                only: ['results'],
                onFinish: () => setLoading(false),
                onSuccess: (page) => {
                    setResults(page.props?.results || []);
                },
            });
        }, 300);
        return () => clearTimeout(timerRef.current);
    }, [query]);

    const handleSubmit = (e) => {
        e.preventDefault();
        if (query.trim()) {
            window.location.href = `/search?q=${encodeURIComponent(query.trim())}`;
        }
    };

    if (!open) return null;

    return (
        <div className="search-modal-overlay" onClick={onClose}>
            <div className="search-modal-dialog" onClick={(e) => e.stopPropagation()}>
                <form onSubmit={handleSubmit} className="search-modal-form">
                    <i className="bi bi-search search-modal-icon"></i>
                    <input
                        ref={inputRef}
                        type="text"
                        value={query}
                        onChange={(e) => setQuery(e.target.value)}
                        placeholder="Search causes, initiatives, events, stories..."
                        className="search-modal-input"
                    />
                    <button type="button" className="search-modal-close" onClick={onClose}>
                        <i className="bi bi-x-lg"></i>
                    </button>
                </form>
                {results.length > 0 && (
                    <div className="search-modal-results">
                        {results.slice(0, 8).map((r, i) => (
                            <a key={i} href={r.url} className="search-modal-result-item" onClick={onClose}>
                                <span className="search-modal-result-type">{r.type}</span>
                                <span className="search-modal-result-title">{r.title}</span>
                                {r.description && <span className="search-modal-result-desc">{r.description.substring(0, 80)}...</span>}
                            </a>
                        ))}
                    </div>
                )}
                {loading && query.trim() && (
                    <div className="search-modal-loading">
                        <span className="spinner-border spinner-border-sm me-2"></span>Searching...
                    </div>
                )}
                {!loading && query.trim() && results.length === 0 && (
                    <div className="search-modal-empty">No results found. Try different keywords.</div>
                )}
            </div>
        </div>
    );
}
