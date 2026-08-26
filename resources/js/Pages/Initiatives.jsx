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

Initiatives.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function Initiatives({ initiatives, hero }) {
    const [search, setSearch] = useState('');
    const [category, setCategory] = useState('');
    const [sortBy, setSortBy] = useState('');
    const [viewMode, setViewMode] = useState('grid');

    const handleSubmit = (e) => {
        e.preventDefault();
        router.get('/initiatives', { search, category, sort: sortBy }, { preserveState: true });
    };

    const renderCard = (initiative, idx, mode) => {
        const props = {
            index: idx,
            image: initiative.image ? mediaUrl(initiative.image) : null,
            imageAlt: initiative.title,
            badges: [{ text: categoryToObjective[initiative.category] || 'Community Development' }],
            title: initiative.title,
            description: initiative.description,
        };
        if (mode === 'list') return <ListingRow key={initiative.id} {...props} />;
        if (mode === 'timeline') return <TimelineCard key={initiative.id} {...props} side={idx % 2 === 0 ? 'left' : 'right'} />;
        return <ListingCard key={initiative.id} {...props} />;
    };

    return (
        <>
            <Head>
                <title>Our Initiatives - Global Harmony Initiative</title>
                <meta name="description" content="Explore our initiatives and programs that are creating lasting change in East Africa." />
            </Head>
            <PageHeader title={hero?.hero_initiatives_title || 'Our Initiatives'} subtitle={hero?.hero_initiatives_subtitle} image={hero?.hero_initiatives_image} buttonText={hero?.hero_initiatives_button_text} buttonUrl={hero?.hero_initiatives_button_url} breadcrumb={[{ label: 'Our Initiatives' }]} />
            <div className="container-fluid px-5">
                <div className="row g-4">
                    <SearchSidebar title="Search & Filter" onSubmit={handleSubmit} viewMode={viewMode} setViewMode={setViewMode}>
                        <input type="text" name="search" className="form-control mb-3" placeholder="Search initiatives..." value={search} onChange={e => setSearch(e.target.value)} />
                        <select name="category" className="form-select mb-3" value={category} onChange={e => setCategory(e.target.value)}>
                            <option value="">All Categories</option>
                            {Object.entries(categoryToObjective).map(([key, label]) => (
                                <option key={key} value={key}>{label}</option>
                            ))}
                        </select>
                        <select name="sort" className="form-select mb-3" value={sortBy} onChange={e => setSortBy(e.target.value)}>
                            <option value="">Sort by</option>
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="alpha">A – Z</option>
                        </select>
                        <button type="submit" className="btn btn-primary w-100">Search</button>
                    </SearchSidebar>
                    <div className="col-lg-9">
                        <ResultsCount data={initiatives} />
                        <ListingCardGrid data={initiatives} emptyMessage="No initiatives found." viewMode={viewMode}>
                            {renderCard}
                        </ListingCardGrid>
                        <Pagination data={initiatives} />
                    </div>
                </div>
            </div>
        </>
    );
}
