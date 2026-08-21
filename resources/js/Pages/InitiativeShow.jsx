import PublicLayout from '../Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';

InitiativeShow.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function InitiativeShow({ initiative, events }) {
    return (
        <>
            <Head>
                <title>{initiative.title} - Global Harmony Initiative</title>
            </Head>

            <div className="container-fluid page-header hero-initiatives mb-5">
                <div className="container py-5">
                    <nav aria-label="breadcrumb animated slideInDown mb-4">
                        <ol className="breadcrumb">
                            <li className="breadcrumb-item"><Link href="/">Home</Link></li>
                            <li className="breadcrumb-item"><Link href="/initiatives">Initiatives</Link></li>
                            <li className="breadcrumb-item active" aria-current="page">{initiative.title}</li>
                        </ol>
                    </nav>
                    <h1 className="display-3 text-white mb-3 animated slideInDown">{initiative.title}</h1>
                </div>
            </div>

            <div className="container py-5">
                <div className="row g-5">
                    <div className="col-lg-8">
                        {initiative.image && (
                            <img src={`/uploads/images/${initiative.image}`} className="img-fluid rounded mb-4 w-100" alt={initiative.title} />
                        )}
                        <div className="mb-4">
                            {initiative.description}
                        </div>
                        {initiative.content && (
                            <div className="mb-4" dangerouslySetInnerHTML={{ __html: initiative.content }} />
                        )}
                    </div>
                    <div className="col-lg-4">
                        <div className="bg-light p-4 rounded mb-4">
                            <h5 className="mb-3">Details</h5>
                            <ul className="list-unstyled">
                                <li className="mb-2"><strong>Category:</strong> {initiative.category}</li>
                                <li className="mb-2"><strong>Status:</strong> <span className={`badge bg-${initiative.status === 'published' ? 'success' : 'warning'}`}>{initiative.status}</span></li>
                                {initiative.cause && <li className="mb-2"><strong>Cause:</strong> {initiative.cause.title}</li>}
                            </ul>
                        </div>
                    </div>
                </div>

                {events && events.length > 0 && (
                    <div className="mt-5">
                        <h3 className="mb-4">Upcoming Events</h3>
                        <div className="row g-4">
                            {events.map((event, idx) => {
                                const eventDate = new Date(event.event_date);
                                return (
                                    <div key={idx} className="col-md-6">
                                        <div className="card h-100">
                                            {event.image && <img src={`/uploads/images/${event.image}`} className="card-img-top" alt={event.title} />}
                                            <div className="card-body">
                                                <h5 className="card-title">{event.title}</h5>
                                                <p className="text-muted"><i className="bi bi-calendar me-1"></i>{eventDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p>
                                                <p className="card-text">{(event.description || '').substring(0, 100)}...</p>
                                                <Link href={`/events/${event.slug || event.id}`} className="btn btn-outline-primary">View Event</Link>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                )}
            </div>
        </>
    );
}
