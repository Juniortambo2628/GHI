import AdminLayout from '../../../Layouts/AdminLayout';
import { Head, router } from '@inertiajs/react';
import { useState, useCallback } from 'react';

function PasskeyIcon() {
    return <i className="bi bi-key-fill" style={{ fontSize: '1.1rem' }}></i>;
}

function DeviceIcon({ name }) {
    const lower = (name || '').toLowerCase();
    if (lower.includes('iphone') || lower.includes('ipad')) return <i className="bi bi-phone"></i>;
    if (lower.includes('android')) return <i className="bi bi-phone"></i>;
    if (lower.includes('mac')) return <i className="bi bi-laptop"></i>;
    if (lower.includes('windows')) return <i className="bi bi-pc-display"></i>;
    if (lower.includes('yubi') || lower.includes('key')) return <i className="bi bi-usb-symbol"></i>;
    return <i className="bi bi-shield-lock"></i>;
}

function PasskeyCard({ passkey, onDelete }) {
    const [confirming, setConfirming] = useState(false);

    return (
        <div className="passkey-card">
            <div className="passkey-card-icon">
                <DeviceIcon name={passkey.name} />
            </div>
            <div className="passkey-card-body">
                <div className="passkey-card-name">{passkey.name}</div>
                <div className="passkey-card-meta">
                    <span>Added {passkey.created_at}</span>
                    {passkey.last_used_at && <span> · Last used {passkey.last_used_at}</span>}
                </div>
            </div>
            <div className="passkey-card-actions">
                {confirming ? (
                    <div className="d-flex gap-1">
                        <button className="btn btn-sm btn-danger" onClick={() => { onDelete(passkey.id); setConfirming(false); }}>Remove</button>
                        <button className="btn btn-sm btn-outline-secondary" onClick={() => setConfirming(false)}>Cancel</button>
                    </div>
                ) : (
                    <button className="btn btn-sm btn-outline-danger" onClick={() => setConfirming(true)}>
                        <i className="bi bi-trash"></i>
                    </button>
                )}
            </div>
        </div>
    );
}

function PasswordForm({ onSuccess }) {
    const [form, setForm] = useState({ current_password: '', password: '', password_confirmation: '' });
    const [errors, setErrors] = useState({});
    const [saving, setSaving] = useState(false);
    const [success, setSuccess] = useState('');

    const submit = (e) => {
        e.preventDefault();
        setSaving(true);
        setErrors({});
        setSuccess('');
        router.put('/admin/security/password', form, {
            preserveState: true,
            onSuccess: () => {
                setSuccess('Password updated successfully.');
                setForm({ current_password: '', password: '', password_confirmation: '' });
                setSaving(false);
            },
            onError: (err) => { setErrors(err); setSaving(false); },
        });
    };

    return (
        <form onSubmit={submit}>
            {success && <div className="alert alert-success py-2 small">{success}</div>}
            <div className="admin-form-grid">
                <label>
                    <span>Current Password</span>
                    <input type="password" value={form.current_password} onChange={e => setForm({ ...form, current_password: e.target.value })} />
                    {errors.current_password && <span className="text-danger small">{errors.current_password}</span>}
                </label>
                <label>
                    <span>New Password</span>
                    <input type="password" value={form.password} onChange={e => setForm({ ...form, password: e.target.value })} />
                    {errors.password && <span className="text-danger small">{errors.password}</span>}
                </label>
                <label>
                    <span>Confirm New Password</span>
                    <input type="password" value={form.password_confirmation} onChange={e => setForm({ ...form, password_confirmation: e.target.value })} />
                </label>
            </div>
            <div className="mt-3">
                <button type="submit" className="btn btn-sm btn-primary" disabled={saving}>
                    {saving ? <><span className="spinner-grow spinner-grow-sm me-1"></span>Updating...</> : <><i className="bi bi-check-lg me-1"></i>Update Password</>}
                </button>
            </div>
        </form>
    );
}

