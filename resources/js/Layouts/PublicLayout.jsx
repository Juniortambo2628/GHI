import { Link, usePage, Head } from '@inertiajs/react';
import { useState } from 'react';
import { motion } from 'framer-motion';
import NavigationLoading from '../Components/Shared/NavigationLoading';
import mediaUrl from '../Components/Shared/mediaUrl';
import SearchModal from '../Components/Shared/SearchModal';
import FallbackImage from '../Components/Shared/FallbackImage';

const navItems = [
    { href: '/', label: 'Home' },
    { href: '/about', label: 'About Us' },
    { href: '/causes', label: 'Our Causes' },
    { href: '/initiatives', label: 'Initiatives' },
    { href: '/events', label: 'Events' },
    { href: '/impact', label: 'Our Impact' },
    { href: '/stories', label: 'Our Stories' },
    { href: '/contact', label: 'Contact' },
];

function isActive(href, url) {
    if (href === '/') return url === '/';
    return url.startsWith(href);
}

export default function PublicLayout({ children }) {
    const { auth, app_name, site_settings: settings } = usePage().props;
    const { url } = usePage();
    const [searchOpen, setSearchOpen] = useState(false);

    const siteName = settings?.site_name || app_name || 'Global Harmony Initiative';
    const logo = mediaUrl(settings?.site_logo) || '/Logo/Square-White-BG.png';
    const favicon = mediaUrl(settings?.site_favicon) || '/Logo/Square-White-BG.png';

    return (
        <>
            <Head>
                <link rel="icon" type="image/png" href={favicon} />
            </Head>
            <NavigationLoading />
            <SearchModal open={searchOpen} onClose={() => setSearchOpen(false)} />
            {/* Topbar */}
            <div className="container-fluid fixed-top px-0">
                <div className="container px-0">
                    <div className="topbar">
                        <div className="row align-items-center justify-content-center">
                            <div className="col-md-8">
                                <div className="topbar-info d-flex flex-wrap">
                                    <a href={`mailto:${settings?.contact_email || 'info@globalharmonyinitiative.com'}`} className="topbar-link me-4">
                                        <i className="bi bi-envelope"></i>{settings?.contact_email || 'info@globalharmonyinitiative.com'}
                                    </a>
                                    <a href={`tel:${settings?.contact_phone || '+1 (437) 297-7977'}`} className="topbar-link">
                                        <i className="bi bi-telephone"></i>{settings?.contact_phone || '+1 (437) 297-7977'}
                                    </a>
                                </div>
                            </div>
                            <div className="col-md-4">
                                <div className="topbar-icon d-flex align-items-center justify-content-end">
                                    {settings?.facebook_url ? (
                                        <a href={settings.facebook_url} className="btn-square text-white me-2" target="_blank" rel="noopener noreferrer"><i className="bi bi-facebook"></i></a>
                                    ) : (
                                        <a href="#" className="btn-square text-white me-2" target="_blank"><i className="bi bi-facebook"></i></a>
                                    )}
                                    {settings?.twitter_url ? (
                                        <a href={settings.twitter_url} className="btn-square text-white me-2" target="_blank" rel="noopener noreferrer"><i className="bi bi-twitter"></i></a>
                                    ) : (
                                        <a href="#" className="btn-square text-white me-2" target="_blank"><i className="bi bi-twitter"></i></a>
                                    )}
                                    {settings?.instagram_url ? (
                                        <a href={settings.instagram_url} className="btn-square text-white me-2" target="_blank" rel="noopener noreferrer"><i className="bi bi-instagram"></i></a>
                                    ) : (
                                        <a href="#" className="btn-square text-white me-2" target="_blank"><i className="bi bi-instagram"></i></a>
                                    )}
                                    {settings?.linkedin_url ? (
                                        <a href={settings.linkedin_url} className="btn-square text-white me-2" target="_blank" rel="noopener noreferrer"><i className="bi bi-linkedin"></i></a>
                                    ) : (
                                        <a href="#" className="btn-square text-white me-2" target="_blank"><i className="bi bi-linkedin"></i></a>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Navbar */}
                    <nav className="navbar navbar-light bg-light navbar-expand-xl">
                        <Link href="/" className="navbar-brand ms-3">
                            <FallbackImage src={logo} alt={siteName} className="navbar-logo-img" />
                        </Link>
                        <button className="navbar-toggler py-2 px-3 me-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                            <span className="bi bi-list text-primary fs-4"></span>
                        </button>
                        <div className="collapse navbar-collapse bg-light" id="navbarCollapse">
                            <div className="navbar-nav ms-auto">
                                {navItems.map(({ href, label }) => (
                                    <Link
                                        key={href}
                                        href={href}
                                        className={`nav-item nav-link ${isActive(href, url) ? 'nav-link-active' : ''}`}
                                    >
                                        {label}
                                    </Link>
                                ))}
                            </div>
                            <div className="d-flex align-items-center flex-nowrap pt-xl-0 navbar-cta-container" style={{ gap: '0.75rem' }}>
                                <button type="button" className="site-search-icon-btn" onClick={() => setSearchOpen(true)} aria-label="Search">
                                    <i className="bi bi-search"></i>
                                </button>
                                <Link href="/get-involved" className="btn-hover-bg btn btn-primary text-white py-2 px-4 me-3">Get Involved</Link>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>

            {/* Main Content */}
            <motion.main
                key={url}
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                transition={{ duration: 0.3 }}
                className="site-main-content"
            >
                {children}
            </motion.main>

            {/* Footer */}
            <footer className="site-footer">
                <div className="footer-top-border"></div>
                <div className="container py-5">
                    <div className="row g-0">
                        <div className="col-lg-3 col-md-6 footer-col">
                            <h5 className="footer-heading">About Us</h5>
                            <div className="footer-heading-line"></div>
                            <p className="small footer-text">{settings?.footer_text || 'Bridging Global Compassion with Local Action.'}</p>
                            <FallbackImage src="/Logo/Landscape-Logo.png" alt={siteName} className="footer-logo mt-3" />
                        </div>
                        <div className="col-lg-3 col-md-6 footer-col">
                            <h5 className="footer-heading">Quick Links</h5>
                            <div className="footer-heading-line"></div>
                            <ul className="list-unstyled">
                                <li><Link href="/about" className="footer-link">About Us</Link></li>
                                <li><Link href="/causes" className="footer-link">Our Causes</Link></li>
                                <li><Link href="/initiatives" className="footer-link">Our Initiatives</Link></li>
                                <li><Link href="/events" className="footer-link">Events & Activities</Link></li>
                                <li><Link href="/impact" className="footer-link">Our Impact</Link></li>
                                <li><Link href="/stories" className="footer-link">Our Stories</Link></li>
                            </ul>
                        </div>
                        <div className="col-lg-3 col-md-6 footer-col">
                            <h5 className="footer-heading">Contact Us</h5>
                            <div className="footer-heading-line"></div>
                            <p className="small footer-text"><i className="bi bi-envelope me-2"></i>{settings?.contact_email || 'info@globalharmonyinitiative.com'}</p>
                            <p className="small footer-text"><i className="bi bi-telephone me-2"></i>{settings?.contact_phone || '+1 (437) 297-7977'}</p>
                        </div>
                        <div className="col-lg-3 col-md-6 footer-col">
                            <h5 className="footer-heading">Our Mission</h5>
                            <div className="footer-heading-line"></div>
                            <p className="small footer-text">To bridge global compassion with local action, empowering communities in East Africa through sustainable programs.</p>
                        </div>
                    </div>
                    <div className="footer-bottom-bar mt-5 pt-4">
                        <div className="d-flex flex-wrap justify-content-between align-items-center">
                            <p className="small mb-0 footer-text">&copy; {new Date().getFullYear()} {siteName}. All rights reserved.</p>
                            <p className="small mb-0 footer-text d-inline-flex align-items-center">
                                Engineered by{' '}
                                <a href="https://okjtech.co.ke" target="_blank" rel="noopener noreferrer" className="footer-link d-inline-flex align-items-center ms-1">
                                    <img src="/Developer/OKJTechLogo-White_Transparent.png" alt="OKJTech" className="footer-dev-logo me-1" />
                                    OKJTech
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </footer>
        </>
    );
}
