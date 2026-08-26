import PublicLayout from '../Layouts/PublicLayout';
import { Link, Head } from '@inertiajs/react';
import { useEffect, useRef } from 'react';
import { motion } from 'framer-motion';
import AnimatedSection, { AnimatedCard } from '../Components/Shared/AnimatedSection';
import SectionHeader from '../Components/Shared/SectionHeader';
import SectionCTA from '../Components/Shared/SectionCTA';
import CounterItem from '../Components/Shared/CounterItem';
import EventListItem from '../Components/Shared/EventListItem';
import VolunteerImage from '../Components/Shared/VolunteerImage';
import mediaUrl from '../Components/Shared/mediaUrl';
import FallbackImage from '../Components/Shared/FallbackImage';

Home.layout = page => <PublicLayout>{page}</PublicLayout>;

const defaultSectionOrder = ['hero', 'about', 'quote', 'foundation', 'objectives', 'counters', 'initiatives', 'events', 'stories', 'gallery', 'volunteer'];

export default function Home({ initiatives, events, stories, recentActivities, galleryImages = [], causes = [], counters, randomQuote, objectives, coreValues, heroSlides: configuredHeroSlides, sectionVisibility, sectionOrder: configuredOrder, settings = {} }) {
    const carouselRef = useRef(null);
    const sectionOrder = configuredOrder?.length === defaultSectionOrder.length ? configuredOrder : defaultSectionOrder;

    useEffect(() => {
        if (window.bootstrap && carouselRef.current) {
            const el = carouselRef.current;
            new window.bootstrap.Carousel(el, { interval: 5000, ride: 'carousel' });
        }
    }, []);

    const fallbackHeroSlides = [
        { heading: 'Global Harmony Initiative', subheading: 'Global Compassion, Local Action', image: 'Banners-and-portraits/pexels-lagosfoodbank-6472487.jpg', primaryText: 'Get Involved', primaryUrl: '/get-involved', secondaryText: 'Our Causes', secondaryUrl: '/causes' },
        { heading: 'Empowering Communities', subheading: 'Education, Health & Livelihoods', image: 'Banners-and-portraits/pexels-speakmediauganda-33749790.jpg', primaryText: 'Our Initiatives', primaryUrl: '/initiatives', secondaryText: 'Learn More', secondaryUrl: '/contact' },
        { heading: 'Making a Difference', subheading: 'Join Our Mission', image: 'Banners-and-portraits/pexels-speakmediauganda-33749791.jpg', primaryText: 'See Our Impact', primaryUrl: '/impact', secondaryText: 'Contact Us', secondaryUrl: '/contact' },
    ];
    const heroSlides = configuredHeroSlides?.length ? configuredHeroSlides : fallbackHeroSlides;
    const isSectionVisible = key => sectionVisibility?.[key] !== false;

    const objectiveImages = [
        'Banners-and-portraits/pexels-lagosfoodbank-6472487.jpg',
        'Banners-and-portraits/pexels-lagosfoodbank-8054617.jpg',
        'Banners-and-portraits/pexels-speakmediauganda-33749783.jpg',
        'Banners-and-portraits/pexels-speakmediauganda-34222337.jpg',
        'Banners-and-portraits/pexels-speakmediauganda-33749791.jpg',
    ];

    const sectionMap = {
        hero: <div key="hero" className="container-fluid carousel-header vh-100 px-0">
            <div id="carouselId" className="carousel slide carousel-fade" ref={carouselRef} data-bs-ride="carousel" data-bs-interval="5000">
                <ol className="carousel-indicators">
                    {heroSlides.map((_, index) => (
                        <li key={index} data-bs-target="#carouselId" data-bs-slide-to={index} className={index === 0 ? 'active' : ''}></li>
                    ))}
                </ol>
                <div className="carousel-inner" role="listbox">
                    {heroSlides.map((slide, index) => (
                        <div key={index} className={`carousel-item ${index === 0 ? 'active' : ''}`}>
                            <FallbackImage src={mediaUrl(slide.image)} className="img-fluid hero-carousel-img" alt={slide.heading} {...(index === 0 ? { fetchPriority: 'high' } : { loading: 'lazy' })} width="1920" height="1080" />
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
        </div>,

        about: <div key="about" className="container-fluid about py-5">
            <div className="container py-5">
                <div className="row g-5">
                    <div className="col-xl-5">
                        <div className="h-100 img-overlay">
                            <FallbackImage src="/Banners-and-portraits/pexels-magda-ehlers-pexels-2660262.jpg" className="img-fluid w-100 h-100 about-feature-img" alt="Image" loading="eager" width="800" height="600" />
                        </div>
                    </div>
                    <div className="col-xl-7">
                        <h5 className="text-uppercase text-secondary">{settings.home_about_subtitle || 'About Us'}</h5>
                        <h1 className="mb-4">{settings.home_about_title || 'Bridging Global Compassion with Local Action'}</h1>
                        <div className="fs-5 mb-4" dangerouslySetInnerHTML={{ __html: settings.home_about_description || '<p>At Global Harmony Initiative Inc., we believe that harmony begins when humanity comes together — across borders, beliefs, and backgrounds — to create lasting change. From classrooms in Kenya to community wells in Zanzibar, we connect people and resources to nurture sustainable growth and empower local leaders to build a brighter tomorrow.</p>' }} />
                        <div className="foundation-card rounded p-4 foundation-card-gradient">
                            <div className="d-flex justify-content-between align-items-start mb-3">
                                <h5 className="text-white mb-0">{settings.home_about_who_we_are_heading || 'Who We Are'}</h5>
                                <i className="bi bi-people text-white foundation-icon"></i>
                            </div>
                            <p className="mb-3 text-white">{settings.home_about_who_we_are_text_1 || 'Global Harmony Initiative Inc. (GHI) is a U.S.-registered 501(c)(3) nonprofit organization working hand in hand with communities across East Africa to alleviate poverty and promote sustainable development.'}</p>
                            <p className="mb-3 text-white">{settings.home_about_who_we_are_text_2 || 'We connect compassion with action — linking donors, volunteers, and local leaders to transform lives through education, health, and economic empowerment.'}</p>
                            <p className="mb-0 text-white">{settings.home_about_who_we_are_text_3 || 'Founded on the belief that every person deserves opportunity and dignity, GHI stands as a bridge between global goodwill and community-driven impact.'}</p>
                            <div className="mt-3">
                                <Link className="btn btn-outline-light py-2 px-4 mt-2" href="/about">{settings.home_about_button_text || 'Read More'} <i className="bi bi-arrow-right ms-1"></i></Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>,

        quote: <div key="quote" className="container-fluid py-5 quote-banner-bg" data-parallax="0.3">
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
        </div>,

        foundation: <AnimatedSection key="foundation" animation="fadeUp">
            <div className="container-fluid py-5 foundation-section-bg">
                <div className="container py-5">
                    <SectionHeader subtitle={settings.home_foundation_subtitle || 'About Us'} title={settings.home_foundation_title || 'Our Foundation'} className="mb-5" />
                    <div className="row g-4 align-items-stretch">
                        <div className="col-lg-5 d-flex flex-column">
                            <div className="foundation-card rounded p-4 mb-4 foundation-card-gradient flex-grow-1">
                                <div className="d-flex justify-content-between align-items-start mb-3">
                                    <h5 className="text-white mb-0">{settings.home_foundation_mission_heading || 'Our Mission'}</h5>
                                    <i className="bi bi-bullseye text-white foundation-icon"></i>
                                </div>
                                <p className="mb-0 text-white">{settings.home_foundation_mission_text || 'To alleviate poverty and promote sustainable development in East Africa by supporting community-led initiatives in education, health, and economic empowerment, while fostering global partnerships built on compassion and accountability.'}</p>
                            </div>
                            <div className="foundation-card rounded p-4 foundation-card-gradient flex-grow-1">
                                <div className="d-flex justify-content-between align-items-start mb-3">
                                    <h5 className="text-white mb-0">{settings.home_foundation_vision_heading || 'Our Vision'}</h5>
                                    <i className="bi bi-eye text-white foundation-icon"></i>
                                </div>
                                <p className="mb-0 text-white">{settings.home_foundation_vision_text || 'A world where communities thrive in harmony — free from poverty, empowered with opportunity, and united by shared humanity.'}</p>
                            </div>
                        </div>
                        <div className="col-lg-7 d-flex">
                            <div className="foundation-card bg-white rounded p-4 w-100 d-flex flex-column">
                                <div className="d-flex justify-content-between align-items-start mb-3">
                                    <h5 className="text-primary mb-0">{settings.home_foundation_values_heading || 'Our Values'}</h5>
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
        </AnimatedSection>,

        objectives: <AnimatedSection key="objectives" animation="fadeUp">
            <div className="container-fluid py-5 service bg-light">
                <div className="container">
                    <SectionHeader subtitle={settings.home_objectives_subtitle || 'What we do'} title={settings.home_objectives_title || 'Our Core Objectives'} className="pb-5" />
                    {causes && causes.length > 0 ? (
                        <div className="row justify-content-center core-objectives-grid">
                            {causes.slice(0, 5).map((cause, index) => {
                                const imgUrl = cause.image || objectiveImages[index % objectiveImages.length];
                                return (
                                    <div key={cause.id} className="col-12 col-sm-6 col-md-4 col-lg-3 core-objective-col">
                                        <div className="service-item">
                                            <FallbackImage src={mediaUrl(imgUrl)} className="img-fluid w-100" alt={cause.title} loading="lazy" width="400" height="300" />
                                            <div className="service-link">
                                                <Link href="/causes" className="h4 mb-0">{cause.title}</Link>
                                            </div>
                                        </div>
                                        <p className="my-4">{cause.description ? cause.description.replace(/<[^>]+>/g, '').substring(0, 150) + (cause.description.length > 150 ? '...' : '') : ''}</p>
                                        {cause.quote && (
                                            <blockquote className="blockquote mb-0">
                                                <p className="mb-0 small fst-italic text-primary" dangerouslySetInnerHTML={{ __html: cause.quote }} />
                                            </blockquote>
                                        )}
                                    </div>
                                );
                            })}
                            <SectionCTA href="/causes">View All Causes</SectionCTA>
                        </div>
                    ) : (
                        <div className="text-center py-5">
                            <p className="mb-3 text-muted">No objectives configured yet. Check back soon!</p>
                        </div>
                    )}
                </div>
            </div>
        </AnimatedSection>,

        counters: <AnimatedSection key="counters" animation="fadeUp">
            <div className="container-fluid counter py-5 counter-section-bg">
                <div className="container">
                    <SectionHeader subtitle={settings.home_counters_subtitle || 'Our Impact'} title={settings.home_counters_title || 'Making a Measurable Difference'} description={settings.home_counters_description || "Through our collective efforts, we've made significant progress in empowering communities across East Africa. Every number represents lives touched and futures transformed."} light className="pb-5" />
                    <div className="row">
                        <CounterItem icon="diagram-3" label="Initiatives" value={counters?.initiatives} />
                        <CounterItem icon="calendar-check" label="Activities" value={counters?.events} />
                        <CounterItem icon="geo-alt" label="Communities" value={counters?.communities} />
                        <CounterItem icon="heart-fill" label="Lives Changed" value={counters?.lives_impacted} />
                        <SectionCTA href="/coming-soon-get-involved">{settings.home_counters_button_text || 'Join With Us'}</SectionCTA>
                    </div>
                </div>
            </div>
        </AnimatedSection>,

        initiatives: <div key="initiatives" className="container-fluid py-5 causes">
            <div className="container">
                <SectionHeader subtitle={settings.home_initiatives_subtitle || 'Our Initiatives'} title={settings.home_initiatives_title || 'Strategic Initiatives for Lasting Impact'} description={settings.home_initiatives_description || 'We work across multiple areas to address critical needs and create lasting positive change in East African communities.'} className="pb-5" />
                {initiatives && initiatives.length > 0 ? (
                    <>
                        <div className="row g-0 initiatives-grid">
                            {initiatives.map((initiative, idx) => {
                                const eventCount = initiative.event_count || 0;
                                const progressPercent = eventCount > 0 ? Math.min(100, Math.round((eventCount / Math.max(eventCount + 5, 10)) * 100)) : 0;
                                const initiativeImage = initiative.image ? mediaUrl(initiative.image) : '/Banners-and-portraits/pexels-speakmediauganda-33749790.jpg';
                                return (
                                    <div key={idx} className="col-12 col-sm-6 col-md-4">
                                        <div className="causes-item h-100 d-flex flex-column">
                                            <div className="causes-img">
                                                <FallbackImage src={initiativeImage} className="img-fluid w-100 impact-card-img" alt={initiative.title} loading="lazy" width="400" height="300" decoding="async" />
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
                            <SectionCTA href="/initiatives">{settings.home_initiatives_all_button_text || 'See All Initiatives'}</SectionCTA>
                        </div>
                    </>
                ) : (
                    <div className="text-center py-5">
                        <p className="mb-3 text-muted">{settings.home_initiatives_empty_message || 'No initiatives configured.'}</p>
                    </div>
                )}
            </div>
        </div>,

        events: <div key="events" className="container-fluid event col-bg-subtle py-5">
            <div className="container">
                <SectionHeader subtitle={settings.home_events_subtitle || 'Events & Activities'} title={settings.home_events_title || 'Each step brings us closer to our vision of a brighter future for All. Join us in making a difference!'} className="mb-5" />
                {!events || events.length === 0 ? (
                    <div className="text-center py-5">
                        <p className="mb-4">{settings.home_events_empty_message || 'No upcoming events at this time. Please check back later.'}</p>
                    </div>
                ) : (
                    <>
                        <div className="events-list-container">
                            {events.map((event, idx) => (
                                <EventListItem key={idx} event={event} />
                            ))}
                        </div>
                        <div className="text-center mt-4">
                            <SectionCTA href="/events">See All Events</SectionCTA>
                        </div>
                    </>
                )}
            </div>
        </div>,

        stories: <div key="stories" className="container-fluid blog py-5">
            <div className="container">
                <SectionHeader subtitle={settings.home_stories_subtitle || 'Our Impact'} title={settings.home_stories_title || 'Stories of Transformation and Positive Outcomes'} description={settings.home_stories_description || 'Discover how our activities are creating lasting change in communities across East Africa. Read, engage, and share these inspiring stories.'} className="pb-5" />
                <div className="row g-4 no-animation justify-content-center" data-disable-animation="true">
                    {!stories || stories.length === 0 ? (
                        <div className="col-12">
                            <p className="text-center py-5">{settings.home_stories_empty_message || 'No stories available at this time. Please check back later.'}</p>
                        </div>
                    ) : (
                        stories.map((story, idx) => {
                            const storyImage = story.image ? mediaUrl(story.image) : '/Banners-and-portraits/pexels-ezeguna_graphy-sulaiman-muhammad-2153324075-34536427.jpg';
                            const storyDate = new Date(story.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                            return (
                                <div key={idx} className="col-lg-6 col-xl-4">
                                    <div className="blog-item h-100 d-flex flex-column">
                                        <div className="blog-img">
                                            <FallbackImage src={storyImage} className="img-fluid w-100 impact-card-img" alt={story.title} loading="lazy" width="400" height="300" />
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
                {stories && stories.length > 0 && (
                    <div className="row mt-4">
                        <SectionCTA href="/stories">See All Stories</SectionCTA>
                    </div>
                )}
            </div>
        </div>,

        gallery: <div key="gallery" className="container-fluid gallery py-5">
            <div className="container py-5">
                <SectionHeader subtitle={settings.home_gallery_subtitle || 'Our work'} title={settings.home_gallery_title || 'Recent Activities Gallery'} description={settings.home_gallery_description || 'See the impact of our programs through images from our most recent activities across East Africa.'} className="pb-5" />
                <div className="row g-0">
                    {galleryImages.length > 0 ? galleryImages.slice(0, 6).map((img, idx) => {
                        const actMedia = mediaUrl(img.path);
                        const isVideo = img.type === 'video';
                        return (
                            <div key={idx} className="col-12 col-md-6 col-lg-4">
                                <div className="gallery-item">
                                    {isVideo ? (
                                        <video className="lazy img-fluid w-100 impact-card-img" src={actMedia} muted loop playsInline preload="metadata" />
                                    ) : (
                                        <FallbackImage className="lazy img-fluid w-100 impact-card-img" src={actMedia} alt={img.initiative || ''} width="800" height="600" decoding="async" />
                                    )}
                                    <div className="search-icon">
                                        {isVideo ? (
                                            <a href={actMedia} className="my-auto"><i className="bi bi-play-circle text-white"></i></a>
                                        ) : (
                                            <a href={actMedia} data-lightbox={`gallery-${idx}`} className="my-auto"><i className="bi bi-search text-white"></i></a>
                                        )}
                                    </div>
                                    <div className="gallery-content">
                                        <div className="gallery-inner pb-5">
                                            <Link href="/initiatives" className="h4 card-title-link">{img.initiative || ''}</Link>
                                            <p className="mb-1">{img.event_title || ''}</p>
                                            <small className="text-white-50"><i className="bi bi-geo-alt me-1"></i>{img.location || ''}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        );
                    }) : recentActivities && recentActivities.slice(0, 6).map((activity, idx) => {
                        const actImage = activity.image ? mediaUrl(activity.image) : '/Banners-and-portraits/pexels-rdne-6646918.jpg';
                        return (
                            <div key={idx} className="col-12 col-md-6 col-lg-4">
                                <div className="gallery-item">
                                    <FallbackImage className="lazy img-fluid w-100 impact-card-img" src={actImage} alt={activity.initiative || ''} width="800" height="600" decoding="async" />
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
        </div>,

        volunteer: <div key="volunteer" className="container-fluid volunteer py-5 mt-5">
            <div className="container py-5">
                <div className="row g-5">
                    <div className="col-lg-5">
                        <div className="row g-4">
                            <VolunteerImage src="/Banners-and-portraits/pexels-belle-co-99483-1000445.jpg" title="Community Leader" role="Volunteer" />
                            <VolunteerImage src="/Banners-and-portraits/pexels-seyhmuskino-30403185.jpg" title="Program Coordinator" role="Volunteer" />
                            <VolunteerImage src="/Banners-and-portraits/pexels-seyhmuskino-30616621.jpg" title="Education Specialist" role="Volunteer" />
                            <VolunteerImage src="/Banners-and-portraits/pexels-seyhmuskino-30668435.jpg" title="Healthcare Volunteer" role="Volunteer" />
                        </div>
                    </div>
                    <div className="col-lg-7">
                        <h5 className="text-uppercase text-secondary">{settings.home_volunteer_subtitle || 'Become a Volunteer?'}</h5>
                        <h1 className="mb-4">{settings.home_volunteer_title || 'Join your hand with us for a better life and beautiful future.'}</h1>
                        <p className="mb-4">{settings.home_volunteer_description || "We welcome dedicated individuals who share our passion for creating positive change. As a volunteer with Global Harmony Initiative, you'll have the opportunity to make a real difference in the lives of communities across East Africa."}</p>
                        <p className="text-dark"><i className="bi bi-check-circle text-primary me-2"></i> {settings.home_volunteer_bullet_1 || 'We are friendly to each other.'}</p>
                        <p className="text-dark"><i className="bi bi-check-circle text-primary me-2"></i> {settings.home_volunteer_bullet_2 || 'If you join with us, we will give you free training.'}</p>
                        <p className="text-dark"><i className="bi bi-check-circle text-primary me-2"></i> {settings.home_volunteer_bullet_3 || "It's an opportunity to help communities in need."}</p>
                        <p className="text-dark"><i className="bi bi-check-circle text-primary me-2"></i> {settings.home_volunteer_bullet_4 || 'No goal requirements.'}</p>
                        <p className="text-dark mb-5"><i className="bi bi-check-circle text-primary me-2"></i> {settings.home_volunteer_bullet_5 || "Joining is totally free. We don't need any money from you."}</p>
                        <Link className="btn-hover-bg btn btn-primary text-white py-2 px-4 mt-4" href="/coming-soon-get-involved">{settings.home_volunteer_button_text || 'Join With Us'}</Link>
                    </div>
                </div>
            </div>
        </div>,
    };

    return (
        <>
            <Head>
                <title>Global Harmony Initiative - Global Compassion, Local Action</title>
                <meta name="description" content="Global Harmony Initiative is a U.S.-registered 501(c)(3) nonprofit organization working in East Africa to create positive change through education, healthcare, and community development." />
            </Head>
            {sectionOrder.map(key => isSectionVisible(key) ? sectionMap[key] : null)}
        </>
    );
}
