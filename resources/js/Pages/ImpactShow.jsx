import PublicLayout from '../Layouts/PublicLayout';
import sanitizeHtml from '../Components/Shared/sanitizeHtml';
import mediaUrl from '../Components/Shared/mediaUrl';
import FallbackImage from '../Components/Shared/FallbackImage';
import StatusBadge from '../Components/Shared/StatusBadge';
import ShowPageLayout from '../Components/Shared/ShowPageLayout';
import RelatedChain from '../Components/Shared/RelatedChain';
import { Head, Link } from '@inertiajs/react';

ImpactShow.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function ImpactShow({ impactActivity }) {
    const activityDate = impactActivity.activity_date
        ? new Date(impactActivity.activity_date).toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' })
        : '';

    return (
        <>
            <Head title={`${impactActivity.title} - Global Harmony Initiative`} />

            <ShowPageLayout title={impactActivity.title} section="impact" sectionLabel="Impact" sectionUrl="/impact" image={impactActivity.image}
                sidebar={
                    <>
                        <div className="bg-light p-4 rounded mb-4">
                            <h5 className="mb-3">Impact Details</h5>
                            <ul className="list-unstyled">
                                <li className="mb-2"><i className="bi bi-people me-2"></i>{Math.floor(Number(impactActivity.people_affected) || 0).toLocaleString()}+ Lives Impacted</li>
                                {activityDate && <li className="mb-2"><i className="bi bi-calendar me-2"></i>{activityDate}</li>}
                                {impactActivity.location && <li className="mb-2"><i className="bi bi-geo-alt me-2"></i>{impactActivity.location}</li>}
                                {impactActivity.metric_type && <li className="mb-2"><strong>Metric:</strong> {(impactActivity.metric_type || '').replace(/_/g, ' ')}: {Math.floor(Number(impactActivity.metric_value) || 0).toLocaleString()}+</li>}
                                <li className="mb-2"><strong>Status:</strong> <StatusBadge status={impactActivity.status} /></li>
                            </ul>
                        </div>
                        {impactActivity.event && (
                            <div className="bg-light p-4 rounded mb-4">
                                <h5 className="mb-3">Related Event</h5>
                                <p className="mb-2">{impactActivity.event.title}</p>
                                <Link href={`/events/${impactActivity.event.slug || impactActivity.event.id}`} className="btn btn-outline-primary btn-sm">View Event</Link>
                            </div>
                        )}
                    </>
                }>
                <div className="mb-4">
                    {impactActivity.description}
                </div>
                {impactActivity.outcome_summary && (
                    <div className="mb-4 p-4 bg-light rounded">
                        <h4><i className="bi bi-check-circle text-success me-2"></i>Outcome Summary</h4>
                        <p className="mb-0">{impactActivity.outcome_summary}</p>
                    </div>
                )}
                {impactActivity.metric_type && (
                    <div className="mb-4 p-4 bg-light rounded text-center">
                        <h2 className="text-primary mb-1">{Math.floor(Number(impactActivity.metric_value) || 0).toLocaleString()}+</h2>
                        <p className="text-muted mb-0">{(impactActivity.metric_type || '').replace(/_/g, ' ')}</p>
                    </div>
                )}
            </ShowPageLayout>

            <RelatedChain currentType="impact" impactActivity={impactActivity} />
        </>
    );
}
