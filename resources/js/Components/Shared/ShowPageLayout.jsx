import { Link } from '@inertiajs/react';
import mediaUrl from './mediaUrl';

export default function ShowPageLayout({ title, section, sectionLabel, sectionUrl, children, sidebar, image }) {
    const bgStyle = image ? { backgroundImage: `url(${mediaUrl(image)})`, backgroundSize: 'cover', backgroundPosition: 'center' } : {};

    return (
        <>
            <div className={`container-fluid page-header hero-${section} mb-5`} style={bgStyle}>
                {image && <div className="page-header-overlay"></div>}
                <div className="container py-5 position-relative">
                    <nav aria-label="breadcrumb" className="mb-4">
                        <ol className="breadcrumb glass-pill-breadcrumb">
                            <li className="breadcrumb-item"><Link href="/">Home</Link></li>
                            <li className="breadcrumb-item"><Link href={sectionUrl}>{sectionLabel}</Link></li>
                            <li className="breadcrumb-item active" aria-current="page">{title}</li>
                        </ol>
                    </nav>
                    <h1 className="display-3 text-white mb-3">{title}</h1>
                </div>
            </div>

            <div className="container py-5">
                <div className="row g-5">
                    <div className="col-lg-8">
                        {children}
                    </div>
                    {sidebar && (
                        <div className="col-lg-4">
                            {sidebar}
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
