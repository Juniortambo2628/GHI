import { Link } from '@inertiajs/react';

export default function SectionCTA({ href, children, size = 'md', className = '' }) {
    const sizeClasses = {
        sm: 'py-2 px-4',
        md: 'py-3 px-5',
        lg: 'py-3 px-5',
    };
    return (
        <div className="col-12">
            <div className={`d-flex align-items-center justify-content-center ${className}`}>
                <Link className={`btn-hover-bg btn btn-primary text-white ${sizeClasses[size] || sizeClasses.md}`} href={href}>{children}</Link>
            </div>
        </div>
    );
}
