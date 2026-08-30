import PublicLayout from '../Layouts/PublicLayout';
import sanitizeHtml from '../Components/Shared/sanitizeHtml';
import mediaUrl from '../Components/Shared/mediaUrl';
import FallbackImage from '../Components/Shared/FallbackImage';
import StatusBadge from '../Components/Shared/StatusBadge';
import ShowPageLayout from '../Components/Shared/ShowPageLayout';
import { Head } from '@inertiajs/react';

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

    return (
        <>
            <Head title={`${story.title || 'Story'} - Global Harmony Initiative`} />

            <ShowPageLayout title={story.title || 'Story'} section="stories" sectionLabel="Stories" sectionUrl="/stories" image={storyImage}
                sidebar={
                    <div className="bg-light p-4 rounded">
                        <h5 className="mb-3">Story Details</h5>
                        <ul className="list-unstyled">
                            {story.author && <li className="mb-2"><i className="bi bi-person me-2"></i>{story.author}</li>}
                            {storyDate && <li className="mb-2"><i className="bi bi-calendar me-2"></i>{storyDate}</li>}
                            {story.category && <li className="mb-2"><strong>Category:</strong> <span className="badge bg-primary">{story.category}</span></li>}
                            {story.status && <li className="mb-2"><strong>Status:</strong> <StatusBadge status={story.status} /></li>}
                        </ul>
                    </div>
                }>
                <div className="mb-4 story-content" dangerouslySetInnerHTML={{ __html: sanitizeHtml(story.content || '') }} />
            </ShowPageLayout>
        </>
    );
}
