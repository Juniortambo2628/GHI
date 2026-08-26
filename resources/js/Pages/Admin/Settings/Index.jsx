import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';
import AdminLayout from '../../../Layouts/AdminLayout';
import AdminDropdown, { AdminDropdownItem } from '../../../Components/Shared/AdminDropdown';
import ImageUploadField from '../../../Components/Shared/ImageUploadField';
import RichTextField from '../../../Components/Shared/RichTextField';
import RouteSelector from '../../../Components/Shared/RouteSelector';

const defaultSlide = { heading: '', subheading: '', image: '', primaryText: '', primaryUrl: '/', secondaryText: '', secondaryUrl: '/' };

const sectionLabels = {
    hero: 'Hero Carousel',
    about: 'About Us',
    quote: 'Quote Banner',
    foundation: 'Foundation (Mission, Vision, Values)',
    objectives: 'Core Objectives',
    counters: 'Impact Counters',
    initiatives: 'Initiatives Grid',
    events: 'Events List',
    stories: 'Stories Grid',
    gallery: 'Activity Gallery',
    volunteer: 'Volunteer CTA',
};

const defaultOrder = Object.keys(sectionLabels);

const pageHeroKeys = [
    { key: 'causes', label: 'Causes', icon: 'bi-heart' },
    { key: 'initiatives', label: 'Initiatives', icon: 'bi-rocket' },
    { key: 'events', label: 'Events', icon: 'bi-calendar-event' },
    { key: 'impact', label: 'Impact', icon: 'bi-bar-chart' },
    { key: 'stories', label: 'Stories', icon: 'bi-journal-text' },
    { key: 'contact', label: 'Contact', icon: 'bi-envelope' },
    { key: 'get_involved', label: 'Get Involved', icon: 'bi-people' },
];

const settingsSections = [
    { key: 'branding', label: 'Site Branding', icon: 'bi-palette' },
    { key: 'info', label: 'Site Information', icon: 'bi-info-circle' },
    { key: 'hero', label: 'Hero Slides', icon: 'bi-image' },
    { key: 'sections', label: 'Homepage Sections', icon: 'bi-layout-split' },
    { key: 'homepage', label: 'Homepage Content', icon: 'bi-house' },
    { key: 'pages', label: 'Page Heroes', icon: 'bi-window' },
    { key: 'about', label: 'About Page', icon: 'bi-file-person' },
];

function PageHeroEditor({ page, data, setData }) {
    const prefix = `hero_${page.key}`;
    return (
        <div className="content-card mb-3">
            <div className="card-header d-flex align-items-center gap-2">
                <i className={`bi ${page.icon}`}></i>
                <h6 className="mb-0">{page.label} Page</h6>
            </div>
            <div className="card-body">
                <div className="row g-3">
                    <div className="col-md-6">
                        <label className="form-label">Hero Title</label>
                        <input type="text" className="form-control" value={data[`${prefix}_title`] || ''} onChange={(e) => setData(`${prefix}_title`, e.target.value)} />
                    </div>
                    <div className="col-md-6">
                        <label className="form-label">Hero Subtitle</label>
                        <input type="text" className="form-control" value={data[`${prefix}_subtitle`] || ''} onChange={(e) => setData(`${prefix}_subtitle`, e.target.value)} />
                    </div>
                    <div className="col-12">
                        <ImageUploadField
                            name={`${prefix}_image`}
                            label="Hero Background Image"
                            value={data[`${prefix}_image`] || ''}
                            onChange={(val) => setData(`${prefix}_image`, val)}
                        />
                    </div>
                    <div className="col-md-6">
                        <label className="form-label">Button Text</label>
                        <input type="text" className="form-control" value={data[`${prefix}_button_text`] || ''} onChange={(e) => setData(`${prefix}_button_text`, e.target.value)} placeholder="e.g. Learn More" />
                    </div>
                    <div className="col-md-6">
                        <label className="form-label">Button Link</label>
                        <RouteSelector
                            value={data[`${prefix}_button_url`] || ''}
                            onChange={(val) => setData(`${prefix}_button_url`, val)}
                            placeholder="Select a page..."
                        />
                    </div>
                </div>
            </div>
        </div>
    );
}

