export default function SearchSidebar({ title = 'Search', children, onSubmit, viewMode, setViewMode }) {
    return (
        <div className="col-lg-3">
            <div className="search-sidebar">
                <div className="d-flex align-items-center justify-content-between mb-3">
                    <h5 className="search-sidebar-title mb-0">{title}</h5>
                    {setViewMode && (
                        <div className="view-toggle">
                            <button type="button" className={`view-toggle-btn ${viewMode === 'grid' ? 'active' : ''}`} onClick={() => setViewMode('grid')} title="Grid View">
                                <i className="bi bi-grid-3x3-gap"></i>
                            </button>
                            <button type="button" className={`view-toggle-btn ${viewMode === 'list' ? 'active' : ''}`} onClick={() => setViewMode('list')} title="List View">
                                <i className="bi bi-list-ul"></i>
                            </button>
                            <button type="button" className={`view-toggle-btn ${viewMode === 'timeline' ? 'active' : ''}`} onClick={() => setViewMode('timeline')} title="Timeline View">
                                <i className="bi bi-diagram-3"></i>
                            </button>
                        </div>
                    )}
                </div>
                <form onSubmit={onSubmit}>
                    {children}
                </form>
            </div>
        </div>
    );
}
