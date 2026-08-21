export default function GlassPill({ children, className = '' }) {
    return (
        <span className={`glass-pill ${className}`}>
            {children}
        </span>
    );
}

export function GlassPillSm({ children, className = '' }) {
    return (
        <span className={`glass-pill-sm ${className}`}>
            {children}
        </span>
    );
}
