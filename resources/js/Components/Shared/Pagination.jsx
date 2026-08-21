import { Link } from '@inertiajs/react';

export default function Pagination({ data }) {
    if (!data || data.last_page <= 1) return null;

    const pages = [];
    const current = data.current_page;
    const last = data.last_page;

    pages.push(1);
    if (current > 3) pages.push('...');
    for (let i = Math.max(2, current - 1); i <= Math.min(last - 1, current + 1); i++) {
        pages.push(i);
    }
    if (current < last - 2) pages.push('...');
    if (last > 1) pages.push(last);

    return (
        <div className="mt-4 text-center">
            <nav>
                <ul className="pagination ghi-pagination">
                    {data.prev_page_url && (
                        <li className="page-item">
                            <Link className="page-link" href={data.prev_page_url}>
                                <i className="bi bi-chevron-left"></i>
                            </Link>
                        </li>
                    )}
                    {pages.map((page, idx) => (
                        page === '...' ? (
                            <li key={`ellipsis-${idx}`} className="page-item disabled">
                                <span className="page-link">...</span>
                            </li>
                        ) : (
                            <li key={page} className={`page-item ${page === current ? 'active' : ''}`}>
                                <Link className="page-link" href={`${data.path}?page=${page}`}>{page}</Link>
                            </li>
                        )
                    ))}
                    {data.next_page_url && (
                        <li className="page-item">
                            <Link className="page-link" href={data.next_page_url}>
                                <i className="bi bi-chevron-right"></i>
                            </Link>
                        </li>
                    )}
                </ul>
            </nav>
        </div>
    );
}
