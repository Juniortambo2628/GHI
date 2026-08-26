import PublicLayout from '../Layouts/PublicLayout';
import { Head, useForm, router } from '@inertiajs/react';
import { useState } from 'react';
import PageHeader from '../Components/Shared/PageHeader';

GetInvolved.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function GetInvolved({ initiatives, hero }) {
    const { data, setData, post, processing, errors, wasSuccessful } = useForm({
        full_name: '',
        email: '',
        initiative_id: '',
        message: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post('/get-involved', { preserveState: true });
    };

    if (wasSuccessful) {
        return (
            <>
                <Head title="Get Involved - Global Harmony Initiative" />
                <PageHeader title="Get Involved" breadcrumb={[{ label: 'Get Involved' }]} />
                <div className="container py-5">
                    <div className="row justify-content-center">
                        <div className="col-md-8 col-lg-6 text-center">
                            <div className="mb-4">
                                <i className="bi bi-check-circle-fill text-success" style={{ fontSize: '4rem' }}></i>
                            </div>
                            <h3 className="mb-3">Thank You!</h3>
                            <p className="text-muted mb-4">We've received your interest and will get back to you soon. Together, we can make a difference.</p>
                            <button className="btn btn-primary" onClick={() => router.visit('/')}>Back to Home</button>
                        </div>
                    </div>
                </div>
            </>
        );
    }

    return (
        <>
            <Head>
                <title>Get Involved - Global Harmony Initiative</title>
                <meta name="description" content="Join Global Harmony Initiative and make a difference in East Africa through education, healthcare, and community development." />
            </Head>
            <PageHeader
                title={hero?.hero_get_involved_title || 'Get Involved'}
                subtitle={hero?.hero_get_involved_subtitle}
                image={hero?.hero_get_involved_image}
                breadcrumb={[{ label: 'Get Involved' }]}
            />
            <div className="container py-5">
                <div className="row justify-content-center">
                    <div className="col-md-8 col-lg-6">
                        <div className="content-card">
                            <div className="card-header">
                                <h5 className="mb-0"><i className="bi bi-heart me-2"></i>Join Our Mission</h5>
                            </div>
                            <div className="card-body">
                                <p className="text-muted mb-4">
                                    Whether you want to volunteer, mentor, partner, or simply learn more — we'd love to hear from you.
                                    Fill out the form below and we'll connect you with the right opportunity.
                                </p>

                                <form onSubmit={submit}>
                                    <div className="mb-3">
                                        <label className="form-label">Full Name <span className="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            className={`form-control ${errors.full_name ? 'is-invalid' : ''}`}
                                            value={data.full_name}
                                            onChange={e => setData('full_name', e.target.value)}
                                            placeholder="Your full name"
                                        />
                                        {errors.full_name && <div className="invalid-feedback">{errors.full_name}</div>}
                                    </div>

                                    <div className="mb-3">
                                        <label className="form-label">Email <span className="text-danger">*</span></label>
                                        <input
                                            type="email"
                                            className={`form-control ${errors.email ? 'is-invalid' : ''}`}
                                            value={data.email}
                                            onChange={e => setData('email', e.target.value)}
                                            placeholder="you@example.com"
                                        />
                                        {errors.email && <div className="invalid-feedback">{errors.email}</div>}
                                    </div>

                                    <div className="mb-3">
                                        <label className="form-label">Interested Initiative</label>
                                        <select
                                            className={`form-select ${errors.initiative_id ? 'is-invalid' : ''}`}
                                            value={data.initiative_id}
                                            onChange={e => setData('initiative_id', e.target.value)}
                                        >
                                            <option value="">-- Select an initiative (optional) --</option>
                                            {initiatives?.map(init => (
                                                <option key={init.id} value={init.id}>{init.title}</option>
                                            ))}
                                        </select>
                                        {errors.initiative_id && <div className="invalid-feedback">{errors.initiative_id}</div>}
                                    </div>

                                    <div className="mb-4">
                                        <label className="form-label">Message</label>
                                        <textarea
                                            className={`form-control ${errors.message ? 'is-invalid' : ''}`}
                                            rows="4"
                                            value={data.message}
                                            onChange={e => setData('message', e.target.value)}
                                            placeholder="Tell us how you'd like to get involved..."
                                        ></textarea>
                                        {errors.message && <div className="invalid-feedback">{errors.message}</div>}
                                    </div>

                                    <button type="submit" className="btn btn-primary w-100" disabled={processing}>
                                        {processing ? (
                                            <><span className="spinner-border spinner-border-sm me-2"></span>Sending...</>
                                        ) : (
                                            <><i className="bi bi-send me-2"></i>Submit Interest</>
                                        )}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
