import PublicLayout from '../Layouts/PublicLayout';
import { Head } from '@inertiajs/react';
import AnimatedSection from '../Components/Shared/AnimatedSection';
import SectionHeader from '../Components/Shared/SectionHeader';
import SectionCTA from '../Components/Shared/SectionCTA';
import PageHeader from '../Components/Shared/PageHeader';
import FallbackImage from '../Components/Shared/FallbackImage';
import mediaUrl from '../Components/Shared/mediaUrl';
import sanitizeHtml from '../Components/Shared/sanitizeHtml';

About.layout = page => <PublicLayout>{page}</PublicLayout>;

const fallbackCoreValues = [
    { icon: 'heart', name: 'Compassion', description: 'We lead with empathy and care for every community we serve.' },
    { icon: 'shield', name: 'Accountability', description: 'We maintain transparency in every action and resource we manage.' },
    { icon: 'people', name: 'Collaboration', description: 'We believe in the power of partnerships to create lasting change.' },
    { icon: 'lightbulb', name: 'Empowerment', description: 'We enable communities to lead their own transformation.' },
    { icon: 'globe', name: 'Sustainability', description: 'We build programs designed to endure and grow beyond our involvement.' },
    { icon: 'hand-thumbs-up', name: 'Dignity', description: 'We honor the humanity and potential in every person we serve.' },
];

export default function About({ settings, coreValues }) {
    const values = coreValues?.length ? coreValues : fallbackCoreValues;
    const methodologyImage = settings?.about_methodology_image ? mediaUrl(settings.about_methodology_image) : '/Banners-and-portraits/pexels-speakmediauganda-33749790.jpg';

    const pillars = [
        { icon: 'people', title: 'Partnership-Based Approach', desc: 'Work with vetted local organizations and grassroots leaders in East Africa.' },
        { icon: 'geo-alt', title: 'Direct Implementation', desc: 'Manage demonstration projects that showcase effective community-led development.' },
        { icon: 'clipboard-check', title: 'Transparency & Accountability', desc: 'Employ robust reporting, monitoring, and evaluation systems.' },
        { icon: 'globe', title: 'Global Engagement & Volunteer Network', desc: 'Mobilize the East African diaspora and international supporters through volunteerism, donations, and advocacy.' },
    ];

    return (
        <>
            <Head title={(settings?.about_hero_title || 'About Us') + ' - Global Harmony Initiative'} />

            {/* Hero */}
            <PageHeader
                title={settings?.about_hero_title || 'About Global Harmony Initiative'}
                subtitle={settings?.about_hero_subtitle || 'Bridging Global Compassion with Local Action'}
                image={settings?.about_hero_image}
                breadcrumb={[{ label: 'About Us' }]}
            />

            {/* Background */}
            <AnimatedSection animation="fadeUp">
                <div className="container-fluid py-5 about">
                    <div className="container py-5">
                        <div className="row g-5 align-items-center">
                            <div className="col-xl-7">
                                <SectionHeader subtitle="Who We Are" title={settings?.about_background_heading || 'Background'} className="text-start mb-4" />
                                <div dangerouslySetInnerHTML={{ __html: sanitizeHtml(settings?.about_background_content || '') }} />
                            </div>
                            <div className="col-xl-5">
                                <FallbackImage src={methodologyImage} className="img-fluid w-100 rounded" alt="About GHI" loading="lazy" width="800" height="600" />
                            </div>
                        </div>
                    </div>
                </div>
            </AnimatedSection>

            {/* Vision & Mission (reuses Foundation pattern) */}
            <AnimatedSection animation="fadeUp">
                <div className="container-fluid py-5 foundation-section-bg">
                    <div className="container py-5">
                        <SectionHeader subtitle="Our Foundation" title="Vision & Mission" className="mb-5" />
                        <div className="row g-4 align-items-stretch">
                            <div className="col-lg-6">
                                <div className="foundation-card rounded p-4 h-100 foundation-card-gradient">
                                    <div className="d-flex justify-content-between align-items-start mb-3">
                                        <h5 className="text-white mb-0">Our Vision</h5>
                                        <i className="bi bi-eye text-white foundation-icon"></i>
                                    </div>
                                    <p className="mb-0 text-white fs-5">A world where communities thrive in harmony — free from poverty, empowered with opportunity, and united by shared humanity.</p>
                                </div>
                            </div>
                            <div className="col-lg-6">
                                <div className="foundation-card rounded p-4 h-100 foundation-card-gradient">
                                    <div className="d-flex justify-content-between align-items-start mb-3">
                                        <h5 className="text-white mb-0">Our Mission</h5>
                                        <i className="bi bi-bullseye text-white foundation-icon"></i>
                                    </div>
                                    <p className="mb-0 text-white">To alleviate poverty and promote sustainable development in East Africa by supporting community-led initiatives in education, health, and economic empowerment, while fostering global partnerships built on compassion and accountability.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </AnimatedSection>

            {/* Core Values */}
            <AnimatedSection animation="fadeUp">
                <div className="container-fluid py-5">
                    <div className="container">
                        <SectionHeader subtitle="What Drives Us" title="Our Core Values" className="mb-5" />
                        <div className="row g-4">
                            {values.map((value, idx) => (
                                <div key={idx} className="col-md-6 col-lg-4">
                                    <div className="content-card h-100 text-center p-4">
                                        <i className={`bi bi-${value.icon} fs-1 text-primary mb-3`}></i>
                                        <h5>{value.name}</h5>
                                        <p className="text-muted mb-0">{value.description}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </AnimatedSection>

            {/* Methodology */}
            <AnimatedSection animation="fadeUp">
                <div className="container-fluid py-5 service bg-light">
                    <div className="container">
                        <SectionHeader subtitle="How We Work" title={settings?.about_methodology_heading || 'Methodology'} description={settings?.about_methodology_content ? settings.about_methodology_content.replace(/<[^>]+>/g, '') : ''} className="pb-5" />
                        <div className="row g-4">
                            {pillars.map((pillar, idx) => (
                                <div key={idx} className="col-md-6 col-lg-3">
                                    <div className="content-card h-100 text-center p-4">
                                        <div className="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white mb-3" style={{ width: '60px', height: '60px' }}>
                                            <i className={`bi bi-${pillar.icon} fs-4`}></i>
                                        </div>
                                        <h5>{pillar.title}</h5>
                                        <p className="text-muted mb-0 small">{pillar.desc}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </AnimatedSection>

            {/* Get Involved CTA */}
            <div className="container-fluid py-5 quote-banner-bg" data-parallax="0.3">
                <div className="container py-5 text-center">
                    <SectionHeader subtitle="Join Us" title="Make a Difference Today" light className="mb-4" />
                    <p className="text-white-50 mb-4 mx-auto" style={{ maxWidth: '600px' }}>
                        Whether you volunteer, donate, or spread awareness — your involvement helps transform lives across East Africa.
                    </p>
                    <SectionCTA href="/get-involved">Get Involved</SectionCTA>
                </div>
            </div>
        </>
    );
}
