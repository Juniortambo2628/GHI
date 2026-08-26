export default function AdminFloatingToolbar({ left, right, unsavedChanges, saveStatus, children }) {
    return (
        <div className="admin-floating-toolbar">
            <div className="toolbar-left">
                {left}
                {children}
            </div>
            <div className="toolbar-right">
                {unsavedChanges && (
                    <span className="unsaved-indicator">
                        <i className="bi bi-circle-fill" style={{ fontSize: '0.4rem' }}></i>
                        Unsaved changes
                    </span>
                )}
                {saveStatus && (
                    <span className={`save-status ${saveStatus.state}`}>
                        {saveStatus.state === 'saving' && <><span className="spinner-grow spinner-grow-sm me-1" role="status"></span>Saving...</>}
                        {saveStatus.state === 'saved' && <><i className="bi bi-check-circle me-1"></i>{saveStatus.message || 'Saved'}</>}
                        {saveStatus.state === 'error' && <><i className="bi bi-exclamation-circle me-1"></i>Error saving</>}
                    </span>
                )}
                {right}
            </div>
        </div>
    );
}
