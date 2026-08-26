const sizes = {
    sm: { width: '1.5rem', height: '1.5rem', fontSize: '0.6rem' },
    md: {},
    lg: { width: '2.5rem', height: '2.5rem', fontSize: '0.85rem' },
};

export default function AdminAvatar({ name, size = 'md', className = '' }) {
    const initials = (name || 'A')
        .split(/[\s@.]+/)
        .filter(Boolean)
        .slice(0, 2)
        .map(w => w[0]?.toUpperCase())
        .join('');

    return (
        <div className={`admin-avatar ${className}`} style={sizes[size] || undefined}>
            {initials}
        </div>
    );
}
