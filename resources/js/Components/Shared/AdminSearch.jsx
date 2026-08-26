import { useState, useRef, useEffect, useCallback } from 'react';
import { router } from '@inertiajs/react';
import { RESOURCE_ICONS, RESOURCE_URLS } from '../../Constants/resources';

export default function AdminSearch() {
    const [query, setQuery] = useState('');
    const [results, setResults] = useState(null);
    const [activeIdx, setActiveIdx] = useState(-1);
    const [open, setOpen] = useState(false);
    const inputRef = useRef(null);
    const panelRef = useRef(null);
    const timerRef = useRef(null);

    const flatResults = results ? Object.entries(results).flatMap(([type, items]) =>
        items.map(item => ({ ...item, _type: type }))
    ) : [];

    const search = useCallback((q) => {
        if (!q || q.length < 2) { setResults(null); return; }
        clearTimeout(timerRef.current);
        timerRef.current = setTimeout(() => {
            fetch(`/admin/search?q=${encodeURIComponent(q)}`, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then(r => r.json())
                .then(data => { setResults(data); setActiveIdx(-1); })
                .catch(() => setResults(null));
        }, 250);
    }, []);

    useEffect(() => {
        search(query);
    }, [query, search]);

    useEffect(() => {
        if (!open) return undefined;
        const handler = (e) => {
            if (panelRef.current && !panelRef.current.contains(e.target) && !inputRef.current?.contains(e.target)) {
                setOpen(false);
            }
        };
        document.addEventListener('mousedown', handler);
        return () => document.removeEventListener('mousedown', handler);
    }, [open]);

    const navigate = (item) => {
        const base = RESOURCE_URLS[item._type];
        if (base) {
            setOpen(false);
            setQuery('');
            setResults(null);
            router.get(`${base}/${item.id}`);
        }
    };

    const handleKeyDown = (e) => {
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            setActiveIdx(prev => Math.min(prev + 1, flatResults.length - 1));
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setActiveIdx(prev => Math.max(prev - 1, -1));
        } else if (e.key === 'Enter' && activeIdx >= 0 && flatResults[activeIdx]) {
            e.preventDefault();
            navigate(flatResults[activeIdx]);
        } else if (e.key === 'Escape') {
            setOpen(false);
        }
    };

    const hasResults = results && Object.values(results).some(arr => arr.length > 0);

    return (
        <div className="admin-search" ref={panelRef}>
            <i className="bi bi-search"></i>
            <input
                ref={inputRef}
                type="text"
                placeholder="Search anything..."
                value={query}
                onChange={(e) => { setQuery(e.target.value); setOpen(true); }}
                onFocus={() => { if (query.length >= 2) setOpen(true); }}
                onKeyDown={handleKeyDown}
                aria-label="Search"
            />
            {open && query.length >= 2 && (
                <div className="admin-search-results">
                    {hasResults ? (
                        Object.entries(results).map(([type, items]) => (
                            items.length > 0 && (
                                <div key={type}>
                                    <div className="admin-search-group-title">{type}</div>
                                    {items.map(item => {
                                        const globalIdx = flatResults.indexOf(flatResults.find(r => r.id === item.id && r._type === type));
                                        return (
                                            <div
                                                key={item.id}
                                                className={`admin-search-item ${globalIdx === activeIdx ? 'active' : ''}`}
                                                onClick={() => navigate({ ...item, _type: type })}
                                            >
                                                <i className={`bi ${RESOURCE_ICONS[type] || 'bi-file'}`}></i>
                                                <span>{item.title || item.name || item.email || `#${item.id}`}</span>
                                            </div>
                                        );
                                    })}
                                </div>
                            )
                        ))
                    ) : (
                        <div className="admin-search-empty">No results found for &ldquo;{query}&rdquo;</div>
                    )}
                </div>
            )}
        </div>
    );
}
