import PublicLayout from '../Layouts/PublicLayout';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import PageHeader from '../Components/Shared/PageHeader';
import SearchSidebar from '../Components/Shared/SearchSidebar';
import ResultsCount from '../Components/Shared/ResultsCount';
import ListingCard from '../Components/Shared/ListingCard';
import ListingRow from '../Components/Shared/ListingRow';
import TimelineCard from '../Components/Shared/TimelineCard';
import ListingCardGrid from '../Components/Shared/ListingCardGrid';
import Pagination from '../Components/Shared/Pagination';
import { mediaUrl } from '../Components/Shared/ImageUploadField';

Causes.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function Causes({ causes, hero }) {
    const [search, setSearch] = useState('');
    const [sortBy, setSortBy] = useState('');
    const [viewMode, setViewMode] = useState('grid');

    const handleSubmit = (e) => {
        e.preventDefault();
        router.get('/causes', { search, sort: sortBy }, { preserveState: true });
    };

    const renderCard = (cause, idx, mode) => {
        const meta = [];
        if (cause.quote) meta.push({ icon: 'bi-quote', text: cause.quote });
        if (cause.icon) meta.push({ icon: `bi-${cause.icon}`, text: cause.title });

        const props = {
            index: idx,
            image: cause.image ? mediaUrl(cause.image) : null,
            imageAlt: cause.title,
            badges: [],
            title: cause.title,
            description: cause.description,
            meta,
        };
        if (mode === 'list') return <ListingRow key={cause.id} {...props} />;
        if (mode === 'timeline') return <TimelineCard key={cause.id} {...props} side={idx % 2 === 0 ? 'left' : 'right'} />;
        return <ListingCard key={cause.id} {...props} />;
    };

    return (
        <>
            <Head>
                <title>Our Causes - Global Harmony Initiative</title>
                <meta name="description" content="Explore our causes and learn how we are making a difference in East Africa through education, healthcare, and community development." />
            </Head>
            <PageHeader title={hero?.hero_causes_title || 'Our Causes'} subtitle={hero?.hero_causes_subtitle} image={hero?.hero_causes_image} buttonText={hero?.hero_causes_button_text} buttonUrl={hero?.hero_causes_button_url} breadcrumb={[{ label: 'Our Causes' }]} />
            <div className="container-fluid px-5">
                <div className="row">
                    <SearchSidebar title="Search & Filter" onSubmit={handleSubmit} viewMode={viewMode} setViewMode={setViewMode}>
                        <input type="text" name="search" className="form-control mb-3" placeholder="Search causes..." value={search} onChange={e => setSearch(e.target.value)} />
                        <select name="sort" className="form-select mb-3" value={sortBy} onChange={e => setSortBy(e.target.value)}>
                            <option value="">Sort by</option>
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="alpha">A – Z</option>
                        </select>
                        <button type="submit" className="btn btn-primary w-100">Search</button>
                    </SearchSidebar>
                    <div className="col-lg-9">
                        <ResultsCount data={causes} />
                        <ListingCardGrid data={causes} emptyMessage="No causes found. Please try different search or filter criteria." viewMode={viewMode}>
                            {renderCard}
                        </ListingCardGrid>
                        <Pagination data={causes} />
                    </div>
                </div>
            </div>
        </>
    );
}