export default function Index({ settings }) {
    const heroSlides = JSON.parse(settings.homepage_hero || '[]');
    const sectionVis = JSON.parse(settings.homepage_sections || '{}');
    const savedOrder = JSON.parse(settings.homepage_section_order || '[]');
    const sectionOrder = savedOrder.length === defaultOrder.length ? savedOrder : defaultOrder;

    const { data, setData, put, processing } = useForm({
        ...settings,
        homepage_hero: settings.homepage_hero || '[]',
        homepage_sections: settings.homepage_sections || '{}',
        homepage_section_order: settings.homepage_section_order || JSON.stringify(defaultOrder),
    });

    const [slides, setSlides] = useState(heroSlides.length ? heroSlides : [{ ...defaultSlide }]);
    const [sections, setSections] = useState(
        Object.keys(sectionLabels).reduce((acc, key) => {
            acc[key] = sectionVis[key] !== false;
            return acc;
        }, {})
    );
    const [order, setOrder] = useState(sectionOrder);
    const [dragIdx, setDragIdx] = useState(null);
    const [activeSection, setActiveSection] = useState('branding');
    const [activePage, setActivePage] = useState(pageHeroKeys[0].key);

    const textFields = [
        { key: 'site_name', label: 'Site Name', col: 'col-md-6' },
        { key: 'site_tagline', label: 'Site Tagline', col: 'col-md-6' },
        { key: 'contact_email', label: 'Contact Email', col: 'col-md-6' },
        { key: 'contact_phone', label: 'Contact Phone', col: 'col-md-6' },
        { key: 'facebook_url', label: 'Facebook URL', col: 'col-md-6' },
        { key: 'instagram_url', label: 'Instagram URL', col: 'col-md-6' },
        { key: 'twitter_url', label: 'Twitter / X URL', col: 'col-md-6' },
        { key: 'linkedin_url', label: 'LinkedIn URL', col: 'col-md-6' },
    ];
    const textareaFields = [
        { key: 'site_description', label: 'Site Description', col: 'col-12' },
        { key: 'footer_text', label: 'Footer Text', col: 'col-12' },
    ];

    const updateSlide = (index, field, value) => {
        const next = slides.map((s, i) => (i === index ? { ...s, [field]: value } : s));
        setSlides(next);
        setData('homepage_hero', JSON.stringify(next));
    };

    const addSlide = () => setSlides([...slides, { ...defaultSlide }]);
    const removeSlide = (index) => {
        const next = slides.filter((_, i) => i !== index);
        setSlides(next);
        setData('homepage_hero', JSON.stringify(next));
    };

    const toggleSection = (key) => {
        const next = { ...sections, [key]: !sections[key] };
        setSections(next);
        setData('homepage_sections', JSON.stringify(next));
    };

    const onDragStart = (e, idx) => {
        setDragIdx(idx);
        e.dataTransfer.effectAllowed = 'move';
    };

    const onDragOver = (e, idx) => {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        if (dragIdx === null || dragIdx === idx) return;
        const next = [...order];
        const [moved] = next.splice(dragIdx, 1);
        next.splice(idx, 0, moved);
        setOrder(next);
        setDragIdx(idx);
    };

    const onDragEnd = () => {
        setDragIdx(null);
        setData('homepage_section_order', JSON.stringify(order));
    };

    const submit = (e) => {
        e.preventDefault();
        put('/admin/settings');
    };

    const currentSection = settingsSections.find(s => s.key === activeSection);
    const currentPage = pageHeroKeys.find(p => p.key === activePage);

    const toolbarLeft = (
        <AdminDropdown
            openUp
            trigger={
                <button type="button" className="btn btn-sm" style={{ background: 'var(--admin-primary)', color: '#fff', borderColor: 'var(--admin-primary)' }}>
                    <i className={`bi ${currentSection?.icon || 'bi-gear'} me-1`}></i>
                    {currentSection?.label || 'Settings'}
                    <i className="bi bi-chevron-up ms-1" style={{ fontSize: '0.65rem' }}></i>
                </button>
            }
        >
            {settingsSections.map(section => (
                <AdminDropdownItem
                    key={section.key}
                    icon={section.icon}
                    onClick={(e) => { e.preventDefault(); setActiveSection(section.key); }}
                >
                    {section.label}
                </AdminDropdownItem>
            ))}
        </AdminDropdown>
    );

    const toolbarRight = activeSection === 'pages' ? (
        <AdminDropdown
            openUp
            trigger={
                <button type="button" className="btn btn-sm" style={{ background: 'var(--admin-primary)', color: '#fff', borderColor: 'var(--admin-primary)' }}>
                    <i className={`bi ${currentPage?.icon || 'bi-window'} me-1`}></i>
                    {currentPage?.label || 'Select Page'}
                    <i className="bi bi-chevron-up ms-1" style={{ fontSize: '0.65rem' }}></i>
                </button>
            }
        >
            {pageHeroKeys.map(page => (
                <AdminDropdownItem
                    key={page.key}
                    icon={page.icon}
                    onClick={(e) => { e.preventDefault(); setActivePage(page.key); }}
                >
                    {page.label}
                </AdminDropdownItem>
            ))}
        </AdminDropdown>
    ) : null;

    return (
        <AdminLayout
            title="Settings"
            description={`Configure ${currentSection?.label?.toLowerCase() || 'your site'}.`}
            onSave={submit}
            saveLabel="Save Settings"
            saveProcessing={processing}
            toolbarLeft={toolbarLeft}
            toolbarRight={toolbarRight}
        >
            <Head title="Settings - Admin" />

            <form onSubmit={submit}>
                {activeSection === 'branding' && (
                    <div className="content-card mb-4">
                        <div className="card-header"><h5 className="mb-0">Site Branding</h5></div>
                        <div className="card-body">
                            <div className="row g-3">
                                <div className="col-md-6">
                                    <ImageUploadField name="site_logo" label="Site Logo" value={data.site_logo || ''} onChange={(val) => setData('site_logo', val)} />
                                </div>
                                <div className="col-md-6">
                                    <ImageUploadField name="site_favicon" label="Favicon" value={data.site_favicon || ''} onChange={(val) => setData('site_favicon', val)} />
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {activeSection === 'info' && (
                    <div className="content-card mb-4">
                        <div className="card-header"><h5 className="mb-0">Site Information</h5></div>
                        <div className="card-body">
                            <div className="row g-3">
                                {textFields.map(({ key, label, col }) => (
                                    <div key={key} className={col}>
                                        <label className="form-label">{label}</label>
                                        <input type="text" className="form-control" value={data[key] || ''} onChange={(e) => setData(key, e.target.value)} />
                                    </div>
                                ))}
                                {textareaFields.map(({ key, label, col }) => (
                                    <div key={key} className={col}>
                                        <label className="form-label">{label}</label>
                                        <textarea className="form-control" rows={4} value={data[key] || ''} onChange={(e) => setData(key, e.target.value)} />
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                )}

                {activeSection === 'hero' && (
                    <div className="content-card mb-4">
                        <div className="card-header d-flex justify-content-between align-items-center">
                            <h5 className="mb-0">Homepage Hero Slides</h5>
                            <button type="button" className="btn btn-sm btn-outline-primary" onClick={addSlide}>
                                <i className="bi bi-plus-circle me-1"></i>Add Slide
                            </button>
                        </div>
                        <div className="card-body">
                            {slides.map((slide, index) => (
                                <div key={index} className="content-card mb-3">
                                    <div className="card-header d-flex justify-content-between align-items-center">
                                        <strong>Slide {index + 1}</strong>
                                        {slides.length > 1 && (
                                            <button type="button" className="btn btn-sm btn-outline-danger" onClick={() => removeSlide(index)}>
                                                <i className="bi bi-trash"></i>
                                            </button>
                                        )}
                                    </div>
                                    <div className="card-body">
                                        <div className="row g-3">
                                            <div className="col-md-6">
                                                <label className="form-label">Heading</label>
                                                <input type="text" className="form-control" value={slide.heading} onChange={(e) => updateSlide(index, 'heading', e.target.value)} />
                                            </div>
                                            <div className="col-md-6">
                                                <label className="form-label">Subheading</label>
                                                <input type="text" className="form-control" value={slide.subheading} onChange={(e) => updateSlide(index, 'subheading', e.target.value)} />
                                            </div>
                                            <div className="col-12">
                                                <ImageUploadField name={`hero-image-${index}`} value={slide.image} onChange={(value) => updateSlide(index, 'image', value)} />
                                            </div>
                                            <div className="col-md-3">
                                                <label className="form-label">Primary Button Text</label>
                                                <input type="text" className="form-control" value={slide.primaryText} onChange={(e) => updateSlide(index, 'primaryText', e.target.value)} />
                                            </div>
                                            <div className="col-md-3">
                                                <label className="form-label">Primary Button Link</label>
                                                <RouteSelector value={slide.primaryUrl} onChange={(val) => updateSlide(index, 'primaryUrl', val)} />
                                            </div>
                                            <div className="col-md-3">
                                                <label className="form-label">Secondary Button Text</label>
                                                <input type="text" className="form-control" value={slide.secondaryText} onChange={(e) => updateSlide(index, 'secondaryText', e.target.value)} />
                                            </div>
                                            <div className="col-md-3">
                                                <label className="form-label">Secondary Button Link</label>
                                                <RouteSelector value={slide.secondaryUrl} onChange={(val) => updateSlide(index, 'secondaryUrl', val)} />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {activeSection === 'sections' && (
                    <div className="content-card mb-4">
                        <div className="card-header"><h5 className="mb-0">Homepage Sections</h5></div>
                        <div className="card-body">
                            <p className="text-muted small mb-3">Drag to reorder sections on the homepage. Toggle visibility with the switch.</p>
                            <div className="section-order-list">
                                {order.map((key, idx) => (
                                    <div
                                        key={key}
                                        className={`section-order-item d-flex align-items-center gap-3 p-2 mb-2 border rounded ${dragIdx === idx ? 'opacity-50' : ''}`}
                                        draggable
                                        onDragStart={(e) => onDragStart(e, idx)}
                                        onDragOver={(e) => onDragOver(e, idx)}
                                        onDragEnd={onDragEnd}
                                    >
                                        <i className="bi bi-grip-vertical text-muted" style={{ cursor: 'grab' }}></i>
                                        <div className={`form-check form-switch section-toggle flex-grow-1 ${sections[key] ? 'active' : ''}`}>
                                            <input className="form-check-input" type="checkbox" id={`section-${key}`} checked={sections[key]} onChange={() => toggleSection(key)} />
                                            <label className="form-check-label" htmlFor={`section-${key}`}>{sectionLabels[key]}</label>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                )}

                {activeSection === 'pages' && currentPage && (
                    <div>
                        <p className="text-muted small mb-3">Configure the hero section for each public page: title, subtitle, background image, and call-to-action button. Use the page switcher in the toolbar to edit each page.</p>
                        <PageHeroEditor key={activePage} page={currentPage} data={data} setData={setData} />
                    </div>
                )}

                {activeSection === 'homepage' && (
                    <div>
                        {[
                            { key: 'about', title: 'About Section', fields: [
                                { k: 'home_about_subtitle', l: 'Subtitle', type: 'text' },
                                { k: 'home_about_title', l: 'Title', type: 'text' },
                                { k: 'home_about_description', l: 'Description', type: 'richtext' },
                                { k: 'home_about_who_we_are_heading', l: '"Who We Are" Heading', type: 'text' },
                                { k: 'home_about_who_we_are_text_1', l: '"Who We Are" Paragraph 1', type: 'text' },
                                { k: 'home_about_who_we_are_text_2', l: '"Who We Are" Paragraph 2', type: 'text' },
                                { k: 'home_about_who_we_are_text_3', l: '"Who We Are" Paragraph 3', type: 'text' },
                                { k: 'home_about_button_text', l: 'Button Text', type: 'text' },
                            ]},
                            { key: 'foundation', title: 'Foundation Section', fields: [
                                { k: 'home_foundation_subtitle', l: 'Subtitle', type: 'text' },
                                { k: 'home_foundation_title', l: 'Title', type: 'text' },
                                { k: 'home_foundation_mission_heading', l: 'Mission Heading', type: 'text' },
                                { k: 'home_foundation_mission_text', l: 'Mission Text', type: 'richtext' },
                                { k: 'home_foundation_vision_heading', l: 'Vision Heading', type: 'text' },
                                { k: 'home_foundation_vision_text', l: 'Vision Text', type: 'richtext' },
                                { k: 'home_foundation_values_heading', l: 'Values Heading', type: 'text' },
                            ]},
                            { key: 'objectives', title: 'Objectives Section', fields: [
                                { k: 'home_objectives_subtitle', l: 'Subtitle', type: 'text' },
                                { k: 'home_objectives_title', l: 'Title', type: 'text' },
                            ]},
                            { key: 'counters', title: 'Counters Section', fields: [
                                { k: 'home_counters_subtitle', l: 'Subtitle', type: 'text' },
                                { k: 'home_counters_title', l: 'Title', type: 'text' },
                                { k: 'home_counters_description', l: 'Description', type: 'richtext' },
                                { k: 'home_counters_button_text', l: 'Button Text', type: 'text' },
                            ]},
                            { key: 'initiatives', title: 'Initiatives Section', fields: [
                                { k: 'home_initiatives_subtitle', l: 'Subtitle', type: 'text' },
                                { k: 'home_initiatives_title', l: 'Title', type: 'text' },
                                { k: 'home_initiatives_description', l: 'Description', type: 'richtext' },
                                { k: 'home_initiatives_empty_message', l: 'Empty State Message', type: 'text' },
                                { k: 'home_initiatives_empty_button_text', l: 'Empty State Button', type: 'text' },
                                { k: 'home_initiatives_all_button_text', l: 'View All Button', type: 'text' },
                            ]},
                            { key: 'events', title: 'Events Section', fields: [
                                { k: 'home_events_subtitle', l: 'Subtitle', type: 'text' },
                                { k: 'home_events_title', l: 'Title', type: 'richtext' },
                                { k: 'home_events_empty_message', l: 'Empty State Message', type: 'text' },
                            ]},
                            { key: 'stories', title: 'Stories Section', fields: [
                                { k: 'home_stories_subtitle', l: 'Subtitle', type: 'text' },
                                { k: 'home_stories_title', l: 'Title', type: 'text' },
                                { k: 'home_stories_description', l: 'Description', type: 'richtext' },
                                { k: 'home_stories_empty_message', l: 'Empty State Message', type: 'text' },
                            ]},
                            { key: 'gallery', title: 'Gallery Section', fields: [
                                { k: 'home_gallery_subtitle', l: 'Subtitle', type: 'text' },
                                { k: 'home_gallery_title', l: 'Title', type: 'text' },
                                { k: 'home_gallery_description', l: 'Description', type: 'richtext' },
                            ]},
                            { key: 'volunteer', title: 'Volunteer CTA Section', fields: [
                                { k: 'home_volunteer_subtitle', l: 'Subtitle', type: 'text' },
                                { k: 'home_volunteer_title', l: 'Title', type: 'text' },
                                { k: 'home_volunteer_description', l: 'Description', type: 'richtext' },
                                { k: 'home_volunteer_bullet_1', l: 'Bullet Point 1', type: 'text' },
                                { k: 'home_volunteer_bullet_2', l: 'Bullet Point 2', type: 'text' },
                                { k: 'home_volunteer_bullet_3', l: 'Bullet Point 3', type: 'text' },
                                { k: 'home_volunteer_bullet_4', l: 'Bullet Point 4', type: 'text' },
                                { k: 'home_volunteer_bullet_5', l: 'Bullet Point 5', type: 'text' },
                                { k: 'home_volunteer_button_text', l: 'Button Text', type: 'text' },
                            ]},
                        ].map(section => (
                            <div key={section.key} className="content-card mb-4">
                                <div className="card-header"><h5 className="mb-0">{section.title}</h5></div>
                                <div className="card-body">
                                    <div className="row g-3">
                                        {section.fields.map(field => (
                                            <div key={field.k} className={field.type === 'richtext' ? 'col-12' : 'col-md-6'}>
                                                {field.type === 'richtext' ? (
                                                    <RichTextField label={field.l} value={data[field.k] || ''} onChange={(val) => setData(field.k, val)} />
                                                ) : (
                                                    <>
                                                        <label className="form-label">{field.l}</label>
                                                        <input type="text" className="form-control" value={data[field.k] || ''} onChange={(e) => setData(field.k, e.target.value)} />
                                                    </>
                                                )}
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                )}

                {activeSection === 'about' && (
                    <div>
                        <div className="content-card mb-4">
                            <div className="card-header"><h5 className="mb-0">Hero Section</h5></div>
                            <div className="card-body">
                                <div className="row g-3">
                                    <div className="col-md-6">
                                        <label className="form-label">Hero Title</label>
                                        <input type="text" className="form-control" value={data.about_hero_title || ''} onChange={(e) => setData('about_hero_title', e.target.value)} />
                                    </div>
                                    <div className="col-md-6">
                                        <label className="form-label">Hero Subtitle</label>
                                        <input type="text" className="form-control" value={data.about_hero_subtitle || ''} onChange={(e) => setData('about_hero_subtitle', e.target.value)} />
                                    </div>
                                    <div className="col-12">
                                        <ImageUploadField name="about_hero_image" label="Hero Background Image" value={data.about_hero_image || ''} onChange={(val) => setData('about_hero_image', val)} />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="content-card mb-4">
                            <div className="card-header"><h5 className="mb-0">Background Section</h5></div>
                            <div className="card-body">
                                <div className="row g-3">
                                    <div className="col-12">
                                        <label className="form-label">Section Heading</label>
                                        <input type="text" className="form-control" value={data.about_background_heading || ''} onChange={(e) => setData('about_background_heading', e.target.value)} />
                                    </div>
                                    <div className="col-12">
                                        <RichTextField label="Content" value={data.about_background_content || ''} onChange={(val) => setData('about_background_content', val)} />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="content-card mb-4">
                            <div className="card-header"><h5 className="mb-0">Methodology Section</h5></div>
                            <div className="card-body">
                                <div className="row g-3">
                                    <div className="col-12">
                                        <label className="form-label">Section Heading</label>
                                        <input type="text" className="form-control" value={data.about_methodology_heading || ''} onChange={(e) => setData('about_methodology_heading', e.target.value)} />
                                    </div>
                                    <div className="col-12">
                                        <RichTextField label="Intro Text" value={data.about_methodology_content || ''} onChange={(val) => setData('about_methodology_content', val)} />
                                    </div>
                                    <div className="col-12">
                                        <ImageUploadField name="about_methodology_image" label="Section Image" value={data.about_methodology_image || ''} onChange={(val) => setData('about_methodology_image', val)} />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p className="text-muted small">Note: The Vision, Mission, and Core Values sections use the same content as the homepage Foundation section. Edit those via the Homepage Sections tab.</p>
                    </div>
                )}
            </form>
        </AdminLayout>
    );
}
