export default function ResultsCount({ data }) {
    return (
        <div className="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p className="mb-0">Showing {data?.from || 0}-{data?.to || 0} of {data?.total || 0} results</p>
            </div>
        </div>
    );
}
