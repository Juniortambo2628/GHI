import PublicLayout from '../Layouts/PublicLayout';
import { Link, Head } from '@inertiajs/react';
import { useEffect, useRef } from 'react';
import { motion } from 'framer-motion';
import AnimatedSection, { AnimatedCard } from '../Components/Shared/AnimatedSection';

Home.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function Home({ initiatives, events, stories, recentActivities, counters, randomQuote, objectives, coreValues }) {
    const carouselRef = useRef(null);

    useEffect(() => {
        if (window.bootstrap && carouselRef.current) {
            const el = carouselRef.current;
            new window.bootstrap.Carousel(el, { interval: 5000, ride: 'carousel' });
        }
    }, []);

    const heroSlides = [
        { heading: 'Global Harmony Initiative', subheading: 'Global Compassion, Local Action', image: 'Banners-and-portraits/pexels-lagosfoodbank-6472487.jpg', primaryText: 'Get Involved', primaryUrl: '/coming-soon-get-involved', secondaryText: 'Donate Now', secondaryUrl: '/coming-soon-donate' },
        { heading: 'Empowering Communities', subheading: 'Education, Health & Livelihoods', image: 'Banners-and-portraits/pexels-speakmediauganda-33749790.jpg', primaryText: 'Our Initiatives', primaryUrl: '/initiatives', secondaryText: 'Learn More', secondaryUrl: '/contact' },
        { heading: 'Making a Difference', subheading: 'Join Our Mission', image: 'Banners-and-portraits/pexels-speakmediauganda-33749791.jpg', primaryText: 'See Our Impact', primaryUrl: '/impact', secondaryText: 'Contact Us', secondaryUrl: '/contact' },
    ];

    const objectiveImages = [
        'Banners-and-portraits/pexels-lagosfoodbank-6472487.jpg',
        'Banners-and-portraits/pexels-lagosfoodbank-8054617.jpg',
        'Banners-and-portraits/pexels-speakmediauganda-33749783.jpg',
        'Banners-and-portraits/pexels-speakmediauganda-34222337.jpg',
        'Banners-and-portraits/pexels-speakmediauganda-33749791.jpg',
    ];

    return (
        <>
            <Head>
                <title>Global Harmony Initiative - Global Compassion, Local Action</title>
                <meta name="description" content="Global Harmony Initiative is a U.S.-registered 501(c)(3) nonprofit organization working in East Africa to create positive change through education, healthcare, and community development." />
            </Head>

            {/* Hero Carousel */}
            <div className="container-fluid carousel-header vh-100 px-0">
                <div id="carouselId" className="carousel slide carousel-fade" ref={carouselRef} data-bs-ride="carousel" data-bs-interval="5000">
                    <ol className="carousel-indicators">
                        {heroSlides.map((_, index) => (
                            <li key={index} data-bs-target="#carouselId" data-bs-slide-to={index} className={index === 0 ? 'active' : ''}></li>
                        ))}
                    </ol>
                    <div className="carousel-inner" role="listbox">
                        {heroSlides.map((slide, index) => (
                            <div key={index} className={`carousel-item ${index === 0 ? 'active' : ''}`}>
                                <img src={`/${slide.image}`} className="img-fluid hero-carousel-img" alt={slide.heading} {...(index === 0 ? { fetchPriority: 'high' } : { loading: 'lazy' })} width="1920" height="1080" />
                                <div className="carousel-caption">
                                    <div className="p-3 hero-caption-container">
                                        {slide.subheading && <p className="text-uppercase text-white-50 mb-3 hero-subheading">{slide.subheading}</p>}
                                        <h1 className="display-1 text-capitalize text-white mb-4">{slide.heading}</h1>
                                        <div className="d-flex align-items-center justify-content-center gap-3 flex-wrap">
                                            <Link className="btn-hover-bg btn btn-primary text-white py-3 px-5" href={slide.primaryUrl}>{slide.primaryText}</Link>
                                            {slide.secondaryText && <Link className="btn-hover-bg btn btn-secondary text-dark py-3 px-5" href={slide.secondaryUrl}>{slide.secondaryText}</Link>}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                    <button className="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
                        <span className="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span className="visually-hidden">Previous</span>
                    </button>
                    <button className="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
                        <span className="carousel-control-next-icon" aria-hidden="true"></span>
                        <span className="visually-hidden">Next</span>
                    </button>
                </div>
            </div>

            {/* About Section */}
            <div className="container-fluid about py-5">
                <div className="container py-5">
                    <div className="row g-5">
                        <div className="col-xl-5">
                            <div className="h-100 img-overlay">
                                <img src="/Banners-and-portraits/pexels-magda-ehlers-pexels-2660262.jpg" className="img-fluid w-100 h-100 about-feature-img" alt="Image" loading="eager" width="800" height="600" />
                            </div>
                        </div>
                        <div className="col-xl-7">
                            <h5 className="text-uppercase text-secondary">About Us</h5>
                            <h1 className="mb-4">Bridging Global Compassion with Local Action</h1>
                            <p className="fs-5 mb-4">At Global Harmony Initiative Inc., we believe that harmony begins when humanity comes together — across borders, beliefs, and backgrounds — to create lasting change. From classrooms in Kenya to community wells in Zanzibar, we connect people and resources to nurture sustainable growth and empower local leaders to build a brighter tomorrow.</p>
                            <div className="tab-class bg-secondary p-4">
                                <ul className="nav d-flex flex-wrap mb-2">
                                    <li className="nav-item mb-3">
                                        <a className="d-flex py-2 text-center bg-white active" data-bs-toggle="pill" href="#tab-1">
                                            <span className="text-dark about-tab-label">About</span>
                                        </a>
                                    </li>
                                    <li className="nav-item mb-3">
                                        <a className="d-flex py-2 mx-2 mx-md-3 text-center bg-white" data-bs-toggle="pill" href="#tab-2">
                                            <span className="text-dark about-tab-label">Mission</span>
                                        </a>
                                    </li>
                                    <li className="nav-item mb-3">
                                        <a className="d-flex py-2 text-center bg-white" data-bs-toggle="pill" href="#tab-3">
                                            <span className="text-dark about-tab-label">Vision</span>
                                        </a>
                                    </li>
                                </ul>
                                <div className="tab-content">
                                    <div id="tab-1" className="tab-pane fade show p-0 active">
                                        <div className="row">
                                            <div className="col-12">
                                                <div className="d-flex">
                                                    <div className="text-start my-auto">
                                                        <h5 className="text-uppercase mb-3">Who We Are</h5>
                                                        <p className="mb-4">Global Harmony Initiative Inc. (GHI) is a U.S.-registered 501(c)(3) nonprofit organization working hand in hand with communities across East Africa to alleviate poverty and promote sustainable development. We connect compassion with action — linking donors, volunteers, and local leaders to transform lives through education, health, and economic empowerment. Founded on the belief that every person deserves opportunity and dignity, GHI stands as a bridge between global goodwill and community-driven impact.</p>
                                                        <div className="d-flex align-items-center justify-content-start">
                                                            <Link className="btn-hover-bg btn btn-primary text-white py-2 px-4" href="/about">Read More</Link>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="tab-2" className="tab-pane fade show p-0">
                                        <div className="row">
                                            <div className="col-12">
                                                <div className="d-flex">
                                                    <div className="text-start my-auto">
                                                        <h5 className="text-uppercase mb-3">Our Mission</h5>
                                                        <p className="mb-4">To alleviate poverty and promote sustainable development in East Africa by supporting community-led initiatives in education, health, and economic empowerment, while fostering global partnerships built on compassion and accountability.</p>
                                                        <div className="d-flex align-items-center justify-content-start">
                                                            <Link className="btn-hover-bg btn btn-primary text-white py-2 px-4" href="/about">Read More</Link>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="tab-3" className="tab-pane fade show p-0">
                                        <div className="row">
                                            <div className="col-12">
                                                <div className="d-flex">
                                                    <div className="text-start my-auto">
                                                        <h5 className="text-uppercase mb-3">Our Vision</h5>
                                                        <p className="mb-4">A world where communities thrive in harmony — free from poverty, empowered with opportunity, and united by shared humanity.</p>
                                                        <div className="d-flex align-items-center justify-content-start">
                                                            <Link className="btn-hover-bg btn btn-primary text-white py-2 px-4" href="/about">Read More</Link>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Quote Banner */}
            <div className="container-fluid py-5 quote-banner-bg" data-parallax="0.3">
                <div className="container py-5">
                    <div className="text-center mx-auto quote-container">
                        {randomQuote && (
                            <blockquote className="blockquote text-white mb-0">
                                <p className="fs-2 mb-3 fst-italic">&ldquo;{randomQuote.quote}&rdquo;</p>
                                <footer className="blockquote-footer text-white-50 mt-3">
                                    <cite className="quote-citation">{randomQuote.author}</cite>
                                </footer>
                            </blockquote>
                        )}
                    </div>
                </div>
            </div>

            {/* Foundation Section */}
            <AnimatedSection animation="fadeUp">
                <div className="container-fluid py-5 foundation-section-bg">
                    <div className="container py-5">
                        <div className="text-center mx-auto mb-5 section-header-container">
                            <h5 className="text-uppercase text-secondary">About Us</h5>
                            <h1 className="mb-0">Our Foundation</h1>
                        </div>
                        <div className="row g-4 align-items-stretch">
                            <div className="col-lg-5 d-flex flex-column">
                                <div className="foundation-card rounded p-4 mb-4 foundation-card-gradient flex-grow-1">
                                    <div className="d-flex justify-content-between align-items-start mb-3">
                                        <h5 className="text-white mb-0">Our Mission</h5>
                                        <i className="bi bi-bullseye text-white foundation-icon"></i>
                                    </div>
                                    <p className="mb-0 text-white">To alleviate poverty and promote sustainable development in East Africa by supporting community-led initiatives in education, health, and economic empowerment, while fostering global partnerships built on compassion and accountability.</p>
                                </div>
                                <div className="foundation-card rounded p-4 foundation-card-gradient flex-grow-1">
                                    <div className="d-flex justify-content-between align-items-start mb-3">
                                        <h5 className="text-white mb-0">Our Vision</h5>
                                        <i className="bi bi-eye text-white foundation-icon"></i>
                                    </div>
                                    <p className="mb-0 text-white">A world where communities thrive in harmony — free from poverty, empowered with opportunity, and united by shared humanity.</p>
                                </div>
                            </div>
                            <div className="col-lg-7 d-flex">
                                <div className="foundation-card bg-white rounded p-4 w-100 d-flex flex-column">
                                    <div className="d-flex justify-content-between align-items-start mb-3">
                                        <h5 className="text-primary mb-0">Our Values</h5>
                                        <i className="bi bi-heart text-primary foundation-icon"></i>
                                    </div>
                                    <div className="row g-3 flex-grow-1">
                                        {coreValues && coreValues.map((value, idx) => (
                                            <div key={idx} className="col-6 d-flex flex-column">
                                                <div className="d-flex align-items-center mb-2">
                                                    <i className={`bi bi-${value.icon} text-primary me-2`}></i>
                                                    <strong className="text-primary small">{value.name}</strong>
                                                </div>
                                                <p className="small mb-0 value-description">{value.description}</p>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </AnimatedSection>

            {/* Core Objectives */}
            <AnimatedSection animation="fadeUp">
            <div className="container-fluid py-5 service bg-light">
                <div className="container">
                    <div className="text-center mx-auto pb-5 section-header-container">
                        <h5 className="text-uppercase text-secondary">What we do</h5>
                        <h1 className="mb-0">Our Core Objectives</h1>
                    </div>
                    <div className="row justify-content-center core-objectives-grid">
                        {objectives && objectives.map((objective, index) => {
                            const imgUrl = objectiveImages[index % objectiveImages.length];
                            const slug = objective.title.toLowerCase().replace(/ /g, '-').replace(/&/g, 'and');
                            return (
                                <div key={index} className="col-12 col-sm-6 col-md-4 col-lg-3 core-objective-col">
                                    <div className="service-item">
                                        <img src={`/${imgUrl}`} className="img-fluid w-100" alt={objective.title} loading="lazy" width="400" height="300" />
                                        <div className="service-link">
                                            <Link href={`/initiatives?objective=${slug}`} className="h4 mb-0">{objective.title}</Link>
                                        </div>
                                    </div>
                                    <p className="my-4">{objective.description}</p>
                                    {objective.quote && (
                                        <blockquote className="blockquote mb-0">
                                            <p className="mb-0 small fst-italic text-primary">{objective.quote}</p>
                                        </blockquote>
                                    )}
                                </div>
                            );
                        })}
                        <div className="col-12">
                            <div className="d-flex align-items-center justify-content-center">
                                <Link className="btn-hover-bg btn btn-primary text-white py-2 px-4" href="/initiatives">View All Initiatives</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </AnimatedSection>

            {/* Counter Section */}
            <AnimatedSection animation="fadeUp">
            <div className="container-fluid counter py-5 counter-section-bg">
                <div className="container">
                    <div className="text-center mx-auto pb-5 section-header-container">
                        <h5 className="text-uppercase text-secondary">Our Impact</h5>
                        <h1 className="text-white mb-3">Making a Measurable Difference</h1>
                        <p className="mb-4 text-white">Through our collective efforts, we've made significant progress in empowering communities across East Africa. Every number represents lives touched and futures transformed.</p>
                    </div>
                    <div className="row">
                        <div className="col-md-6 col-lg-6 col-xl-3">
                            <div className="counter-item text-center p-5">
                                <i className="bi bi-diagram-3 text-white counter-icon"></i>
                                <h3 className="text-white my-4">Initiatives</h3>
                                <div className="counter-counting">
                                    <span className="text-white fs-2 fw-bold">{(counters?.initiatives || 0).toLocaleString()}</span>
                                    <span className="h1 fw-bold text-white">+</span>
                                </div>
                            </div>
                        </div>
                        <div className="col-md-6 col-lg-6 col-xl-3">
                            <div className="counter-item text-center p-5">
                                <i className="bi bi-calendar-check text-white counter-icon"></i>
                                <h3 className="text-white my-4">Activities</h3>
                                <div className="counter-counting text-center w-100 counter-display">
                                    <span className="text-white fs-2 fw-bold">{(counters?.events || 0).toLocaleString()}</span>
                                    <span className="h1 fw-bold text-white">+</span>
                                </div>
                            </div>
                        </div>
                        <div className="col-md-6 col-lg-6 col-xl-3">
                            <div className="counter-item text-center p-5">
                                <i className="bi bi-geo-alt text-white counter-icon"></i>
                                <h3 className="text-white my-4">Communities</h3>
                                <div className="counter-counting text-center w-100 counter-display">
                                    <span className="text-white fs-2 fw-bold">{(counters?.communities || 0).toLocaleString()}</span>
                                    <span className="h1 fw-bold text-white">+</span>
                                </div>
                            </div>
                        </div>
                        <div className="col-md-6 col-lg-6 col-xl-3">
                            <div className="counter-item text-center p-5">
                                <i className="bi bi-heart-fill text-white counter-icon"></i>
                                <h3 className="text-white my-4">Lives Changed</h3>
                                <div className="counter-counting text-center w-100 counter-display">
                                    <span className="text-white fs-2 fw-bold">{(counters?.lives_impacted || 0).toLocaleString()}</span>
                                    <span className="h1 fw-bold text-white">+</span>
                                </div>
                            </div>
                        </div>
                        <div className="col-12">
                            <div className="d-flex align-items-center justify-content-center">
                                <Link className="btn-hover-bg btn btn-primary text-white py-2 px-4" href="/coming-soon-get-involved">Join With Us</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </AnimatedSection>

            {/* Initiatives Section */}
            <div className="container-fluid py-5 causes">
                <div className="container">
                    <div className="text-center mx-auto pb-5 section-header-container">
                        <h5 className="text-uppercase text-secondary">Our Initiatives</h5>
                        <h1 className="mb-4">Strategic Initiatives for Lasting Impact</h1>
                        <p className="mb-0">We work across multiple areas to address critical needs and create lasting positive change in East African communities.</p>
                    </div>
                    <div className="row g-0 initiatives-grid">
                        {initiatives && initiatives.map((initiative, idx) => {
                            const eventCount = initiative.event_count || 0;
                            const progressPercent = eventCount > 0 ? Math.min(100, Math.round((eventCount / Math.max(eventCount + 5, 10)) * 100)) : 0;
                            const initiativeImage = initiative.image ? `/uploads/images/${initiative.image}` : '/Banners-and-portraits/pexels-speakmediauganda-33749790.jpg';
                            return (
                                <div key={idx} className="col-12 col-sm-6 col-md-4">
                                    <div className="causes-item h-100 d-flex flex-column">
                                        <div className="causes-img">
                                            <img src={initiativeImage} className="img-fluid w-100 impact-card-img" alt={initiative.title} loading="lazy" width="400" height="300" decoding="async" />
                                            <div className="causes-link pb-2 px-3">
                                                <span className="glass-pill"><i className="bi bi-calendar-check me-1"></i>Events: {eventCount}</span>
                                            </div>
                                            <div className="causes-dination p-2">
                                                <Link className="btn-hover-bg btn btn-primary text-white py-2 px-3" href={`/initiatives?initiative=${initiative.slug || ''}`}>View Details</Link>
                                            </div>
                                        </div>
                                        <div className="causes-content p-4 flex-grow-1 d-flex flex-column">
                                            <div className="mb-2">
                                                <span className="glass-pill mb-2">{initiative.objective || 'Community Development'}</span>
                                            </div>
                                            <Link className="h5 mb-3 card-title-link" href={`/initiatives?initiative=${initiative.slug || ''}`}>{initiative.title}</Link>
                                            <p className="mb-3">{(initiative.description || '').substring(0, 100)}{(initiative.description || '').length > 100 ? '...' : ''}</p>
                                            <div className="mt-auto">
                                                <progress className="initiative-progress-track progress-color-primary mb-2" value={progressPercent} max="100" aria-valuenow={progressPercent} aria-valuemin="0" aria-valuemax="100"></progress>
                                                <small className="text-muted">{progressPercent}% Complete</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                    <div className="row mt-4">
                        <div className="col-12 text-center">
                            <Link className="btn-hover-bg btn btn-primary text-white py-3 px-5" href="/initiatives">See All Initiatives</Link>
                        </div>
                    </div>
                </div>
            </div>

            {/* Events Section */}
            <div className="container-fluid event col-bg-subtle py-5">
                <div className="container">
                    <div className="text-center mx-auto mb-5 section-header-container">
                        <h5 className="text-uppercase text-secondary">Events & Activities</h5>
                        <h1 className="mb-0">Each step brings us closer to our vision of a brighter future for All. Join us in making a difference!</h1>
                    </div>
                    {!events || events.length === 0 ? (
                        <div className="text-center py-5">
                            <p className="mb-4">No upcoming events at this time. Please check back later.</p>
                            <Link className="btn-hover-bg btn btn-primary text-white py-3 px-5" href="/events">See All Events</Link>
                        </div>
                    ) : (
                        <div className="events-list-container">
                            {events.map((event, idx) => {
                                const eventDate = new Date(event.date || event.event_date);
                                const day = String(eventDate.getDate()).padStart(2, '0');
                                const month = eventDate.toLocaleString('en-US', { month: 'long' });
                                const time = eventDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
                                const eventImage = event.image ? `/uploads/images/${event.image}` : '/Banners-and-portraits/pexels-rdne-6646883.jpg';
                                return (
                                    <div key={idx} className="event-list-item">
                                        <div className="event-date-block">
                                            <div className="event-day">{day}</div>
                                            <div className="event-month">{month}</div>
                                        </div>
                                        <div className="event-image-container">
                                            <img src={eventImage} alt={event.title} className="event-image" loading="lazy" width="300" height="200" />
                                        </div>
                                        <div className="event-details">
                                            <h4 className="event-title"><Link href="/events">{event.title}</Link></h4>
                                            <p className="event-subtitle">{event.initiative || 'General'}</p>
                                            <div className="event-meta">
                                                <span className="glass-pill-sm"><i className="bi bi-geo-alt"></i></span>
                                                <span className="event-location ms-1">{event.location || ''}</span>
                                                <span className="glass-pill-sm ms-2"><i className="bi bi-clock"></i></span>
                                                <span className="event-time ms-1">{time}</span>
                                            </div>
                                        </div>
                                        <div className="event-action">
                                            <Link className="btn btn-dark btn-sm" href={`/events`}>view details</Link>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    )}
                    <div className="text-center mt-4">
                        <Link className="btn-hover-bg btn btn-primary text-white py-3 px-5" href="/events">See All Events</Link>
                    </div>
                </div>
            </div>

            {/* Stories Section */}
            <div className="container-fluid blog py-5">
                <div className="container">
                    <div className="text-center mx-auto pb-5 section-header-container">
                        <h5 className="text-uppercase text-secondary">Our Impact</h5>
                        <h1 className="mb-0">Stories of Transformation and Positive Outcomes</h1>
                        <p className="mb-0 mt-3">Discover how our activities are creating lasting change in communities across East Africa. Read, engage, and share these inspiring stories.</p>
                    </div>
                    <div className="row g-4 no-animation justify-content-center" data-disable-animation="true">
                        {!stories || stories.length === 0 ? (
                            <div className="col-12">
                                <p className="text-center py-5">No stories available at this time. Please check back later.</p>
                            </div>
                        ) : (
                            stories.map((story, idx) => {
                                const storyImage = story.image ? `/uploads/images/${story.image}` : '/Banners-and-portraits/pexels-ezeguna_graphy-sulaiman-muhammad-2153324075-34536427.jpg';
                                const storyDate = new Date(story.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                                return (
                                    <div key={idx} className="col-lg-6 col-xl-4">
                                        <div className="blog-item h-100 d-flex flex-column">
                                             <div className="blog-img">
                                                <img src={storyImage} className="img-fluid w-100 impact-card-img" alt={story.title} loading="lazy" width="400" height="300" />
                                                <div className="blog-info">
                                                    <span className="glass-pill"><i className="bi bi-clock me-1"></i>{storyDate}</span>
                                                    <div className="d-flex gap-2">
                                                        <button className="btn-like text-white border-0 bg-transparent" title="Like this story">
                                                            <span className="glass-pill"><i className="bi bi-heart me-1"></i>{story.likes || 0}</span>
                                                        </button>
                                                        <a href={`/stories/${story.slug || story.id}#comments`} className="text-white" title="View comments">
                                                            <span className="glass-pill"><i className="bi bi-chat me-1"></i>{story.comments || 0}</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="blog-content p-4 flex-grow-1 d-flex flex-column">
                                                <div className="blog-comment d-flex align-items-center mb-3">
                                                    <div className="small">
                                                        <span className="glass-pill">{story.objective || 'Community Development'}</span>
                                                    </div>
                                                </div>
                                                <Link href={`/stories/${story.slug || story.id}`} className="h4 d-inline-block mb-3 card-title-link">{story.title}</Link>
                                                <p className="mb-3 flex-grow-1">{(story.content || '').substring(0, 120)}{(story.content || '').length > 120 ? '...' : ''}</p>
                                                <Link href={`/stories/${story.slug || story.id}`} className="fw-bold card-title-link">Read More <i className="bi bi-arrow-right"></i></Link>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })
                        )}
                    </div>
                    <div className="row mt-4">
                        <div className="col-12 text-center">
                            <Link className="btn-hover-bg btn btn-primary text-white py-3 px-5" href="/stories">See All Stories</Link>
                        </div>
                    </div>
                </div>
            </div>

            {/* Gallery Section */}
            <div className="container-fluid gallery py-5">
                <div className="container py-5">
                    <div className="text-center mx-auto pb-5 section-header-container">
                        <h5 className="text-uppercase text-secondary">Our work</h5>
                        <h1 className="mb-4">Recent Activities Gallery</h1>
                        <p className="mb-0">See the impact of our programs through images from our most recent activities across East Africa.</p>
                    </div>
                    <div className="row g-0">
                        {recentActivities && recentActivities.slice(0, 6).map((activity, idx) => {
                            const actImage = activity.image ? `/uploads/images/${activity.image}` : '/Banners-and-portraits/pexels-rdne-6646918.jpg';
                            return (
                                <div key={idx} className="col-12 col-md-6 col-lg-4">
                                    <div className="gallery-item">
                                        <img className="lazy img-fluid w-100 impact-card-img" src={actImage} alt={activity.initiative || ''} width="800" height="600" decoding="async" />
                                        <div className="search-icon">
                                            <a href={actImage} data-lightbox={`gallery-${idx}`} className="my-auto"><i className="bi bi-search text-white"></i></a>
                                        </div>
                                        <div className="gallery-content">
                                            <div className="gallery-inner pb-5">
                                                <Link href="/initiatives" className="h4 card-title-link">{activity.initiative || ''}</Link>
                                                <Link href="/initiatives" className="card-title-link"><p className="mb-1">{activity.objective || ''}</p></Link>
                                                <small className="text-white-50"><i className="bi bi-geo-alt me-1"></i>{activity.location || ''}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </div>

            {/* Volunteer Section */}
            <div className="container-fluid volunteer py-5 mt-5">
                <div className="container py-5">
                    <div className="row g-5">
                        <div className="col-lg-5">
                            <div className="row g-4">
                                <div className="col-lg-6">
                                    <div className="volunteer-img">
                                        <img src="/Banners-and-portraits/pexels-belle-co-99483-1000445.jpg" className="img-fluid w-100 impact-card-img" alt="Image" loading="lazy" width="400" height="500" />
                                        <div className="volunteer-title">
                                            <h5 className="mb-2 text-white">Community Leader</h5>
                                            <p className="mb-0 text-white">Volunteer</p>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-lg-6">
                                    <div className="volunteer-img">
                                        <img src="/Banners-and-portraits/pexels-seyhmuskino-30403185.jpg" className="img-fluid w-100 impact-card-img" alt="Image" loading="lazy" width="400" height="500" />
                                        <div className="volunteer-title">
                                            <h5 className="mb-2 text-white">Program Coordinator</h5>
                                            <p className="mb-0 text-white">Volunteer</p>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-lg-6">
                                    <div className="volunteer-img">
                                        <img src="/Banners-and-portraits/pexels-seyhmuskino-30616621.jpg" className="img-fluid w-100 impact-card-img" alt="Image" loading="lazy" width="400" height="500" />
                                        <div className="volunteer-title">
                                            <h5 className="mb-2 text-white">Education Specialist</h5>
                                            <p className="mb-0 text-white">Volunteer</p>
                                        </div>
                                    </div>
                                </div>
                                <div className="col-lg-6">
                                    <div className="volunteer-img">
                                        <img src="/Banners-and-portraits/pexels-seyhmuskino-30668435.jpg" className="img-fluid w-100 impact-card-img" alt="Image" loading="lazy" width="400" height="500" />
                                        <div className="volunteer-title">
                                            <h5 className="mb-2 text-white">Healthcare Volunteer</h5>
                                            <p className="mb-0 text-white">Volunteer</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-7">
                            <h5 className="text-uppercase text-secondary">Become a Volunteer?</h5>
                            <h1 className="mb-4">Join your hand with us for a better life and beautiful future.</h1>
                            <p className="mb-4">We welcome dedicated individuals who share our passion for creating positive change. As a volunteer with Global Harmony Initiative, you'll have the opportunity to make a real difference in the lives of communities across East Africa.</p>
                            <p className="text-dark"><i className="bi bi-check-circle text-primary me-2"></i> We are friendly to each other.</p>
                            <p className="text-dark"><i className="bi bi-check-circle text-primary me-2"></i> If you join with us, we will give you free training.</p>
                            <p className="text-dark"><i className="bi bi-check-circle text-primary me-2"></i> It's an opportunity to help communities in need.</p>
                            <p className="text-dark"><i className="bi bi-check-circle text-primary me-2"></i> No goal requirements.</p>
                            <p className="text-dark mb-5"><i className="bi bi-check-circle text-primary me-2"></i> Joining is totally free. We don't need any money from you.</p>
                            <Link className="btn-hover-bg btn btn-primary text-white py-2 px-4" href="/coming-soon-get-involved">Join With Us</Link>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
