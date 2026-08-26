import { Link } from '@inertiajs/react';
import mediaUrl from './mediaUrl';

export default function PageHeader({ title, subtitle, image, buttonText, buttonUrl, breadcrumb = [] }) {
    const bgStyle = image ? { backgroundImage: `url(${mediaUrl(image)})`, backgroundSize: 'cover', backgroundPosition: 'center' } : {};

    return (
        <div className="container-fluid page-header hero-section mb-5" style={bgStyle}>
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
                {subtitle && <p className="lead text-white mb-4" style={{ maxWidth: '600px' }}>{subtitle}</p>}
                {buttonText && buttonUrl && (
                    <Link href={buttonUrl} className="btn btn-primary btn-lg">{buttonText}</Link>
                )}
            </div>
        </div>
    );
}
