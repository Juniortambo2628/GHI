import PublicLayout from '../Layouts/PublicLayout';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import mediaUrl from '../Components/Shared/mediaUrl';
import PageHeader from '../Components/Shared/PageHeader';
import ListingCard from '../Components/Shared/ListingCard';
import ListingRow from '../Components/Shared/ListingRow';
import TimelineCard from '../Components/Shared/TimelineCard';
import ListingPageLayout from '../Components/Shared/ListingPageLayout';

Events.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function Events({ events, hero }) {
    const [search, setSearch] = useState('');
    const [upcoming, setUpcoming] = useState(false);
    const [past, setPast] = useState(false);
    const [sortBy, setSortBy] = useState('');
    const [location, setLocation] = useState('');
    const [viewMode, setViewMode] = useState('timeline');

    const handleSubmit = (e) => {
        e.preventDefault();
        const params = { search, location, sort: sortBy };
        if (upcoming) params.upcoming = 1;
        if (past) params.past = 1;
        router.get('/events', params, { preserveState: true });
    };

    const renderCard = (event, idx, mode) => {
        const eventDate = new Date(event.event_date);
        const isUpcoming = eventDate >= new Date();
        const props = {
            index: idx,
            image: event.image ? mediaUrl(event.image) : null,
            imageAlt: event.title,
            badges: [
                { text: event.initiative?.title || 'General' },
                { text: isUpcoming ? 'Upcoming' : 'Past', className: isUpcoming ? 'badge-upcoming' : 'badge-past' }
            ],
            title: event.title,
            description: event.description,
            meta: [
                { icon: 'bi bi-calendar', text: `Date: ${eventDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}` },
                { icon: 'bi bi-geo-alt', text: `Location: ${event.location || 'N/A'}` },
                { icon: 'bi bi-diagram-3', text: `Initiative: ${event.initiative?.title || 'General'}` },
                { icon: 'bi bi-info-circle', text: `Status: ${event.status || 'Published'}` }
            ],
            images: event.images,
            detailContent: event.content,
        };
        if (mode === 'list') return <ListingRow key={event.id} {...props} />;
        if (mode === 'timeline') return <TimelineCard key={event.id} {...props} side={idx % 2 === 0 ? 'left' : 'right'} />;
        return <ListingCard key={event.id} {...props} />;
    };

    return (
        <>
            <Head>
                <title>Events & Activities - Global Harmony Initiative</title>
                <meta name="description" content="Discover our upcoming events and activities." />
            </Head>
            <PageHeader title={hero?.hero_events_title || 'Events & Activities'} subtitle={hero?.hero_events_subtitle} image={hero?.hero_events_image} buttonText={hero?.hero_events_button_text} buttonUrl={hero?.hero_events_button_url} breadcrumb={[{ label: 'Events & Activities' }]} />
            <ListingPageLayout
                data={events}
                emptyMessage="No events found."
                viewMode={viewMode}
                setViewMode={setViewMode}
                onSubmit={handleSubmit}
                filters={
                    <>
                        <input type="text" name="search" className="form-control mb-3" placeholder="Search events..." value={search} onChange={e => setSearch(e.target.value)} />
                        <input type="text" name="location" className="form-control mb-3" placeholder="Filter by location..." value={location} onChange={e => setLocation(e.target.value)} />
                        <div className="form-check mb-2">
                            <input className="form-check-input" type="checkbox" id="upcoming" checked={upcoming} onChange={e => setUpcoming(e.target.checked)} />
                            <label className="form-check-label" htmlFor="upcoming">Upcoming Only</label>
                        </div>
                        <div className="form-check mb-3">
                            <input className="form-check-input" type="checkbox" id="past" checked={past} onChange={e => setPast(e.target.checked)} />
                            <label className="form-check-label" htmlFor="past">Past Events</label>
                        </div>
                        <select name="sort" className="form-select mb-3" value={sortBy} onChange={e => setSortBy(e.target.value)}>
                            <option value="">Sort by</option>
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="alpha">A – Z</option>
                        </select>
                        <button type="submit" className="btn btn-primary w-100">Search</button>
                    </>
                }
                renderCard={renderCard}
            />
        </>
    );
}
