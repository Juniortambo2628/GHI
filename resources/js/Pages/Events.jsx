import PublicLayout from '../Layouts/PublicLayout';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import { mediaUrl } from '../Components/Shared/ImageUploadField';
import PageHeader from '../Components/Shared/PageHeader';
import SearchSidebar from '../Components/Shared/SearchSidebar';
import ResultsCount from '../Components/Shared/ResultsCount';
import ListingCard from '../Components/Shared/ListingCard';
import ListingRow from '../Components/Shared/ListingRow';
import TimelineCard from '../Components/Shared/TimelineCard';
import ListingCardGrid from '../Components/Shared/ListingCardGrid';
import Pagination from '../Components/Shared/Pagination';

Events.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function Events({ events, hero }) {
    const [search, setSearch] = useState('');
    const [upcoming, setUpcoming] = useState(false);
    const [past, setPast] = useState(false);
    const [sortBy, setSortBy] = useState('');
    const [viewMode, setViewMode] = useState('timeline');

    const handleSubmit = (e) => {
        e.preventDefault();
        const params = { search, sort: sortBy };
        if (upcoming) params.upcoming = 1;
        if (past) params.past = 1;
        router.get('/events', params, { preserveState: true });
    };

    const renderCard = (event, idx, mode) => {
        const eventDate = new Date(event.event_date);
        const isUpcoming = eventDate >= new Date();
        const props = {
            key: event.id,
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
                { icon: 'bi bi-calendar', text: eventDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) },
                { icon: 'bi bi-geo-alt', text: event.location }
            ],
        };
        if (mode === 'list') return <ListingRow {...props} />;
        if (mode === 'timeline') return <TimelineCard {...props} side={idx % 2 === 0 ? 'left' : 'right'} />;
        return <ListingCard {...props} />;
    };

    return (
        <>
            <Head>
                <title>Events & Activities - Global Harmony Initiative</title>
                <meta name="description" content="Discover our upcoming events and activities." />
            </Head>
            <PageHeader title={hero?.hero_events_title || 'Events & Activities'} subtitle={hero?.hero_events_subtitle} image={hero?.hero_events_image} buttonText={hero?.hero_events_button_text} buttonUrl={hero?.hero_events_button_url} breadcrumb={[{ label: 'Events & Activities' }]} />
            <div className="container-fluid px-5">
                <div className="row g-4">
                    <SearchSidebar title="Search & Filter" onSubmit={handleSubmit} viewMode={viewMode} setViewMode={setViewMode}>
                        <input type="text" name="search" className="form-control mb-3" placeholder="Search events..." value={search} onChange={e => setSearch(e.target.value)} />
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
                    </SearchSidebar>
                    <div className="col-lg-9">
                        <ResultsCount data={events} />
                        <ListingCardGrid data={events} emptyMessage="No events found." viewMode={viewMode}>
                            {renderCard}
                        </ListingCardGrid>
                        <Pagination data={events} />
                    </div>
                </div>
            </div>
        </>
    );
}
