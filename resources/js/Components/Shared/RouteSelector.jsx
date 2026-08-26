import { useState, useRef, useEffect, useCallback } from 'react';
import { createPortal } from 'react-dom';

const ROUTES = [
    { path: '/', label: 'Home' },
    { path: '/causes', label: 'Causes' },
    { path: '/initiatives', label: 'Initiatives' },
    { path: '/events', label: 'Events' },
    { path: '/impact', label: 'Impact' },
    { path: '/stories', label: 'Stories' },
    { path: '/contact', label: 'Contact' },
    { path: '/get-involved', label: 'Get Involved' },
    { path: '/search', label: 'Search' },
];

export default function RouteSelector({ value, onChange, name, placeholder = 'Select a page...' }) {
    const [open, setOpen] = useState(false);
    const [filter, setFilter] = useState('');
    const triggerRef = useRef(null);
    const dropdownRef = useRef(null);
    const searchRef = useRef(null);
    const [dropdownStyle, setDropdownStyle] = useState({});
    const [renderDropdown, setRenderDropdown] = useState(false);

    const positionDropdown = useCallback(() => {
        if (!triggerRef.current) return;
        const rect = triggerRef.current.getBoundingClientRect();
        const spaceBelow = window.innerHeight - rect.bottom;
        const dropdownHeight = 280;
        const openUp = spaceBelow < dropdownHeight + 8;
        setDropdownStyle({
            position: 'fixed',
            left: rect.left,
            width: rect.width,
            ...(openUp
                ? { bottom: window.innerHeight - rect.top + 4 }
                : { top: rect.bottom + 4 }),
            zIndex: 9999,
        });
    }, []);

    useEffect(() => {
        if (!open) {
            setRenderDropdown(false);
            return;
        }
        positionDropdown();
        requestAnimationFrame(() => setRenderDropdown(true));
        const handler = (e) => {
            if (
                triggerRef.current && !triggerRef.current.contains(e.target) &&
                dropdownRef.current && !dropdownRef.current.contains(e.target)
            ) {
                setOpen(false);
                setFilter('');
            }
        };
        const onScroll = () => positionDropdown();
        document.addEventListener('mousedown', handler);
        window.addEventListener('scroll', onScroll, true);
        window.addEventListener('resize', onScroll);
        return () => {
            document.removeEventListener('mousedown', handler);
            window.removeEventListener('scroll', onScroll, true);
            window.removeEventListener('resize', onScroll);
        };
    }, [open, positionDropdown]);

    useEffect(() => {
        if (renderDropdown && searchRef.current) {
            searchRef.current.focus({ preventScroll: true });
        }
    }, [renderDropdown]);

    const filtered = ROUTES.filter(r =>
        r.label.toLowerCase().includes(filter.toLowerCase()) ||
        r.path.toLowerCase().includes(filter.toLowerCase())
    );

    const selected = ROUTES.find(r => r.path === value);

    const dropdown = open && renderDropdown && createPortal(
        <div className="route-selector-dropdown" ref={dropdownRef} style={dropdownStyle}>
            <div className="route-selector-search">
                <input
                    ref={searchRef}
                    type="text"
                    className="form-control form-control-sm"
                    placeholder="Search pages..."
                    value={filter}
                    onChange={e => setFilter(e.target.value)}
                    onFocus={(e) => e.target.scrollIntoView({ block: 'nearest' })}
                />
            </div>
            <div className="route-selector-list">
                <button
                    type="button"
                    className={`route-selector-item ${!value ? 'active' : ''}`}
                    onClick={() => { onChange(''); setOpen(false); setFilter(''); }}
                >
                    <span className="text-muted">None (no button)</span>
                </button>
                {filtered.map(route => (
                    <button
                        key={route.path}
                        type="button"
                        className={`route-selector-item ${value === route.path ? 'active' : ''}`}
                        onClick={() => { onChange(route.path); setOpen(false); setFilter(''); }}
                    >
                        <span className="route-selector-label">{route.label}</span>
                        <span className="route-selector-path">{route.path}</span>
                    </button>
                ))}
                {filtered.length === 0 && <div className="route-selector-empty">No matching pages</div>}
            </div>
        </div>,
        document.body
    );

    return (
        <div className="route-selector" ref={triggerRef}>
            <input type="hidden" name={name} value={value || ''} />
            <button type="button" className="form-control text-start route-selector-trigger" onClick={() => setOpen(!open)}>
                {selected ? (
                    <span><i className="bi bi-link-45deg me-1 text-muted"></i>{selected.label} <small className="text-muted">({selected.path})</small></span>
                ) : (
                    <span className="text-muted">{placeholder}</span>
                )}
                <i className={`bi bi-chevron-${open ? 'up' : 'down'} route-selector-chevron`}></i>
            </button>
            {dropdown}
        </div>
    );
}
