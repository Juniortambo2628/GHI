import { useState, useRef, useEffect, useCallback } from 'react';

const ICONS = [
    { id: 'globe2', label: 'Globe' },
    { id: 'heart', label: 'Heart' },
    { id: 'people', label: 'People' },
    { id: 'people-fill', label: 'People Filled' },
    { id: 'hand-thumbs-up', label: 'Thumbs Up' },
    { id: 'hand-thumbs-up-fill', label: 'Thumbs Up Filled' },
    { id: 'hand-index-thumb', label: 'Pointing' },
    { id: 'hand-index-thumb-fill', label: 'Pointing Filled' },
    { id: 'heart-fill', label: 'Heart Filled' },
    { id: 'shield-check', label: 'Shield Check' },
    { id: 'lightbulb', label: 'Lightbulb' },
    { id: 'lightbulb-fill', label: 'Lightbulb Filled' },
    { id: 'book', label: 'Book' },
    { id: 'book-fill', label: 'Book Filled' },
    { id: 'mortarboard', label: 'Education' },
    { id: 'mortarboard-fill', label: 'Education Filled' },
    { id: 'hospital', label: 'Hospital' },
    { id: 'heart-pulse', label: 'Health' },
    { id: 'heart-pulse-fill', label: 'Health Filled' },
    { id: 'bandaid', label: 'Bandaid' },
    { id: 'bandaid-fill', label: 'Bandaid Filled' },
    { id: 'droplet', label: 'Water' },
    { id: 'droplet-fill', label: 'Water Filled' },
    { id: 'cloud-rain', label: 'Rain' },
    { id: 'sun', label: 'Sun' },
    { id: 'sun-fill', label: 'Sun Filled' },
    { id: 'tree', label: 'Tree' },
    { id: 'flower1', label: 'Flower' },
    { id: 'egg', label: 'Seed' },
    { id: 'cup-hot', label: 'Nourishment' },
    { id: 'cup-hot-fill', label: 'Nourishment Filled' },
    { id: 'house', label: 'House' },
    { id: 'house-fill', label: 'House Filled' },
    { id: 'buildings', label: 'Buildings' },
    { id: 'buildings-fill', label: 'Buildings Filled' },
    { id: 'flag', label: 'Flag' },
    { id: 'flag-fill', label: 'Flag Filled' },
    { id: 'trophy', label: 'Trophy' },
    { id: 'trophy-fill', label: 'Trophy Filled' },
    { id: 'star', label: 'Star' },
    { id: 'star-fill', label: 'Star Filled' },
    { id: 'gem', label: 'Gem' },
    { id: 'gem-fill', label: 'Gem Filled' },
    { id: 'megaphone', label: 'Megaphone' },
    { id: 'megaphone-fill', label: 'Megaphone Filled' },
    { id: 'bullhorn', label: 'Advocacy' },
    { id: 'handshake', label: 'Partnership' },
    { id: 'handshake-fill', label: 'Partnership Filled' },
    { id: 'globe-americas', label: 'Americas' },
    { id: 'globe-africa', label: 'Africa' },
    { id: 'globe-asia-australia', label: 'Asia' },
    { id: 'geo-alt', label: 'Location' },
    { id: 'geo-alt-fill', label: 'Location Filled' },
    { id: 'pin-map', label: 'Map Pin' },
    { id: 'cash-stack', label: 'Cash' },
    { id: 'piggy-bank', label: 'Savings' },
    {id: 'coin', label: 'Coin' },
    { id: 'credit-card', label: 'Card' },
    { id: 'currency-dollar', label: 'Dollar' },
    { id: 'graph-up', label: 'Growth' },
    { id: 'graph-up-arrow', label: 'Growth Up' },
    { id: 'pie-chart', label: 'Chart' },
    { id: 'bar-chart', label: 'Bar Chart' },
    { id: 'clipboard-data', label: 'Data' },
    { id: 'trophy', label: 'Trophy' },
    { id: 'rocket', label: 'Rocket' },
    { id: 'rocket-fill', label: 'Rocket Filled' },
    { id: 'lightning', label: 'Energy' },
    { id: 'lightning-fill', label: 'Energy Filled' },
    { id: 'gear', label: 'Settings' },
    { id: 'tools', label: 'Tools' },
    { id: 'wrench', label: 'Wrench' },
    { id: 'hammer', label: 'Build' },
    { id: 'paint-bucket', label: 'Paint' },
    { id: 'palette', label: 'Palette' },
    { id: 'music-note', label: 'Music' },
    { id: 'camera', label: 'Camera' },
    { id: 'image', label: 'Image' },
    { id: 'film', label: 'Film' },
    { id: 'mic', label: 'Microphone' },
    { id: 'telephone', label: 'Phone' },
    { id: 'envelope', label: 'Email' },
    { id: 'envelope-fill', label: 'Email Filled' },
    { id: 'chat', label: 'Chat' },
    { id: 'chat-fill', label: 'Chat Filled' },
    { id: 'share', label: 'Share' },
    { id: 'link', label: 'Link' },
    { id: 'link-45deg', label: 'External Link' },
    { id: 'atsapp', label: 'WhatsApp' },
    { id: 'award', label: 'Award' },
    { id: 'award-fill', label: 'Award Filled' },
    { id: 'badge', label: 'Badge' },
    { id: 'badge-fill', label: 'Badge Filled' },
    { id: 'journal', label: 'Journal' },
    { id: 'journal-text', label: 'Journal Text' },
    { id: 'newspaper', label: 'News' },
    { id: 'pencil', label: 'Write' },
    { id: 'pencil-fill', label: 'Write Filled' },
    { id: 'trash', label: 'Delete' },
    { id: 'plus-circle', label: 'Add' },
    { id: 'check-circle', label: 'Complete' },
    { id: 'check-circle-fill', label: 'Complete Filled' },
    { id: 'x-circle', label: 'Cancel' },
    { id: 'exclamation-circle', label: 'Alert' },
    { id: 'info-circle', label: 'Info' },
    { id: 'question-circle', label: 'Help' },
    { id: 'search', label: 'Search' },
    { id: 'filter', label: 'Filter' },
    { id: 'sort-down', label: 'Sort' },
    { id: 'calendar', label: 'Calendar' },
    { id: 'calendar-event', label: 'Event' },
    { id: 'clock', label: 'Clock' },
    { id: 'hourglass', label: 'Time' },
    { id: 'hourglass-split', label: 'Hourglass' },
    { id: 'box', label: 'Box' },
    { id: 'archive', label: 'Archive' },
    { id: 'box-seam', label: 'Package' },
    { id: 'truck', label: 'Transport' },
    { id: 'bus-front', label: 'Bus' },
    { id: 'airplane', label: 'Travel' },
    { id: 'globe2', label: 'Global' },
    { id: 'wifi', label: 'Connected' },
    { id: 'phone', label: 'Mobile' },
    { id: 'laptop', label: 'Laptop' },
    { id: 'desktop', label: 'Desktop' },
    { id: 'printer', label: 'Print' },
    { id: 'upc-scan', label: 'Scan' },
];

