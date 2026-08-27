import { Link, router, usePage, Head } from '@inertiajs/react';
import FlashMessages from '../Components/Shared/FlashMessages';
import NavigationLoading from '../Components/Shared/NavigationLoading';
import AdminSearch from '../Components/Shared/AdminSearch';
import NotificationBell from '../Components/Shared/NotificationBell';
import AdminAvatar from '../Components/Shared/AdminAvatar';
import AdminDropdown, { AdminDropdownItem, AdminDropdownDivider } from '../Components/Shared/AdminDropdown';
import AdminPageHero from '../Components/Shared/AdminPageHero';
import AdminFloatingToolbar from '../Components/Shared/AdminFloatingToolbar';
import AdminSummaryCards from '../Components/Shared/AdminSummaryCards';
import { RESOURCE_ICONS, RESOURCE_URLS, RESOURCE_LABELS } from '../Constants/resources';
import mediaUrl from '../Components/Shared/mediaUrl';

export default function AdminLayout({ children, title, description, breadcrumbs, toolbar, toolbarLeft, toolbarRight, onSave, saveLabel = 'Save', saveProcessing, unsavedChanges, saveStatus }) {
    const { auth, site_settings: settings } = usePage().props;
    const currentUrl = usePage().url;
    const favicon = mediaUrl(settings?.site_favicon) || '/Logo/Square-White-BG.png';
    const userName = auth?.user?.name || auth?.user?.email || 'Admin';

    const menuItems = [
        { icon: 'bi-speedometer2', label: 'Overview', url: '/admin' },
        ...Object.keys(RESOURCE_ICONS)
            .filter(k => k !== 'media')
            .map(k => ({ icon: RESOURCE_ICONS[k], label: RESOURCE_LABELS[k], url: RESOURCE_URLS[k] })),
        { icon: 'bi-people', label: 'Get Involved', url: '/admin/get-involved' },
        { icon: 'bi-shield-lock', label: 'Security', url: '/admin/security' },
    ];

    const autoBreadcrumbs = breadcrumbs || (() => {
        const segments = currentUrl.split('/').filter(Boolean);
        if (segments.length <= 1) return [];
        const crumbs = [{ label: 'Dashboard', href: '/admin' }];
        const section = segments[1];
        const matchingItem = menuItems.find(m => m.url.endsWith(`/${section}`));
        if (matchingItem) {
            crumbs.push({ label: matchingItem.label });
        }
        return crumbs;
    })();

    const defaultRight = onSave ? (
        <button type="button" className="btn btn-primary" onClick={onSave} disabled={saveProcessing}>
            {saveProcessing ? <><span className="spinner-grow spinner-grow-sm me-1" role="status"></span>Saving...</> : <><i className="bi bi-check-lg me-1"></i>{saveLabel}</>}
        </button>
    ) : null;

    return (
        <div className="admin-wrapper">
            <Head>
                <link rel="icon" type="image/png" href={favicon} />
            </Head>
            <NavigationLoading />
            <FlashMessages />
            <aside className="admin-sidebar">
                <div className="sidebar-content">
                    <div className="sidebar-logo">
                        <img src="/Logo/Square-White-BG.png" alt="GHI" className="logo-img" />
                    </div>
                    <nav className="sidebar-nav">
                        <ul className="nav-menu">
                            {menuItems.map(item => (
                                <li key={item.url} className={`nav-item ${currentUrl === item.url || currentUrl.startsWith(`${item.url}/`) ? 'active' : ''}`}>
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
                    <div className="header-left">
                        <span className="d-md-none" style={{ cursor: 'pointer', fontSize: '1.25rem', color: 'var(--admin-muted)' }}>
                            <i className="bi bi-list"></i>
                        </span>
                    </div>
                    <div className="header-center">
                        <AdminSearch />
                    </div>
                    <div className="header-right">
                        <NotificationBell />
                        <AdminDropdown
                            trigger={
                                <div className="admin-profile-btn">
                                    <AdminAvatar name={userName} size="sm" />
                                    <span className="admin-profile-name">{userName}</span>
                                    <i className="bi bi-chevron-down" style={{ fontSize: '0.65rem', color: 'var(--admin-muted)' }}></i>
                                </div>
                            }
                        >
                            <AdminDropdownItem href="/admin/settings" icon="bi-person">{userName}</AdminDropdownItem>
                            <AdminDropdownItem href="/admin/settings" icon="bi-gear">Settings</AdminDropdownItem>
                            <AdminDropdownDivider />
                            <AdminDropdownItem onClick={() => router.post('/logout')} icon="bi-box-arrow-right">Log Out</AdminDropdownItem>
                        </AdminDropdown>
                    </div>
                </header>

                {title && <AdminPageHero title={title} description={description} breadcrumbs={autoBreadcrumbs} />}

                <AdminSummaryCards />

                {children}
            </main>

            <AdminFloatingToolbar
                left={toolbarLeft || toolbar}
                right={toolbarRight ? (<>{toolbarRight}{defaultRight}</>) : defaultRight}
                unsavedChanges={unsavedChanges}
                saveStatus={saveStatus}
            />
        </div>
    );
}
