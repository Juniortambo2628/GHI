import { Link, usePage } from '@inertiajs/react';

export default function AdminLayout({ children, title }) {
    const { auth } = usePage().props;
    const currentUrl = usePage().url;

    const menuItems = [
        { icon: 'bi-speedometer2', label: 'Dashboard', url: '/admin' },
        { icon: 'bi-heart', label: 'Causes', url: '/admin/causes' },
        { icon: 'bi-lightbulb', label: 'Initiatives', url: '/admin/initiatives' },
        { icon: 'bi-calendar-event', label: 'Events', url: '/admin/events' },
        { icon: 'bi-journal-text', label: 'Stories', url: '/admin/stories' },
        { icon: 'bi-graph-up', label: 'Impact', url: '/admin/impact' },
    ];

    return (
        <div className="admin-wrapper">
            <aside className="admin-sidebar">
                <div className="sidebar-content">
                    <div className="sidebar-logo">
                        <img src="/Logo/Square-White-BG.png" alt="GHI" className="logo-img" />
                        <div className="logo-text">
                            <strong>GLOBAL HARMONY INITIATIVE</strong><br />
                            <small>ADMIN DASHBOARD</small>
                        </div>
                    </div>
                    <nav className="sidebar-nav">
                        <ul className="nav-menu">
                            {menuItems.map(item => (
                                <li key={item.url} className={`nav-item ${currentUrl === item.url ? 'active' : ''}`}>
                                    <Link href={item.url} className="nav-link">
                                        <i className={`bi ${item.icon}`}></i>
                                        <span>{item.label}</span>
                                    </Link>
                                </li>
                            ))}
                        </ul>
                        <ul className="nav-menu actions-menu">
                            <li className="nav-item">
                                <Link href="/" className="nav-link"><i className="bi bi-house"></i><span>Back to Site</span></Link>
                            </li>
                        </ul>
                    </nav>
                </div>
            </aside>
            <main className="admin-main">
                <header className="admin-header">
                    <div className="container-fluid d-flex align-items-center">
                        <div className="ms-auto d-flex align-items-center gap-3">
                            <span>{auth?.user?.email || 'Admin'}</span>
                        </div>
                    </div>
                </header>
                <h1 className="h3 mb-4">{title}</h1>
                {children}
            </main>
        </div>
    );
}
