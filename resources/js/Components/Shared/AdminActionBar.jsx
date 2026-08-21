import { Link } from '@inertiajs/react';

export default function AdminActionBar({ createUrl, entityName }) {
    return (
        <div className="d-flex justify-content-between mb-4">
            <div></div>
            <Link href={createUrl} className="btn btn-primary"><i className="bi bi-plus-circle me-2"></i>Add {entityName}</Link>
        </div>
    );
}
