import { Link } from '@inertiajs/react';

export default function ContentCard({ title, children, actions }) {
    return (
        <div className="content-card">
            {(title || actions) && (
                <div className="card-header d-flex justify-content-between align-items-center">
                    <h5 className="mb-0">{title}</h5>
                    {actions}
                </div>
            )}
            <div className="card-body">
                {children}
            </div>
        </div>
    );
}