export default function IconPicker({ value, onChange }) {
    const [open, setOpen] = useState(false);
    const [search, setSearch] = useState('');
    const containerRef = useRef(null);
    const searchRef = useRef(null);
    const [dropdownStyle, setDropdownStyle] = useState({});

    const filtered = ICONS.filter(icon =>
        icon.id.toLowerCase().includes(search.toLowerCase()) ||
        icon.label.toLowerCase().includes(search.toLowerCase())
    );

    const updatePosition = useCallback(() => {
        if (!containerRef.current) return;
        const rect = containerRef.current.getBoundingClientRect();
        const spaceBelow = window.innerHeight - rect.bottom;
        const ddHeight = 320;
        setDropdownStyle({
            position: 'fixed',
            left: rect.left,
            ...(spaceBelow < ddHeight ? { bottom: window.innerHeight - rect.top + 4 } : { top: rect.bottom + 4 }),
            width: Math.max(rect.width, 340),
            zIndex: 9999,
        });
    }, []);

    useEffect(() => {
        if (open) {
            updatePosition();
            requestAnimationFrame(() => searchRef.current?.focus({ preventScroll: true }));
        }
    }, [open, updatePosition]);

    useEffect(() => {
        if (!open) return;
        const handler = (e) => {
            if (containerRef.current && !containerRef.current.contains(e.target)) setOpen(false);
        };
        document.addEventListener('mousedown', handler);
        return () => document.removeEventListener('mousedown', handler);
    }, [open]);

    const selectedIcon = ICONS.find(i => i.id === value);

    return (
        <div ref={containerRef} style={{ position: 'relative' }}>
            <label className="form-label">Icon</label>
            <button
                type="button"
                className="form-control text-start d-flex align-items-center gap-2"
                onClick={() => setOpen(!open)}
                style={{ height: 'var(--admin-input-height)' }}
            >
                {selectedIcon ? (
                    <>
                        <i className={`bi bi-${selectedIcon.id}`} style={{ fontSize: '1.15rem' }}></i>
                        <span className="text-truncate">{selectedIcon.label}</span>
                        <span className="ms-auto text-muted small">bi-{selectedIcon.id}</span>
                    </>
                ) : (
                    <span className="text-muted">Select an icon...</span>
                )}
                <i className="bi bi-chevron-down ms-auto" style={{ fontSize: '0.7rem', opacity: 0.5 }}></i>
            </button>
            {open && (
                <div className="icon-picker-dropdown" style={dropdownStyle}>
                    <div className="icon-picker-search">
                        <i className="bi bi-search"></i>
                        <input
                            ref={searchRef}
                            type="text"
                            placeholder="Search icons..."
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                        />
                        {search && (
                            <button type="button" className="icon-picker-clear" onClick={() => setSearch('')}>
                                <i className="bi bi-x"></i>
                            </button>
                        )}
                    </div>
                    <div className="icon-picker-grid">
                        {filtered.map(icon => (
                            <button
                                key={icon.id}
                                type="button"
                                className={`icon-picker-item ${value === icon.id ? 'active' : ''}`}
                                title={icon.label}
                                onClick={() => { onChange(icon.id); setOpen(false); setSearch(''); }}
                            >
                                <i className={`bi bi-${icon.id}`}></i>
                                <span>{icon.label}</span>
                            </button>
                        ))}
                        {filtered.length === 0 && (
                            <div className="icon-picker-empty">No icons found</div>
                        )}
                    </div>
                    {value && (
                        <div className="icon-picker-footer">
                            <button type="button" className="btn btn-sm btn-outline-secondary" onClick={() => { onChange(''); setOpen(false); setSearch(''); }}>
                                <i className="bi bi-x-circle me-1"></i>Clear
                            </button>
                        </div>
                    )}
                </div>
            )}
            <input type="hidden" name="icon" value={value || ''} readOnly />
        </div>
    );
}
