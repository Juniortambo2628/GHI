import PublicLayout from '../Layouts/PublicLayout';
import sanitizeHtml from '../Components/Shared/sanitizeHtml';
import mediaUrl from '../Components/Shared/mediaUrl';
import stripHtml from '../Components/Shared/stripHtml';
import FallbackImage from '../Components/Shared/FallbackImage';
import StatusBadge from '../Components/Shared/StatusBadge';
import ShowPageLayout from '../Components/Shared/ShowPageLayout';
import { Head, Link } from '@inertiajs/react';

StoryShow.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function StoryShow({ story }) {
    if (!story) {
        return (
            <div className="container py-5 text-center">
                <h1>Story not found</h1>
                <a href="/stories" className="btn btn-primary mt-3">Back to Stories</a>
            </div>
        );
    }

    const storyDate = story.created_at
        ? new Date(story.created_at).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })
        : '';

    const storyImage = story.image || story.featured_image;
    const event = story.event || null;
    const totalImpact = event?.impact_activities?.reduce((sum, a) => sum + (a.people_affected || 0), 0) || 0;

    const chainItems = [];
    if (event?.initiative?.causes) {
        event.initiative.causes.forEach(c => chainItems.push({ label: c.title, url: `/causes/${c.slug}` }));
    }
    if (event?.initiative) {
        chainItems.push({ label: event.initiative.title, url: `/initiatives/${event.initiative.slug}` });
    }
    if (event) {
        chainItems.push({ label: event.title, url: `/events/${event.slug}` });
    }
    chainItems.push({ label: story.title || 'This Story', url: null });

    return (
        <>
            <Head title={`${story.title || 'Story'} - Global Harmony Initiative`} />

            <ShowPageLayout title={story.title || 'Story'} section="stories" sectionLabel="Stories" sectionUrl="/stories" image={storyImage}
                sidebar={
                    <>
                        <div className="bg-light p-4 rounded mb-4">
                            <h5 className="mb-3">Story Details</h5>
                            <ul className="list-unstyled">
                                {story.author && <li className="mb-2"><i className="bi bi-person me-2"></i>{story.author}</li>}
                                {storyDate && <li className="mb-2"><i className="bi bi-calendar me-2"></i>{storyDate}</li>}
                                {story.category && <li className="mb-2"><strong>Category:</strong> <span className="badge bg-primary">{story.category}</span></li>}
                                {story.status && <li className="mb-2"><strong>Status:</strong> <StatusBadge status={story.status} /></li>}
                            </ul>
                        </div>
                        {event && (
                            <div className="bg-light p-4 rounded mb-4">
                                <h5 className="mb-3">Related Event</h5>
                                <ul className="list-unstyled">
                                    <li className="mb-2"><strong><Link href={`/events/${event.slug}`}>{event.title}</Link></strong></li>
                                    {event.event_date && <li className="mb-2"><i className="bi bi-calendar me-2"></i>{new Date(event.event_date).toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' })}</li>}
                                    {event.location && <li className="mb-2"><i className="bi bi-geo-alt me-2"></i>{event.location}</li>}
                                    {event.initiative && <li className="mb-2"><strong>Initiative:</strong> <Link href={`/initiatives/${event.initiative.slug}`}>{event.initiative.title}</Link></li>}
                                    {event.initiative?.causes && event.initiative.causes.length > 0 && (
                                        <li className="mb-2"><strong>Cause{event.initiative.causes.length > 1 ? 's' : ''}:</strong> {event.initiative.causes.map(c => <Link key={c.id} href={`/causes/${c.slug}`} className="me-1">{c.title}</Link>)}</li>
                                    )}
                                </ul>
                            </div>
                        )}
                        {event?.impact_activities && event.impact_activities.length > 0 && (
                            <div className="bg-light p-4 rounded">
                                <h5 className="mb-3">Impact Summary</h5>
                                <ul className="list-unstyled">
                                    <li className="mb-2"><strong>Total Lives Impacted:</strong> {Math.floor(totalImpact).toLocaleString()}+</li>
                                    {event.impact_activities.map(a => (
                                        <li key={a.id} className="mb-2">
                                            <Link href={`/impact/${a.slug}`}>{a.title}</Link>
                                            <span className="text-muted ms-1">({Math.floor(Number(a.people_affected) || 0).toLocaleString()}+)</span>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        )}
                    </>
                }>
                {chainItems.length > 1 && (
                    <nav aria-label="relationship chain" className="mb-4">
                        <ol className="breadcrumb glass-pill-breadcrumb mb-0">
                            {chainItems.map((item, idx) => (
                                <li key={idx} className={`breadcrumb-item ${idx === chainItems.length - 1 ? 'active' : ''}`} aria-current={idx === chainItems.length - 1 ? 'page' : undefined}>
                                    {item.url ? <Link href={item.url}>{item.label}</Link> : <strong>{item.label}</strong>}
                                </li>
                            ))}
                        </ol>
                    </nav>
                )}
                <div className="mb-4 story-content" dangerouslySetInnerHTML={{ __html: sanitizeHtml(story.content || '') }} />
            </ShowPageLayout>
        </>
    );
}
