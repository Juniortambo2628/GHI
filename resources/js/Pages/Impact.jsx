import PublicLayout from '../Layouts/PublicLayout';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import { categoryToObjective } from '../Constants/categoryToObjective';
import { mediaUrl } from '../Components/Shared/ImageUploadField';
import PageHeader from '../Components/Shared/PageHeader';
import SearchSidebar from '../Components/Shared/SearchSidebar';
import ResultsCount from '../Components/Shared/ResultsCount';
import ListingCard from '../Components/Shared/ListingCard';
import ListingRow from '../Components/Shared/ListingRow';
import TimelineCard from '../Components/Shared/TimelineCard';
import ListingCardGrid from '../Components/Shared/ListingCardGrid';
import Pagination from '../Components/Shared/Pagination';

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
                { icon: 'bi bi-calendar', text: formattedDate }
            ],
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
            <div className="container-fluid px-5">
                <div className="row g-4">
                    <SearchSidebar onSubmit={handleSubmit} viewMode={viewMode} setViewMode={setViewMode}>
                        <input type="text" name="search" className="form-control mb-3" placeholder="Search impact..." value={search} onChange={e => setSearch(e.target.value)} />
                        <select name="sort" className="form-select mb-3" value={sortBy} onChange={e => setSortBy(e.target.value)}>
                            <option value="">Sort by</option>
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="alpha">A – Z</option>
                            <option value="impact">Most Impact</option>
                        </select>
                        <button type="submit" className="btn btn-primary w-100">Search</button>
                    </SearchSidebar>
                    <div className="col-lg-9">
                        <ResultsCount data={impactActivities} />
                        <ListingCardGrid data={impactActivities} emptyMessage="No impact stories found." viewMode={viewMode}>
                            {renderCard}
                        </ListingCardGrid>
                        <Pagination data={impactActivities} />
                    </div>
                </div>
            </div>
        </>
    );
}
