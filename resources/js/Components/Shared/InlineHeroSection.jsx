import mediaUrl from './mediaUrl';

export default function InlineHeroSection({ title, subtitle, gradient = 'linear-gradient(135deg, #000656 0%, #1a3a8f 100%)', image, buttonText, buttonUrl, children }) {
    const bgStyle = image
        ? { backgroundImage: `url(${mediaUrl(image)})`, backgroundSize: 'cover', backgroundPosition: 'center' }
        : { background: gradient };

    return (
        <section className="hero-section" style={bgStyle}>
            <div className="page-header-overlay"></div>
            <div className="container text-white text-center position-relative py-5">
                <h1 className="display-4 fw-bold">{title}</h1>
                {subtitle && <p className="lead mb-4">{subtitle}</p>}
                {children}
            </div>
        </section>
    );
}
