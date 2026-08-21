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

Causes.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function Causes({ causes }) {
    const [search, setSearch] = useState('');
    const [sortBy, setSortBy] = useState('');
    const [viewMode, setViewMode] = useState('grid');

    const handleSubmit = (e) => {
        e.preventDefault();
        router.get('/causes', { search, sort: sortBy }, { preserveState: true });
    };

    const renderCard = (cause, idx, mode) => {
        const props = {
            key: cause.id,
            index: idx,
            image: cause.image ? `/uploads/images/${cause.image}` : null,
            imageAlt: cause.title,
            badges: cause.quote ? [{ text: cause.quote.split(' ').slice(0, 5).join(' '), className: 'bg-primary' }] : [],
            title: cause.title,
            description: cause.description,
        };
        if (mode === 'list') return <ListingRow {...props} />;
        if (mode === 'timeline') return <TimelineCard {...props} side={idx % 2 === 0 ? 'left' : 'right'} />;
        return <ListingCard {...props} />;
    };

    return (
        <>
            <Head>
                <title>Our Causes - Global Harmony Initiative</title>
                <meta name="description" content="Explore our causes and learn how we are making a difference in East Africa through education, healthcare, and community development." />
            </Head>
            <PageHeader title="Our Causes" breadcrumb={[{ label: 'Our Causes' }]} />
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
