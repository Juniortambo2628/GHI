import { useState } from 'react';

export default function AdminConfirm({ message, onConfirm, children }) {
    const [open, setOpen] = useState(false);
    return <>{<span onClick={() => setOpen(true)}>{children}</span>}{open && <div className="admin-modal-backdrop" role="dialog" aria-modal="true"><div className="admin-modal"><h2>Confirm action</h2><p>{message}</p><div className="admin-modal-actions"><button className="btn btn-outline-secondary" onClick={() => setOpen(false)}>Cancel</button><button className="btn btn-danger" onClick={() => { setOpen(false); onConfirm(); }}>Continue</button></div></div></div>}</>;
}