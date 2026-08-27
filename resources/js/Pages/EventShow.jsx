import PublicLayout from '../Layouts/PublicLayout';
import sanitizeHtml from '../Components/Shared/sanitizeHtml';
import mediaUrl from '../Components/Shared/mediaUrl';
import FallbackImage from '../Components/Shared/FallbackImage';
import StatusBadge from '../Components/Shared/StatusBadge';
import ShowPageLayout from '../Components/Shared/ShowPageLayout';
import { Head, Link } from '@inertiajs/react';

EventShow.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function EventShow({ event, impactActivities }) {
    const eventDate = new Date(event.event_date);

    return (
        <>
            <Head>
                <title>{event.title} - Global Harmony Initiative</title>
            </Head>

            <ShowPageLayout title={event.title} section="events" sectionLabel="Events" sectionUrl="/events"
                sidebar={
                    <div className="bg-light p-4 rounded mb-4">
                        <h5 className="mb-3">Event Details</h5>
                        <ul className="list-unstyled">
                            <li className="mb-2"><i className="bi bi-calendar me-2"></i>{eventDate.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' })}</li>
                            <li className="mb-2"><i className="bi bi-clock me-2"></i>{eventDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })}</li>
                            {event.location && <li className="mb-2"><i className="bi bi-geo-alt me-2"></i>{event.location}</li>}
                            {event.initiative && <li className="mb-2"><strong>Initiative:</strong> {event.initiative.title}</li>}
                            <li className="mb-2"><strong>Status:</strong> <StatusBadge status={event.status} /></li>
                        </ul>
                    </div>
                }>
                {event.image && (
                    <FallbackImage src={mediaUrl(event.image)} className="img-fluid rounded mb-4 w-100" alt={event.title} />
                )}
                <div className="mb-4">
                    {event.description}
                </div>
                {event.content && (
                    <div className="mb-4" dangerouslySetInnerHTML={{ __html: sanitizeHtml(event.content) }} />
                )}
            </ShowPageLayout>

            {impactActivities && impactActivities.length > 0 && (
                <div className="container py-5">
                    <div className="mt-5">
                        <h3 className="mb-4">Impact Activities</h3>
                        <div className="row g-4">
                            {impactActivities.map((impact, idx) => (
                                <div key={idx} className="col-md-6 col-lg-4">
                                    <div className="card h-100">
                                        {impact.image && <FallbackImage src={mediaUrl(impact.image)} className="card-img-top" alt={impact.title} />}
                                        <div className="card-body">
                                            <h5 className="card-title">{impact.title}</h5>
                                            <p className="text-muted"><i className="bi bi-people me-1"></i>{impact.people_affected} Lives Impacted</p>
                                            <p className="card-text">{(impact.description || '').substring(0, 100)}...</p>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}
