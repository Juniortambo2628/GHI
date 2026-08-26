import PublicLayout from '../Layouts/PublicLayout';
import { mediaUrl } from '../Components/Shared/ImageUploadField';
import FallbackImage from '../Components/Shared/FallbackImage';
import StatusBadge from '../Components/Shared/StatusBadge';
import { Head, Link } from '@inertiajs/react';

CauseShow.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function CauseShow({ cause, initiatives }) {
    return (
        <>
            <Head>
                <title>{cause.title} - Global Harmony Initiative</title>
            </Head>

            <div className="container-fluid page-header hero-causes mb-5">
                <div className="container py-5">
                    <nav aria-label="breadcrumb animated slideInDown mb-4">
                        <ol className="breadcrumb">
                            <li className="breadcrumb-item"><Link href="/">Home</Link></li>
                            <li className="breadcrumb-item"><Link href="/causes">Causes</Link></li>
                            <li className="breadcrumb-item active" aria-current="page">{cause.title}</li>
                        </ol>
                    </nav>
                    <h1 className="display-3 text-white mb-3 animated slideInDown">{cause.title}</h1>
                </div>
            </div>

            <div className="container py-5">
                <div className="row g-5">
                    <div className="col-lg-8">
                        {cause.image && (
                            <FallbackImage src={mediaUrl(cause.image)} className="img-fluid rounded mb-4 w-100" alt={cause.title} />
                        )}
                        {cause.quote && (
                            <blockquote className="blockquote mb-4">
                                <p className="fst-italic text-primary">"{cause.quote}"</p>
                            </blockquote>
                        )}
                        <div className="mb-4">
                            {cause.description}
                        </div>
                    </div>
                    <div className="col-lg-4">
                        <div className="bg-light p-4 rounded mb-4">
                            <h5 className="mb-3">Details</h5>
                            <ul className="list-unstyled">
                                <li className="mb-2"><strong>Status:</strong> <StatusBadge status={cause.status} /></li>
                                {cause.icon && <li className="mb-2"><strong>Icon:</strong> <i className={`bi bi-${cause.icon}`}></i> {cause.icon}</li>}
                            </ul>
                        </div>
                    </div>
                </div>

                {initiatives && initiatives.length > 0 && (
                    <div className="mt-5">
                        <h3 className="mb-4">Related Initiatives</h3>
                        <div className="row g-4">
                            {initiatives.map((init, idx) => (
                                <div key={idx} className="col-md-6 col-lg-4">
                                    <div className="card h-100">
                                        {init.image && <FallbackImage src={mediaUrl(init.image)} className="card-img-top" alt={init.title} />}
                                        <div className="card-body">
                                            <h5 className="card-title">{init.title}</h5>
                                            <p className="card-text">{(init.description || '').substring(0, 100)}...</p>
                                            <Link href={`/initiatives/${init.slug || init.id}`} className="btn btn-outline-primary">View Initiative</Link>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </>
    );
}
