import { Link } from '@inertiajs/react';
import mediaUrl from './mediaUrl';
import FallbackImage from './FallbackImage';

export default function EventListItem({ event }) {
    const eventDate = new Date(event.date || event.event_date);
    const day = String(eventDate.getDate()).padStart(2, '0');
    const month = eventDate.toLocaleString('en-US', { month: 'long' });
    const time = eventDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
    const eventImage = event.image ? mediaUrl(event.image) : '/Banners-and-portraits/pexels-rdne-6646883.jpg';

    return (
        <div className="event-list-item">
            <div className="event-date-block">
                <div className="event-day">{day}</div>
                <div className="event-month">{month}</div>
            </div>
            <div className="event-image-container">
                <FallbackImage src={eventImage} alt={event.title} className="event-image" loading="lazy" width="300" height="200" />
            </div>
            <div className="event-details">
                <h4 className="event-title"><Link href="/events">{event.title}</Link></h4>
                <p className="event-subtitle">{event.initiative?.title || event.initiative || 'General'}</p>
                <div className="event-meta">
                    <span className="glass-pill-sm"><i className="bi bi-geo-alt"></i></span>
                    <span className="event-location ms-1">{event.location || ''}</span>
                    <span className="glass-pill-sm ms-2"><i className="bi bi-clock"></i></span>
                    <span className="event-time ms-1">{time}</span>
                </div>
            </div>
            <div className="event-action">
                <Link className="btn btn-dark btn-sm" href="/events">view details</Link>
            </div>
        </div>
    );
}
