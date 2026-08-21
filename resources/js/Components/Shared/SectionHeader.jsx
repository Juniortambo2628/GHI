export default function SectionHeader({ subtitle, title, description, light = false, className = '' }) {
    const textColor = light ? 'text-white' : '';
    return (
        <div className={`text-center mx-auto section-header-container ${className}`}>
            {subtitle && <h5 className={`text-uppercase ${light ? 'text-white-50' : 'text-secondary'}`}>{subtitle}</h5>}
            <h1 className={`mb-0 ${textColor}`}>{title}</h1>
            {description && <p className={`mb-0 mt-3 ${textColor}`}>{description}</p>}
        </div>
    );
}
