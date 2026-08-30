import { Head, Link } from '@inertiajs/react';
import PublicLayout from '../Layouts/PublicLayout';
import SectionHeader from '../Components/Shared/SectionHeader';
import stripHtml from '../Components/Shared/stripHtml';

Search.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function Search({ term, results }) {
    return (
        <>
            <Head title={term ? `Search: ${term}` : 'Search'} />

            {/* Hero */}
            <section className="container-fluid hero-section" style={{ background: 'linear-gradient(135deg, #000656 0%, #1a3a8f 100%)' }}>
                <div className="container py-5">
                    <div className="row py-5">
                        <div className="col-lg-8 mx-auto text-center">
                            <SectionHeader subtitle="Search" title={term ? `Results for "${term}"` : 'Search Our Site'} light className="mb-4" />
                            <form className="search-page-form d-flex gap-2 justify-content-center" action="/search" method="get">
                                <input
                                    name="q"
                                    defaultValue={term}
                                    placeholder="Search the site..."
                                    className="form-control"
                                    style={{ maxWidth: '28rem', height: '3rem', borderRadius: '999px', padding: '0.5rem 1.5rem' }}
                                />
                                <button className="btn btn-primary text-white py-2 px-4" type="submit" style={{ borderRadius: '999px' }}>
                                    <i className="bi bi-search me-1"></i>Search
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            {/* Results */}
            <section className="container py-5">
                {term && (
                    <p className="mb-4">
                        {results.length} result{results.length !== 1 ? 's' : ''} for <strong>{term}</strong>
                    </p>
                )}

                {results.length > 0 ? (
                    <div className="row g-4">
                        {results.map((result, idx) => (
                            <div key={`${result.type}-${idx}`} className="col-md-6 col-lg-4">
                                <article className="card h-100 border-0 shadow-sm">
                                    <div className="card-body d-flex flex-column">
                                        <small className="text-uppercase text-primary fw-bold" style={{ fontSize: '0.7rem', letterSpacing: '0.08em' }}>{result.type}</small>
                                        <h5 className="card-title mt-2">
                                            <Link href={result.url} className="text-decoration-none text-dark">{result.title}</Link>
                                        </h5>
                                        <p className="card-text text-muted small">{stripHtml(result.description)?.substring(0, 120)}{stripHtml(result.description)?.length > 120 ? '...' : ''}</p>
                                        <div className="mt-auto pt-3">
                                            <Link href={result.url} className="btn btn-primary btn-sm">View {result.type}</Link>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        ))}
                    </div>
                ) : term ? (
                    <div className="text-center py-5">
                        <i className="bi bi-search fs-1 text-muted mb-3 d-block"></i>
                        <h4>No results found</h4>
                        <p className="text-muted">Try different keywords or browse our sections.</p>
                        <Link href="/" className="btn btn-primary text-white mt-2">Back to Home</Link>
                    </div>
                ) : (
                    <div className="text-center py-5">
                        <i className="bi bi-search fs-1 text-muted mb-3 d-block"></i>
                        <h4>Start searching</h4>
                        <p className="text-muted">Enter a keyword above to find causes, initiatives, events, stories, and more.</p>
                    </div>
                )}
            </section>
        </>
    );
}
