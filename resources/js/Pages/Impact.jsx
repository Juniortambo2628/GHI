import PublicLayout from '../Layouts/PublicLayout';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import { categoryToObjective } from '../Constants/categoryToObjective';
import mediaUrl from '../Components/Shared/mediaUrl';
import PageHeader from '../Components/Shared/PageHeader';
import ListingCard from '../Components/Shared/ListingCard';
import ListingRow from '../Components/Shared/ListingRow';
import TimelineCard from '../Components/Shared/TimelineCard';
import ListingPageLayout from '../Components/Shared/ListingPageLayout';

Impact.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function Impact({ impactActivities, hero }) {
    const [search, setSearch] = useState('');
    const [sortBy, setSortBy] = useState('');
    const [viewMode, setViewMode] = useState('grid');

    const handleSubmit = (e) => {
        e.preventDefault();
        router.get('/impact', { search, sort: sortBy }, { preserveState: true });
    };

    const renderCard = (impact, idx, mode) => {
        const formattedDate = impact.created_at ? new Date(impact.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '';
        const props = {
            index: idx,
            image: impact.image ? mediaUrl(impact.image) : null,
            imageAlt: impact.title,
            badges: [
                { text: (impact.region || '').charAt(0).toUpperCase() + (impact.region || '').slice(1) },
                { text: categoryToObjective[impact.objective] || 'Community Development', className: 'badge-right' }
            ],
            title: impact.title,
            description: impact.description,
            meta: [
                { icon: 'bi bi-people', text: `${(impact.people_affected || 0).toLocaleString()} Lives Impacted` },
                { icon: 'bi bi-calendar', text: `Activity Date: ${impact.activity_date ? new Date(impact.activity_date).toLocaleDateString() : formattedDate}` },
                { icon: 'bi bi-geo-alt', text: `Location: ${impact.location || 'N/A'}` },
                { icon: 'bi bi-info-circle', text: `Status: ${impact.status || 'Published'}` },
            ],
            detailContent: impact.outcome_summary,
            link: `/impact/${impact.slug}`,
            buttonText: 'View Impact',
        };
        if (mode === 'list') return <ListingRow key={impact.id} {...props} />;
        if (mode === 'timeline') return <TimelineCard key={impact.id} {...props} side={idx % 2 === 0 ? 'left' : 'right'} />;
        return <ListingCard key={impact.id} {...props} />;
    };

    return (
        <>
            <Head>
                <title>Our Impact - Global Harmony Initiative</title>
                <meta name="description" content="See the impact of our work in East Africa." />
            </Head>
            <PageHeader title={hero?.hero_impact_title || 'Our Impact'} subtitle={hero?.hero_impact_subtitle} image={hero?.hero_impact_image} buttonText={hero?.hero_impact_button_text} buttonUrl={hero?.hero_impact_button_url} breadcrumb={[{ label: 'Our Impact' }]} />
            <ListingPageLayout
                data={impactActivities}
                emptyMessage="No impact stories found."
                viewMode={viewMode}
                setViewMode={setViewMode}
                onSubmit={handleSubmit}
                filters={
                    <>
                        <input type="text" name="search" className="form-control mb-3" placeholder="Search impact..." value={search} onChange={e => setSearch(e.target.value)} />
                        <select name="sort" className="form-select mb-3" value={sortBy} onChange={e => setSortBy(e.target.value)}>
                            <option value="">Sort by</option>
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="alpha">A – Z</option>
                            <option value="impact">Most Impact</option>
                        </select>
                        <button type="submit" className="btn btn-primary w-100">Search</button>
                    </>
                }
                renderCard={renderCard}
            />
        </>
    );
}
