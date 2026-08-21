export function SkeletonCard() {
    return (
        <div className="col-md-6 col-lg-4 listing-item mb-4">
            <div className="card h-100">
                <div className="skeleton skeleton-img"></div>
                <div className="card-body">
                    <div className="skeleton skeleton-title mb-2"></div>
                    <div className="skeleton skeleton-text mb-1"></div>
                    <div className="skeleton skeleton-text mb-1"></div>
                    <div className="skeleton skeleton-text-short mb-3"></div>
                    <div className="skeleton skeleton-btn"></div>
                </div>
            </div>
        </div>
    );
}

export function SkeletonRow({ count = 3 }) {
    return (
        <div className="row">
            {Array.from({ length: count }).map((_, i) => (
                <SkeletonCard key={i} />
            ))}
        </div>
    );
}

export function SkeletonText({ lines = 3, className = '' }) {
    return (
        <div className={className}>
            {Array.from({ length: lines }).map((_, i) => (
                <div key={i} className={`skeleton skeleton-text mb-2 ${i === lines - 1 ? 'skeleton-text-short' : ''}`}></div>
            ))}
        </div>
    );
}

export function SkeletonHero() {
    return (
        <div className="skeleton" style={{ height: '60vh', width: '100%' }}></div>
    );
}
