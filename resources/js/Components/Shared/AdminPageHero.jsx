import AdminBreadcrumb from './AdminBreadcrumb';

export default function AdminPageHero({ title, description, breadcrumbs = [] }) {
    return (
        <div className="admin-page-hero">
            <AdminBreadcrumb items={breadcrumbs} />
            <h1>{title}</h1>
            {description && <p>{description}</p>}
        </div>
    );
}
