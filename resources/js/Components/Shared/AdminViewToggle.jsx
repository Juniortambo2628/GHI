export default function AdminViewToggle({ view, setView }) {
    return (
        <div className="admin-view-toggle" role="group" aria-label="View mode">
            <button type="button" className={view === 'list' ? 'active' : ''} onClick={() => setView('list')} aria-label="List view"><i className="bi bi-list"></i></button>
            <button type="button" className={view === 'grid' ? 'active' : ''} onClick={() => setView('grid')} aria-label="Grid view"><i className="bi bi-grid"></i></button>
        </div>
    );
}
