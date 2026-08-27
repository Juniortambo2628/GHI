import { Head, useForm, usePage } from '@inertiajs/react';
import { useState } from 'react';
import PublicLayout from '../Layouts/PublicLayout';

Profile.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function Profile() {
    const { user } = usePage().props;

    const profileForm = useForm({
        name: user.name || '',
        email: user.email || '',
    });

    const passwordForm = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const deleteForm = useForm({
        password: '',
    });

    const [showPasswordSaved, setShowPasswordSaved] = useState(false);
    const [showProfileSaved, setShowProfileSaved] = useState(false);

    const submitProfile = (e) => {
        e.preventDefault();
        profileForm.patch('/profile', {
            onSuccess: () => {
                setShowProfileSaved(true);
                setTimeout(() => setShowProfileSaved(false), 2000);
            },
        });
    };

    const submitPassword = (e) => {
        e.preventDefault();
        passwordForm.put('/password', {
            onSuccess: () => {
                passwordForm.reset();
                setShowPasswordSaved(true);
                setTimeout(() => setShowPasswordSaved(false), 2000);
            },
        });
    };

    const submitDelete = (e) => {
        e.preventDefault();
        if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
            deleteForm.delete('/profile');
        }
    };

    return (
        <>
            <Head title="Profile" />

            <div className="container-fluid page-header hero-contact mb-5">
                <div className="container py-5">
                    <nav aria-label="breadcrumb animated slideInDown mb-4">
                        <ol className="breadcrumb">
                            <li className="breadcrumb-item"><a href="/">Home</a></li>
                            <li className="breadcrumb-item active">Profile</li>
                        </ol>
                    </nav>
                    <h1 className="display-3 text-white mb-3 animated slideInDown">My Profile</h1>
                </div>
            </div>

            <div className="container py-5">
                <div className="row justify-content-center">
                    <div className="col-lg-8">
                        {/* Profile Information */}
                        <div className="content-card mb-4">
                            <div className="card-header">
                                <h5 className="mb-0">Profile Information</h5>
                            </div>
                            <div className="card-body">
                                <p className="text-muted small mb-4">Update your account's profile information and email address.</p>
                                <form onSubmit={submitProfile}>
                                    <div className="mb-3">
                                        <label className="form-label">Name</label>
                                        <input
                                            type="text"
                                            className={`form-control ${profileForm.errors.name ? 'is-invalid' : ''}`}
                                            value={profileForm.data.name}
                                            onChange={(e) => profileForm.setData('name', e.target.value)}
                                            required
                                        />
                                        {profileForm.errors.name && <div className="invalid-feedback">{profileForm.errors.name}</div>}
                                    </div>
                                    <div className="mb-3">
                                        <label className="form-label">Email</label>
                                        <input
                                            type="email"
                                            className={`form-control ${profileForm.errors.email ? 'is-invalid' : ''}`}
                                            value={profileForm.data.email}
                                            onChange={(e) => profileForm.setData('email', e.target.value)}
                                            required
                                        />
                                        {profileForm.errors.email && <div className="invalid-feedback">{profileForm.errors.email}</div>}
                                    </div>
                                    <div className="d-flex align-items-center gap-3">
                                        <button type="submit" className="btn btn-primary" disabled={profileForm.processing}>
                                            {profileForm.processing ? 'Saving...' : 'Save'}
                                        </button>
                                        {showProfileSaved && <span className="text-muted small">Saved.</span>}
                                    </div>
                                </form>
                            </div>
                        </div>

                        {/* Update Password */}
                        <div className="content-card mb-4">
                            <div className="card-header">
                                <h5 className="mb-0">Update Password</h5>
                            </div>
                            <div className="card-body">
                                <p className="text-muted small mb-4">Ensure your account is using a long, random password to stay secure.</p>
                                <form onSubmit={submitPassword}>
                                    <div className="mb-3">
                                        <label className="form-label">Current Password</label>
                                        <input
                                            type="password"
                                            className={`form-control ${passwordForm.errors.current_password ? 'is-invalid' : ''}`}
                                            value={passwordForm.data.current_password}
                                            onChange={(e) => passwordForm.setData('current_password', e.target.value)}
                                            autoComplete="current-password"
                                        />
                                        {passwordForm.errors.current_password && <div className="invalid-feedback">{passwordForm.errors.current_password}</div>}
                                    </div>
                                    <div className="mb-3">
                                        <label className="form-label">New Password</label>
                                        <input
                                            type="password"
                                            className={`form-control ${passwordForm.errors.password ? 'is-invalid' : ''}`}
                                            value={passwordForm.data.password}
                                            onChange={(e) => passwordForm.setData('password', e.target.value)}
                                            autoComplete="new-password"
                                        />
                                        {passwordForm.errors.password && <div className="invalid-feedback">{passwordForm.errors.password}</div>}
                                    </div>
                                    <div className="mb-3">
                                        <label className="form-label">Confirm Password</label>
                                        <input
                                            type="password"
                                            className={`form-control ${passwordForm.errors.password_confirmation ? 'is-invalid' : ''}`}
                                            value={passwordForm.data.password_confirmation}
                                            onChange={(e) => passwordForm.setData('password_confirmation', e.target.value)}
                                            autoComplete="new-password"
                                        />
                                    </div>
                                    <div className="d-flex align-items-center gap-3">
                                        <button type="submit" className="btn btn-primary" disabled={passwordForm.processing}>
                                            {passwordForm.processing ? 'Saving...' : 'Save'}
                                        </button>
                                        {showPasswordSaved && <span className="text-muted small">Saved.</span>}
                                    </div>
                                </form>
                            </div>
                        </div>

                        {/* Delete Account */}
                        <div className="content-card mb-4 border-danger">
                            <div className="card-header bg-danger text-white">
                                <h5 className="mb-0">Delete Account</h5>
                            </div>
                            <div className="card-body">
                                <p className="text-muted mb-4">Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm.</p>
                                <form onSubmit={submitDelete}>
                                    <div className="mb-3">
                                        <label className="form-label">Password</label>
                                        <input
                                            type="password"
                                            className={`form-control ${deleteForm.errors.password ? 'is-invalid' : ''}`}
                                            value={deleteForm.data.password}
                                            onChange={(e) => deleteForm.setData('password', e.target.value)}
                                            autoComplete="current-password"
                                        />
                                        {deleteForm.errors.password && <div className="invalid-feedback">{deleteForm.errors.password}</div>}
                                    </div>
                                    <button type="submit" className="btn btn-danger" disabled={deleteForm.processing}>
                                        {deleteForm.processing ? 'Deleting...' : 'Delete Account'}
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
