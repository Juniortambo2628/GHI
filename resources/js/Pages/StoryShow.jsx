import PublicLayout from '../Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';

StoryShow.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function StoryShow({ story }) {
    const storyDate = story.created_at ? new Date(story.created_at).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) : '';

    return (
        <>
            <Head>
                <title>{story.title} - Global Harmony Initiative</title>
            </Head>

            <div className="container-fluid page-header hero-stories mb-5">
                <div className="container py-5">
                    <nav aria-label="breadcrumb animated slideInDown mb-4">
                        <ol className="breadcrumb">
                            <li className="breadcrumb-item"><Link href="/">Home</Link></li>
                            <li className="breadcrumb-item"><Link href="/stories">Stories</Link></li>
                            <li className="breadcrumb-item active" aria-current="page">{story.title}</li>
                        </ol>
                    </nav>
                    <h1 className="display-3 text-white mb-3 animated slideInDown">{story.title}</h1>
                </div>
            </div>

            <div className="container py-5">
                <div className="row g-5">
                    <div className="col-lg-8">
                        {(story.image || story.featured_image) && (
                            <img src={`/uploads/images/${story.image || story.featured_image}`} className="img-fluid rounded mb-4 w-100" alt={story.title} />
                        )}
                        <div className="mb-4">
                            {story.content}
                        </div>
                    </div>
                    <div className="col-lg-4">
                        <div className="bg-light p-4 rounded">
                            <h5 className="mb-3">Story Details</h5>
                            <ul className="list-unstyled">
                                {story.author && <li className="mb-2"><i className="bi bi-person me-2"></i>{story.author}</li>}
                                {storyDate && <li className="mb-2"><i className="bi bi-calendar me-2"></i>{storyDate}</li>}
                                {story.category && <li className="mb-2"><strong>Category:</strong> <span className="badge bg-primary">{story.category}</span></li>}
                                <li className="mb-2"><strong>Status:</strong> <span className={`badge bg-${story.status === 'published' ? 'success' : 'warning'}`}>{story.status}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
