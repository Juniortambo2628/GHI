import PublicLayout from '../Layouts/PublicLayout';
import { Head, useForm } from '@inertiajs/react';
import PageHeader from '../Components/Shared/PageHeader';

Contact.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function Contact({ hero }) {
    const { data, setData, post, processing, errors, wasSuccessful } = useForm({
        firstname: '', lastname: '', email: '', subject: '', message: ''
    });

    const submit = (e) => {
        e.preventDefault();
        post('/contact');
    };

    return (
        <>
            <Head title="Contact" />
            <PageHeader title={hero?.hero_contact_title || 'Contact Us'} subtitle={hero?.hero_contact_subtitle} image={hero?.hero_contact_image} buttonText={hero?.hero_contact_button_text} buttonUrl={hero?.hero_contact_button_url} breadcrumb={[{ label: 'Contact Us' }]} />
            <div className="container py-5">
                <div className="row g-5">
                    <div className="col-lg-5">
                        <h3>Get in Touch</h3>
                        <p className="text-muted">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
                        <div className="mt-4">
                            <p><i className="bi bi-envelope text-primary me-3"></i>info@globalharmonyinitiative.com</p>
                            <p><i className="bi bi-telephone text-primary me-3"></i>+1 (437) 297-7977</p>
                        </div>
                    </div>
                    <div className="col-lg-7">
                        {wasSuccessful && <div className="alert alert-success">Thank you for your message. We will get back to you soon.</div>}
                        <form onSubmit={submit}>
                            <div className="row g-3">
                                <div className="col-md-6">
                                    <label className="form-label">First Name *</label>
                                    <input type="text" className={`form-control ${errors.firstname ? 'is-invalid' : ''}`} value={data.firstname} onChange={e => setData('firstname', e.target.value)} required />
                                    {errors.firstname && <div className="invalid-feedback">{errors.firstname}</div>}
                                </div>
                                <div className="col-md-6">
                                    <label className="form-label">Last Name *</label>
                                    <input type="text" className={`form-control ${errors.lastname ? 'is-invalid' : ''}`} value={data.lastname} onChange={e => setData('lastname', e.target.value)} required />
                                    {errors.lastname && <div className="invalid-feedback">{errors.lastname}</div>}
                                </div>
                                <div className="col-md-6">
                                    <label className="form-label">Email *</label>
                                    <input type="email" className={`form-control ${errors.email ? 'is-invalid' : ''}`} value={data.email} onChange={e => setData('email', e.target.value)} required />
                                    {errors.email && <div className="invalid-feedback">{errors.email}</div>}
                                </div>
                                <div className="col-md-6">
                                    <label className="form-label">Subject *</label>
                                    <input type="text" className={`form-control ${errors.subject ? 'is-invalid' : ''}`} value={data.subject} onChange={e => setData('subject', e.target.value)} required />
                                    {errors.subject && <div className="invalid-feedback">{errors.subject}</div>}
                                </div>
                                <div className="col-12">
                                    <label className="form-label">Message *</label>
                                    <textarea className={`form-control ${errors.message ? 'is-invalid' : ''}`} rows="5" value={data.message} onChange={e => setData('message', e.target.value)} required></textarea>
                                    {errors.message && <div className="invalid-feedback">{errors.message}</div>}
                                </div>
                                <div className="col-12">
                                    <button type="submit" className="btn btn-primary px-5" disabled={processing}>{processing ? 'Sending...' : 'Send Message'}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </>
    );
}
