import PublicLayout from '../Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';

DonateSuccess.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function DonateSuccess() {
    return (
        <>
            <Head>
                <title>Donation Successful - Global Harmony Initiative</title>
            </Head>

            <div className="container py-5">
                <div className="row justify-content-center">
                    <div className="col-lg-6 text-center">
                        <div className="mb-4">
                            <i className="bi bi-check-circle-fill text-success success-icon"></i>
                        </div>
                        <h1 className="display-5 mb-4">Thank You!</h1>
                        <p className="lead mb-4">Your donation has been received. We truly appreciate your generosity and support for our mission.</p>
                        <p className="text-muted mb-4">A confirmation will be sent to your email address shortly.</p>
                        <div className="d-flex gap-3 justify-content-center">
                            <Link href="/" className="btn btn-primary">Return Home</Link>
                            <Link href="/contact" className="btn btn-outline-primary">Contact Us</Link>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
