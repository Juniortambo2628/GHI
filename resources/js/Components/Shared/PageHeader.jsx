import { Link } from '@inertiajs/react';

export default function PageHeader({ title, breadcrumb = [] }) {
    return (
        <div className="container-fluid page-header hero-section mb-5">
            <div className="page-header-overlay"></div>
            <div className="container py-5 position-relative">
                <nav aria-label="breadcrumb" className="mb-4">
                    <ol className="breadcrumb glass-pill-breadcrumb">
                        <li className="breadcrumb-item"><Link href="/">Home</Link></li>
                        {breadcrumb.map((item, idx) => (
                            <li key={idx} className="breadcrumb-item">
                                {item.href ? <Link href={item.href}>{item.label}</Link> : <span className="active">{item.label}</span>}
                            </li>
                        ))}
                    </ol>
                </nav>
                <h1 className="display-3 text-white mb-3">{title}</h1>
            </div>
        </div>
    );
}
