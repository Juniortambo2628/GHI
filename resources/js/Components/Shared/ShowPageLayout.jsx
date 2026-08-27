import { Link } from '@inertiajs/react';

export default function ShowPageLayout({ title, section, sectionLabel, sectionUrl, children, sidebar }) {
    return (
        <>
            <div className={`container-fluid page-header hero-${section} mb-5`}>
                <div className="container py-5">
                    <nav aria-label="breadcrumb animated slideInDown mb-4">
                        <ol className="breadcrumb">
                            <li className="breadcrumb-item"><Link href="/">Home</Link></li>
                            <li className="breadcrumb-item"><Link href={sectionUrl}>{sectionLabel}</Link></li>
                            <li className="breadcrumb-item active" aria-current="page">{title}</li>
                        </ol>
                    </nav>
                    <h1 className="display-3 text-white mb-3 animated slideInDown">{title}</h1>
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
