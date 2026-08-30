import { Link } from '@inertiajs/react';
import mediaUrl from './mediaUrl';
import stripHtml from './stripHtml';
import FallbackImage from './FallbackImage';

function MiniCard({ title, description, image, link, badge }) {
    return (
        <div className="col-6 col-md-4 col-lg-3">
            <Link href={link} className="text-decoration-none">
                <div className="card h-100 border-0 shadow-sm related-chain-card">
                    <div className="related-chain-img-wrapper">
                        <FallbackImage src={image ? mediaUrl(image) : null} className="card-img-top" alt={title} />
                    </div>
                    <div className="card-body p-3">
                        {badge && <small className="text-uppercase text-primary fw-bold" style={{ fontSize: '0.65rem', letterSpacing: '0.08em' }}>{badge}</small>}
                        <h6 className="card-title mt-1 mb-1 text-dark">{title}</h6>
                        {description && <p className="card-text text-muted small mb-0">{stripHtml(description).substring(0, 60)}{stripHtml(description).length > 60 ? '...' : ''}</p>}
                    </div>
                </div>
            </Link>
        </div>
    );
}

function ChainBreadcrumb({ items }) {
    return (
        <nav aria-label="relationship chain" className="mb-4">
            <ol className="breadcrumb glass-pill-breadcrumb mb-0">
                {items.map((item, idx) => (
                    <li key={idx} className={`breadcrumb-item ${idx === items.length - 1 ? 'active' : ''}`} aria-current={idx === items.length - 1 ? 'page' : undefined}>
                        {item.url ? <Link href={item.url}>{item.label}</Link> : <strong>{item.label}</strong>}
                    </li>
                ))}
            </ol>
        </nav>
    );
}

export default function RelatedChain({ currentType, cause, initiative, event, impactActivity, impactActivities = [], initiatives = [], events = [] }) {
    const sections = [];

    if (currentType === 'cause') {
        sections.push({ title: 'Initiatives', items: initiatives.map(i => ({ title: i.title, description: i.description, image: i.image, link: `/initiatives/${i.slug}`, badge: i.category })), empty: 'No initiatives yet.' });
        const allEvents = initiatives.flatMap(i => (i.events || []).map(e => ({ ...e, initiativeTitle: i.title })));
        if (allEvents.length > 0) sections.push({ title: 'Events', items: allEvents.slice(0, 8).map(e => ({ title: e.title, description: e.description, image: e.image, link: `/events/${e.slug}`, badge: e.initiativeTitle })), empty: '' });
        const allImpact = initiatives.flatMap(i => (i.events || []).flatMap(e => (e.impactActivities || []).map(a => ({ ...a, eventTitle: e.title }))));
        if (allImpact.length > 0) sections.push({ title: 'Impact Activities', items: allImpact.slice(0, 8).map(a => ({ title: a.title, description: a.description, image: a.image, link: `/impact/${a.slug}`, badge: `${Math.floor(Number(a.people_affected) || 0).toLocaleString()}+ lives` })), empty: '' });
    }

    if (currentType === 'initiative') {
        if (cause) {
            sections.push({ title: 'Related Cause', items: (Array.isArray(cause) ? cause : [cause]).map(c => ({ title: c.title, description: c.description, image: c.image, link: `/causes/${c.slug}`, badge: 'Cause' })), empty: '' });
        }
        sections.push({ title: 'Events', items: events.map(e => ({ title: e.title, description: e.description, image: e.image, link: `/events/${e.slug}`, badge: new Date(e.event_date) >= new Date() ? 'Upcoming' : 'Past' })), empty: 'No events yet.' });
        const allImpact = events.flatMap(e => (e.impactActivities || []).map(a => ({ ...a, eventTitle: e.title })));
        if (allImpact.length > 0) sections.push({ title: 'Impact Activities', items: allImpact.slice(0, 8).map(a => ({ title: a.title, description: a.description, image: a.image, link: `/impact/${a.slug}`, badge: `${Math.floor(Number(a.people_affected) || 0).toLocaleString()}+ lives` })), empty: '' });
    }

    if (currentType === 'event') {
        const chainItems = [];
        if (event?.initiative?.causes) {
            event.initiative.causes.forEach(c => chainItems.push({ label: c.title, url: `/causes/${c.slug}` }));
        }
        if (event?.initiative) {
            chainItems.push({ label: event.initiative.title, url: `/initiatives/${event.initiative.slug}` });
        }
        chainItems.push({ label: event?.title || 'This Event', url: null });
        sections.push({ breadcrumb: chainItems });

        if (impactActivities.length > 0) {
            sections.push({ title: 'Impact Activities', items: impactActivities.map(a => ({ title: a.title, description: a.description, image: a.image, link: `/impact/${a.slug}`, badge: `${Math.floor(Number(a.people_affected) || 0).toLocaleString()}+ lives` })), empty: '' });
        }
    }

    if (currentType === 'impact') {
        const chainItems = [];
        if (impactActivity?.event?.initiative?.causes) {
            impactActivity.event.initiative.causes.forEach(c => chainItems.push({ label: c.title, url: `/causes/${c.slug}` }));
        }
        if (impactActivity?.event?.initiative) {
            chainItems.push({ label: impactActivity.event.initiative.title, url: `/initiatives/${impactActivity.event.initiative.slug}` });
        }
        if (impactActivity?.event) {
            chainItems.push({ label: impactActivity.event.title, url: `/events/${impactActivity.event.slug}` });
        }
        chainItems.push({ label: impactActivity?.title || 'This Impact', url: null });
        sections.push({ breadcrumb: chainItems });
    }

    if (sections.length === 0) return null;

    return (
        <div className="container py-5">
            {sections.map((section, idx) => {
                if (section.breadcrumb) {
                    return <ChainBreadcrumb key={idx} items={section.breadcrumb} />;
                }
                if (section.items.length === 0) return null;
                return (
                    <div key={idx} className="mb-4">
                        <h5 className="mb-3">{section.title}</h5>
                        <div className="row g-3">
                            {section.items.map((item, i) => (
                                <MiniCard key={i} {...item} />
                            ))}
                        </div>
                    </div>
                );
            })}
        </div>
    );
}
