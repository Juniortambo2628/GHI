import PublicLayout from '../Layouts/PublicLayout';
import sanitizeHtml from '../Components/Shared/sanitizeHtml';
import mediaUrl from '../Components/Shared/mediaUrl';
import FallbackImage from '../Components/Shared/FallbackImage';
import StatusBadge from '../Components/Shared/StatusBadge';
import ShowPageLayout from '../Components/Shared/ShowPageLayout';
import RelatedChain from '../Components/Shared/RelatedChain';
import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import GalleryLightbox from '../Components/Shared/GalleryLightbox';

EventShow.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function EventShow({ event, impactActivities }) {
    const eventDate = new Date(event.event_date);
    const [lightbox, setLightbox] = useState(null);
    const images = event.images || [];

    return (
        <>
            <Head title={`${event.title} - Global Harmony Initiative`} />

            <ShowPageLayout title={event.title} section="events" sectionLabel="Events" sectionUrl="/events" image={event.image}
                sidebar={
                    <>
                        <div className="bg-light p-4 rounded mb-4">
                            <h5 className="mb-3">Event Details</h5>
                            <ul className="list-unstyled">
                                <li className="mb-2"><i className="bi bi-calendar me-2"></i>{eventDate.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' })}</li>
                                <li className="mb-2"><i className="bi bi-clock me-2"></i>{eventDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' })}</li>
                                {event.location && <li className="mb-2"><i className="bi bi-geo-alt me-2"></i>{event.location}</li>}
                                {event.initiative && <li className="mb-2"><strong>Initiative:</strong> <Link href={`/initiatives/${event.initiative.slug}`}>{event.initiative.title}</Link></li>}
                                {event.initiative?.causes && event.initiative.causes.length > 0 && (
                                    <li className="mb-2"><strong>Cause{event.initiative.causes.length > 1 ? 's' : ''}:</strong> {event.initiative.causes.map(c => <Link key={c.id} href={`/causes/${c.slug}`} className="me-1">{c.title}</Link>).reduce((a, b) => [a, ', ', b])}</li>
                                )}
                                <li className="mb-2"><strong>Status:</strong> <StatusBadge status={event.status} /></li>
                            </ul>
                        </div>
                        {impactActivities && impactActivities.length > 0 && (
                            <div className="bg-light p-4 rounded">
                                <h5 className="mb-3">Impact Summary</h5>
                                <ul className="list-unstyled">
                                    <li className="mb-2"><strong>Total Activities:</strong> {impactActivities.length}</li>
                                    <li className="mb-2"><strong>Total Lives Impacted:</strong> {Math.floor(impactActivities.reduce((sum, a) => sum + (a.people_affected || 0), 0)).toLocaleString()}+</li>
                                </ul>
                            </div>
                        )}
                    </>
                }>
                <div className="mb-4">
                    {event.description}
                </div>
                {event.content && (
                    <div className="mb-4" dangerouslySetInnerHTML={{ __html: sanitizeHtml(event.content) }} />
                )}
            </ShowPageLayout>

            {images.length > 0 && (
                <div className="container pb-5">
                    <h3 className="mb-4">Event Gallery</h3>
                    <div className="row g-3">
                        {images.map((img, idx) => (
                            <div key={img.id || idx} className="col-6 col-md-4 col-lg-3">
                                <div className="gallery-event-card" onClick={() => setLightbox(idx)}>
                                    {img.type === 'video' ? (
                                        <video src={mediaUrl(img.path)} muted loop playsInline preload="metadata" />
                                    ) : (
                                        <img src={mediaUrl(img.path)} alt={img.alt || event.title} />
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>
                    {lightbox !== null && (
                        <GalleryLightbox images={images} currentIndex={lightbox} onClose={() => setLightbox(null)} onPrev={() => setLightbox((lightbox - 1 + images.length) % images.length)} onNext={() => setLightbox((lightbox + 1) % images.length)} />
                    )}
                </div>
            )}

            <RelatedChain currentType="event" event={event} impactActivities={impactActivities} />
        </>
    );
}
