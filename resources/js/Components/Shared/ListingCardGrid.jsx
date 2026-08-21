import ListingCard from './ListingCard';
import ListingRow from './ListingRow';
import TimelineCard from './TimelineCard';

export default function ListingCardGrid({ data, emptyMessage = 'No items found.', children, gridClass = 'row g-4', viewMode = 'grid' }) {
    if (!data?.data || data.data.length === 0) {
        return (
            <div className="content-container grid-view mt-4">
                <p className="text-center py-5">{emptyMessage}</p>
            </div>
        );
    }

    if (viewMode === 'list') {
        return (
            <div className="content-container list-view mt-4">
                <div className="listing-row-container">
                    {data.data.map((item, idx) => (
                        children ? children(item, idx, 'list') : <ListingRow key={idx} {...item} />
                    ))}
                </div>
            </div>
        );
    }

    if (viewMode === 'timeline') {
        return (
            <div className="content-container timeline-view mt-4">
                <div className="timeline-container">
                    <div className="timeline-line"></div>
                    {data.data.map((item, idx) => (
                        children ? children(item, idx, 'timeline') : <TimelineCard key={idx} {...item} side={idx % 2 === 0 ? 'left' : 'right'} />
                    ))}
                </div>
            </div>
        );
    }

    return (
        <div className="content-container grid-view mt-4">
            <div className={gridClass}>
                {data.data.map((item, idx) => (
                    children ? children(item, idx, 'grid') : <ListingCard key={idx} {...item} />
                ))}
            </div>
        </div>
    );
}
