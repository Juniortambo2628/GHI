import { useEffect, useRef } from 'react';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const toolbar = [
    [{ header: [1, 2, 3, false] }],
    ['bold', 'italic', 'underline', 'link'],
    [{ list: 'ordered' }, { list: 'bullet' }],
    ['blockquote', 'clean'],
];

export default function RichTextField({ label = 'Content', value, onChange }) {
    const container = useRef(null);
    const editor = useRef(null);

    useEffect(() => {
        if (!container.current) return undefined;
        editor.current = new Quill(container.current, { theme: 'snow', modules: { toolbar } });
        editor.current.root.innerHTML = value || '';
        editor.current.on('text-change', () => onChange(editor.current.root.innerHTML));
        return () => { editor.current?.off('text-change'); editor.current = null; };
    }, []);

    return <div className="admin-rich-text-field"><label>{label}</label><div ref={container} className="admin-rich-text-editor" /></div>;
}
