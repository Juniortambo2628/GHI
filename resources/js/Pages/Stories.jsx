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

Stories.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function Stories({ stories, hero }) {
    const [search, setSearch] = useState('');
    const [category, setCategory] = useState('');
    const [sortBy, setSortBy] = useState('');
    const [viewMode, setViewMode] = useState('grid');

    const handleSubmit = (e) => {
        e.preventDefault();
        router.get('/stories', { search, category, sort: sortBy }, { preserveState: true });
    };

    const renderCard = (story, idx, mode) => {
        const storyImage = story.image ? mediaUrl(story.image) : '/Banners-and-portraits/pexels-ezeguna_graphy-sulaiman-muhammad-2153324075-34536427.jpg';
        const formattedDate = story.created_at ? new Date(story.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '';
        const props = {
            index: idx,
            image: storyImage,
            imageAlt: story.title,
            badges: [{ text: (story.category || '').charAt(0).toUpperCase() + (story.category || '').slice(1) }],
            title: story.title,
            description: story.content,
            meta: [
                { icon: 'bi bi-calendar', text: `Published: ${formattedDate}` },
                { icon: 'bi bi-person', text: `Author: ${story.author || 'Staff'}` },
                { icon: 'bi bi-info-circle', text: `Status: ${story.status || 'Published'}` },
            ],
            detailContent: story.content,
            buttonText: 'Read More',
        };
        if (mode === 'list') return <ListingRow key={story.id} {...props} />;
        if (mode === 'timeline') return <TimelineCard key={story.id} {...props} side={idx % 2 === 0 ? 'left' : 'right'} />;
        return <ListingCard key={story.id} {...props} />;
    };

    return (
        <>
            <Head>
                <title>Our Stories - Global Harmony Initiative</title>
                <meta name="description" content="Read inspiring stories from our community." />
            </Head>
            <PageHeader title={hero?.hero_stories_title || 'Our Stories'} subtitle={hero?.hero_stories_subtitle} image={hero?.hero_stories_image} buttonText={hero?.hero_stories_button_text} buttonUrl={hero?.hero_stories_button_url} breadcrumb={[{ label: 'Our Stories' }]} />
            <ListingPageLayout
                data={stories}
                emptyMessage="No stories found."
                viewMode={viewMode}
                setViewMode={setViewMode}
                onSubmit={handleSubmit}
                filters={
                    <>
                        <input type="text" name="search" className="form-control mb-3" placeholder="Search stories..." value={search} onChange={e => setSearch(e.target.value)} />
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
                    </>
                }
                renderCard={renderCard}
            />
        </>
    );
}
