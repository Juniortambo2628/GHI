import PublicLayout from '../Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';

ImpactShow.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function ImpactShow({ impactActivity }) {
    return (
        <>
            <Head>
                <title>{impactActivity.title} - Global Harmony Initiative</title>
            </Head>

            <div className="container-fluid page-header hero-impact mb-5">
                <div className="container py-5">
                    <nav aria-label="breadcrumb animated slideInDown mb-4">
                        <ol className="breadcrumb">
                            <li className="breadcrumb-item"><Link href="/">Home</Link></li>
                            <li className="breadcrumb-item"><Link href="/impact">Impact</Link></li>
                            <li className="breadcrumb-item active" aria-current="page">{impactActivity.title}</li>
                        </ol>
                    </nav>
                    <h1 className="display-3 text-white mb-3 animated slideInDown">{impactActivity.title}</h1>
                </div>
            </div>

            <div className="container py-5">
                <div className="row g-5">
                    <div className="col-lg-8">
                        {impactActivity.image && (
                            <img src={`/uploads/images/${impactActivity.image}`} className="img-fluid rounded mb-4 w-100" alt={impactActivity.title} />
                        )}
                        <div className="mb-4">
                            {impactActivity.description}
                        </div>
                        {impactActivity.outcome_summary && (
                            <div className="mb-4">
                                <h4>Outcome Summary</h4>
                                <p>{impactActivity.outcome_summary}</p>
                            </div>
                        )}
                    </div>
                    <div className="col-lg-4">
                        <div className="bg-light p-4 rounded mb-4">
                            <h5 className="mb-3">Impact Details</h5>
                            <ul className="list-unstyled">
                                <li className="mb-2"><i className="bi bi-people me-2"></i>{impactActivity.people_affected?.toLocaleString() || 0} Lives Impacted</li>
                                {impactActivity.activity_date && <li className="mb-2"><i className="bi bi-calendar me-2"></i>{new Date(impactActivity.activity_date).toLocaleDateString()}</li>}
                                {impactActivity.location && <li className="mb-2"><i className="bi bi-geo-alt me-2"></i>{impactActivity.location}</li>}
                                {impactActivity.metric_type && <li className="mb-2"><strong>Metric:</strong> {impactActivity.metric_type}: {impactActivity.metric_value}</li>}
                                <li className="mb-2"><strong>Status:</strong> <span className={`badge bg-${impactActivity.status === 'published' ? 'success' : 'warning'}`}>{impactActivity.status}</span></li>
                            </ul>
                        </div>
                        {impactActivity.event && (
                            <div className="bg-light p-4 rounded">
                                <h5 className="mb-3">Related Event</h5>
                                <Link href={`/events/${impactActivity.event.slug || impactActivity.event.id}`}>{impactActivity.event.title}</Link>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}