export default function Security({ passkeys, user, rp }) {
    const [showAddPasskey, setShowAddPasskey] = useState(false);
    const [passkeyName, setPasskeyName] = useState('');
    const [registering, setRegistering] = useState(false);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    const registerPasskey = useCallback(async () => {
        if (!passkeyName.trim()) return;
        setRegistering(true);
        setError('');
        setSuccess('');

        try {
            const optionsRes = await fetch('/admin/security/passkeys/options', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-XSRF-TOKEN': decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] || '') },
            });
            if (!optionsRes.ok) throw new Error('Failed to get passkey options');
            const options = await optionsRes.json();

            const credential = await navigator.credentials.create({
                publicKey: {
                    challenge: Uint8Array.from(atob(options.challenge), c => c.charCodeAt(0)),
                    rp: options.rp,
                    user: {
                        id: Uint8Array.from(atob(options.user.id), c => c.charCodeAt(0)),
                        name: options.user.name,
                        displayName: options.user.displayName,
                    },
                    pubKeyCredParams: options.pubKeyCredParams,
                    authenticatorSelection: options.authenticatorSelection,
                    timeout: options.timeout,
                    attestation: options.attestation,
                    excludeCredentials: options.excludeCredentials?.map(c => ({
                        ...c,
                        id: Uint8Array.from(atob(c.id), ch => ch.charCodeAt(0)),
                    })) || [],
                },
            });

            const csrfToken = decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] || '');
            const registerRes = await fetch('/admin/security/passkeys', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-XSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    name: passkeyName,
                    credential: {
                        id: credential.id,
                        rawId: btoa(String.fromCharCode(...new Uint8Array(credential.rawId))),
                        type: credential.type,
                        response: {
                            clientDataJSON: btoa(String.fromCharCode(...new Uint8Array(credential.response.clientDataJSON))),
                            attestationObject: btoa(String.fromCharCode(...new Uint8Array(credential.response.attestationObject))),
                        },
                    },
                }),
            });

            if (!registerRes.ok) {
                const err = await registerRes.json();
                throw new Error(err.message || 'Registration failed');
            }

            setSuccess('Passkey registered successfully!');
            setShowAddPasskey(false);
            setPasskeyName('');
            router.reload({ only: ['passkeys'] });
        } catch (err) {
            setError(err.message || 'Passkey registration failed. Make sure your device supports WebAuthn.');
        } finally {
            setRegistering(false);
        }
    }, [passkeyName]);

    const deletePasskey = (id) => {
        router.delete(`/admin/security/passkeys/${id}`, {
            preserveState: true,
            onSuccess: () => { setSuccess('Passkey removed.'); },
        });
    };

    const webAuthnSupported = typeof window !== 'undefined' && window.PublicKeyCredential !== undefined;

    return (
        <>
            <Head title="Security - Admin" />

            {success && <div className="alert alert-success py-2 small mb-3">{success}</div>}
            {error && <div className="alert alert-danger py-2 small mb-3">{error}</div>}

            <div className="content-card mb-4">
                <div className="card-header d-flex justify-content-between align-items-center">
                    <h5 className="mb-0"><i className="bi bi-key me-2"></i>Passkeys</h5>
                    <button className="btn btn-sm btn-primary" onClick={() => { setShowAddPasskey(!showAddPasskey); setError(''); }} disabled={!webAuthnSupported}>
                        <i className="bi bi-plus-circle me-1"></i>Add Passkey
                    </button>
                </div>
                <div className="card-body">
                    {!webAuthnSupported && (
                        <div className="alert alert-warning py-2 small mb-3">
                            <i className="bi bi-exclamation-triangle me-1"></i>
                            WebAuthn is not supported in this browser. Passkeys require a modern browser with HTTPS or localhost.
                        </div>
                    )}
                    <p className="text-muted small mb-3">
                        Passkeys let you sign in with biometrics, a security key, or your device PIN instead of a password.
                        Register multiple passkeys for backup across devices.
                    </p>

                    {showAddPasskey && (
                        <div className="passkey-register-form p-3 mb-3 border rounded">
                            <label className="form-label fw-semibold">Passkey Name</label>
                            <input
                                type="text"
                                className="form-control mb-2"
                                placeholder="e.g. MacBook Pro, iPhone 15, YubiKey"
                                value={passkeyName}
                                onChange={e => setPasskeyName(e.target.value)}
                                autoFocus
                            />
                            <div className="d-flex gap-2">
                                <button className="btn btn-sm btn-primary" onClick={registerPasskey} disabled={registering || !passkeyName.trim()}>
                                    {registering ? <><span className="spinner-grow spinner-grow-sm me-1"></span>Registering...</> : <><i className="bi bi-shield-check me-1"></i>Register</>}
                                </button>
                                <button className="btn btn-sm btn-outline-secondary" onClick={() => { setShowAddPasskey(false); setPasskeyName(''); }}>Cancel</button>
                            </div>
                        </div>
                    )}

                    {passkeys.length > 0 ? (
                        <div className="passkey-list">
                            {passkeys.map(pk => (
                                <PasskeyCard key={pk.id} passkey={pk} onDelete={deletePasskey} />
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-4 text-muted">
                            <PasskeyIcon />
                            <p className="mt-2 mb-0">No passkeys registered yet.</p>
                            <p className="small">Add a passkey for passwordless sign-in.</p>
                        </div>
                    )}
                </div>
            </div>

            <div className="content-card mb-4">
                <div className="card-header">
                    <h5 className="mb-0"><i className="bi bi-lock me-2"></i>Password</h5>
                </div>
                <div className="card-body">
                    <p className="text-muted small mb-3">Change your account password. Choose a strong, unique password.</p>
                    <PasswordForm />
                </div>
            </div>

            <div className="content-card mb-4">
                <div className="card-header">
                    <h5 className="mb-0"><i className="bi bi-shield-check me-2"></i>Account Security</h5>
                </div>
                <div className="card-body">
                    <div className="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div>
                            <div className="fw-semibold">Email Verified</div>
                            <div className="text-muted small">{user.email_verified ? 'Your email is verified.' : 'Your email is not verified.'}</div>
                        </div>
                        <span className={`badge ${user.email_verified ? 'bg-success' : 'bg-warning'}`}>
                            {user.email_verified ? 'Verified' : 'Unverified'}
                        </span>
                    </div>
                    <div className="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div>
                            <div className="fw-semibold">Two-Factor Authentication</div>
                            <div className="text-muted small">Add an extra layer of security with 2FA.</div>
                        </div>
                        <span className="badge bg-secondary">Coming Soon</span>
                    </div>
                    <div className="d-flex align-items-center justify-content-between py-2">
                        <div>
                            <div className="fw-semibold">Active Passkeys</div>
                            <div className="text-muted small">{passkeys.length} passkey(s) registered.</div>
                        </div>
                        <span className="badge" style={{ background: 'var(--admin-primary)', color: '#fff' }}>{passkeys.length}</span>
                    </div>
                </div>
            </div>
        </>
    );
}

Security.layout = page => <AdminLayout title="Security" description="Manage passkeys, password, and security settings.">{page}</AdminLayout>;
