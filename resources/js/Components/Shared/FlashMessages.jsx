import { useEffect, useState } from 'react';
import { usePage } from '@inertiajs/react';

export default function FlashMessages() {
    const { flash } = usePage().props;
    const [messages, setMessages] = useState([]);

    useEffect(() => {
        const next = [
            flash?.success && { type: 'success', text: flash.success },
            flash?.error && { type: 'error', text: flash.error },
        ].filter(Boolean);
        setMessages(next);
        if (!next.length) return undefined;
        const timer = window.setTimeout(() => setMessages([]), 5000);
        return () => window.clearTimeout(timer);
    }, [flash?.success, flash?.error]);

    return <div className="admin-toast-stack" aria-live="polite">
        {messages.map((message, index) => <div className={`admin-toast admin-toast-${message.type}`} key={`${message.type}-${index}`}>
            <span>{message.text}</span>
            <button type="button" onClick={() => setMessages(current => current.filter((_, itemIndex) => itemIndex !== index))} aria-label="Dismiss">×</button>
        </div>)}
    </div>;
}
