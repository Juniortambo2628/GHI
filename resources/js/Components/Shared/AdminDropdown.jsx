import { useState, useRef, useEffect, createContext, useContext } from 'react';

const DropdownContext = createContext();

export default function AdminDropdown({ trigger, children, align = 'right', className = '', openUp = false }) {
    const [open, setOpen] = useState(false);
    const ref = useRef(null);

    useEffect(() => {
        if (!open) return undefined;
        const handler = (e) => {
            if (ref.current && !ref.current.contains(e.target)) setOpen(false);
        };
        document.addEventListener('mousedown', handler);
        return () => document.removeEventListener('mousedown', handler);
    }, [open]);

    useEffect(() => {
        if (!open) return undefined;
        const handler = (e) => {
            if (e.key === 'Escape') setOpen(false);
        };
        document.addEventListener('keydown', handler);
        return () => document.removeEventListener('keydown', handler);
    }, [open]);

    return (
        <DropdownContext.Provider value={{ close: () => setOpen(false) }}>
            <div className={`admin-dropdown-wrapper ${className}`} ref={ref}>
                <span onClick={() => setOpen(prev => !prev)} style={{ cursor: 'pointer' }}>
                    {trigger}
                </span>
                {open && (
                    <div className={`admin-dropdown-menu${openUp ? ' open-up' : ''}`} style={align === 'left' ? { right: 'auto', left: 0 } : undefined}>
                        {children}
                    </div>
                )}
            </div>
        </DropdownContext.Provider>
    );
}

export function AdminDropdownItem({ href, icon, onClick, children, danger }) {
    const { close } = useContext(DropdownContext);
    const className = `admin-dropdown-item${danger ? ' text-danger' : ''}`;

    const handleClick = (e) => {
        e.preventDefault();
        if (onClick) onClick(e);
        close();
    };

    if (href) {
        return <a href={href} className={className} onClick={handleClick}><i className={`bi ${icon}`}></i>{children}</a>;
    }
    return <button type="button" className={className} onClick={handleClick}><i className={`bi ${icon}`}></i>{children}</button>;
}

export function AdminDropdownDivider() {
    return <div className="admin-dropdown-divider"></div>;
}
