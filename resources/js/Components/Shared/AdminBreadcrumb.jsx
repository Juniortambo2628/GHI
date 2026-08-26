import { Link } from '@inertiajs/react';

export default function AdminBreadcrumb({ items = [] }) {
    if (!items.length) return null;

    return (
        <nav className="admin-breadcrumb" aria-label="Breadcrumb">
            <Link href="/admin"><i className="bi bi-house-fill"></i></Link>
            {items.map((item, idx) => (
                <span key={idx} className="d-flex align-items-center gap-1">
                    <span className="separator">/</span>
                    {item.href ? (
                        <Link href={item.href}>{item.label}</Link>
                    ) : (
                        <span>{item.label}</span>
                    )}
                </span>
            ))}
        </nav>
    );
}
