import PublicLayout from '../Layouts/PublicLayout';
import sanitizeHtml from '../Components/Shared/sanitizeHtml';
import mediaUrl from '../Components/Shared/mediaUrl';
import FallbackImage from '../Components/Shared/FallbackImage';
import StatusBadge from '../Components/Shared/StatusBadge';
import ShowPageLayout from '../Components/Shared/ShowPageLayout';
import { Head, Link } from '@inertiajs/react';

InitiativeShow.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function InitiativeShow({ initiative, events }) {
    return (
        <>
            <Head>
                <title>{initiative.title} - Global Harmony Initiative</title>
            </Head>

            <ShowPageLayout title={initiative.title} section="initiatives" sectionLabel="Initiatives" sectionUrl="/initiatives"
                sidebar={
                    <div className="bg-light p-4 rounded mb-4">
                        <h5 className="mb-3">Details</h5>
                        <ul className="list-unstyled">
                            <li className="mb-2"><strong>Category:</strong> {initiative.category}</li>
                            <li className="mb-2"><strong>Status:</strong> <StatusBadge status={initiative.status} /></li>
                            {initiative.cause && <li className="mb-2"><strong>Cause:</strong> {initiative.cause.title}</li>}
                        </ul>
                    </div>
                }>
                {initiative.image && (
                    <FallbackImage src={mediaUrl(initiative.image)} className="img-fluid rounded mb-4 w-100" alt={initiative.title} />
                )}
                <div className="mb-4">
                    {initiative.description}
                </div>
                {initiative.content && (
                    <div className="mb-4" dangerouslySetInnerHTML={{ __html: sanitizeHtml(initiative.content) }} />
                )}
            </ShowPageLayout>

            {events && events.length > 0 && (
                <div className="container py-5">
                    <div className="mt-5">
                        <h3 className="mb-4">Upcoming Events</h3>
                        <div className="row g-4">
                            {events.map((event, idx) => {
                                const eventDate = new Date(event.event_date);
                                return (
                                    <div key={idx} className="col-md-6">
                                        <div className="card h-100">
                                            {event.image && <FallbackImage src={mediaUrl(event.image)} className="card-img-top" alt={event.title} />}
                                            <div className="card-body">
                                                <h5 className="card-title">{event.title}</h5>
                                                <p className="text-muted"><i className="bi bi-calendar me-1"></i>{eventDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p>
                                                <p className="card-text">{(event.description || '').substring(0, 100)}...</p>
                                                <Link href={`/events/${event.slug || event.id}`} className="btn btn-outline-primary">View Event</Link>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}
