import PublicLayout from '../Layouts/PublicLayout';
import { Head } from '@inertiajs/react';
import PageHeader from '../Components/Shared/PageHeader';

ComingSoonGetInvolved.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function ComingSoonGetInvolved() {
    return (
        <>
            <Head title="Get Involved - Coming Soon" />
            <PageHeader title="Get Involved" breadcrumb={[{ label: 'Get Involved' }]} />
            <div className="container py-5">
                <div className="text-center mb-5">
                    <h3>We're creating an exciting platform where you can join our community and make a real difference.</h3>
                    <p className="text-muted">Coming soon — stay tuned!</p>
                </div>
                <div className="row g-4 justify-content-center">
                    {[
                        { icon: 'hand-thumbs-up', title: 'Volunteer', desc: 'Join our team of dedicated volunteers' },
                        { icon: 'person-badge', title: 'Become a Mentor', desc: 'Share your skills and experience' },
                        { icon: 'calendar-event', title: 'Organize Events', desc: 'Help plan community events' },
                        { icon: 'piggy-bank', title: 'Fundraise', desc: 'Start your own fundraising campaign' },
                        { icon: 'building', title: 'Corporate Partnership', desc: 'Partner with us for bigger impact' },
                        { icon: 'briefcase', title: 'Join Our Team', desc: 'Explore career opportunities' },
                    ].map((item, idx) => (
                        <div key={idx} className="col-md-6 col-lg-4">
                            <div className="card h-100 text-center border-0 shadow-sm">
                                <div className="card-body p-4">
                                    <i className={`bi bi-${item.icon} fs-1 text-primary mb-3`}></i>
                                    <h5>{item.title}</h5>
                                    <p className="text-muted">{item.desc}</p>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </>
    );
}
