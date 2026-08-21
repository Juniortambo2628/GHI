import PublicLayout from '../Layouts/PublicLayout';
import { Link } from '@inertiajs/react';

export default function NotFound() {
    return (
        <PublicLayout>
            <section className="hero-section error-hero-bg">
                <div className="container text-center text-white">
                    <div className="error-code">404</div>
                    <h1 className="display-3 fw-bold mb-4">Page Not Found</h1>
                    <p className="lead mb-5 error-lead-text">
                        Oops! The page you're looking for seems to have wandered off. Don't worry, even the best explorers get lost sometimes.
                    </p>
                    <div>
                        <Link href="/" className="btn btn-light btn-lg me-3">
                            <i className="bi bi-house-door me-2"></i>Go Home
                        </Link>
                        <Link href="/about" className="btn btn-outline-light btn-lg">
                            <i className="bi bi-info-circle me-2"></i>About Us
                        </Link>
                    </div>
                </div>
            </section>

            <section className="py-5 bg-light">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-lg-10">
                            <h2 className="text-center mb-5">Here's Where You Might Want to Go</h2>
                            <div className="row g-4">
                                <div className="col-md-6 col-lg-3">
                                    <div className="card h-100 text-center border-0 shadow-sm hover-lift">
                                        <div className="card-body p-4">
                                            <div className="icon-circle icon-circle-sm mx-auto mb-3 icon-circle-gradient-purple">
                                                <i className="bi bi-house-door fs-3 text-white"></i>
                                            </div>
                                            <h5 className="card-title">Home</h5>
                                            <p className="card-text text-muted small">Return to our homepage</p>
                                            <Link href="/" className="stretched-link"></Link>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-md-6 col-lg-3">
                                    <div className="card h-100 text-center border-0 shadow-sm hover-lift">
                                        <div className="card-body p-4">
                                            <div className="icon-circle icon-circle-sm mx-auto mb-3 icon-circle-gradient-pink">
                                                <i className="bi bi-people fs-3 text-white"></i>
                                            </div>
                                            <h5 className="card-title">About Us</h5>
                                            <p className="card-text text-muted small">Learn about our mission</p>
                                            <Link href="/about" className="stretched-link"></Link>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-md-6 col-lg-3">
                                    <div className="card h-100 text-center border-0 shadow-sm hover-lift">
                                        <div className="card-body p-4">
                                            <div className="icon-circle icon-circle-sm mx-auto mb-3 icon-circle-gradient-blue">
                                                <i className="bi bi-briefcase fs-3 text-white"></i>
                                            </div>
                                            <h5 className="card-title">Our Work</h5>
                                            <p className="card-text text-muted small">Explore our initiatives</p>
                                            <Link href="/initiatives" className="stretched-link"></Link>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-md-6 col-lg-3">
                                    <div className="card h-100 text-center border-0 shadow-sm hover-lift">
                                        <div className="card-body p-4">
                                            <div className="icon-circle icon-circle-sm mx-auto mb-3 icon-circle-gradient-green">
                                                <i className="bi bi-envelope fs-3 text-white"></i>
                                            </div>
                                            <h5 className="card-title">Contact</h5>
                                            <p className="card-text text-muted small">Get in touch with us</p>
                                            <Link href="/contact" className="stretched-link"></Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section className="py-5">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-lg-8">
                            <div className="card border-0 shadow-sm">
                                <div className="card-body p-5 text-center">
                                    <h3 className="mb-4">Looking for Something Specific?</h3>
                                    <p className="text-muted mb-4">Try one of the links above or return to our homepage.</p>
                                    <Link href="/" className="btn btn-primary btn-lg px-5">
                                        <i className="bi bi-house-door me-2"></i>Go Home
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
