export default function InlineHeroSection({ title, subtitle, gradient = 'linear-gradient(135deg, #1a3a5c 0%, #0d2137 100%)' }) {
    return (
        <section className="hero-section" style={{background: gradient}}>
            <div className="container text-white text-center">
                <h1 className="display-4 fw-bold">{title}</h1>
                {subtitle && <p className="lead">{subtitle}</p>}
            </div>
        </section>
    );
}
