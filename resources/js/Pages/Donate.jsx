import PublicLayout from '../Layouts/PublicLayout';
import { Head } from '@inertiajs/react';
import InlineHeroSection from '../Components/Shared/InlineHeroSection';

Donate.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function Donate() {
    return (
        <>
            <Head title="Donate" />
            <InlineHeroSection title="Support Our Mission" subtitle="Your donation helps us create lasting change in communities across East Africa." />
            <div className="container py-5 text-center">
                <div className="row justify-content-center">
                    <div className="col-lg-6">
                        <div className="card shadow">
                            <div className="card-body p-5">
                                <h3 className="mb-3">Coming Soon</h3>
                                <p className="text-muted">Online donations will be available soon. In the meantime, you can contact us directly to make a donation.</p>
                                <a href="/contact" className="btn btn-primary">Contact Us</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
