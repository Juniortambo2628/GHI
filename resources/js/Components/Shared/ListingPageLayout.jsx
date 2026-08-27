import SearchSidebar from './SearchSidebar';
import ResultsCount from './ResultsCount';
import ListingCardGrid from './ListingCardGrid';
import Pagination from './Pagination';

export default function ListingPageLayout({ data, emptyMessage, viewMode, setViewMode, onSubmit, filters, renderCard }) {
    return (
        <div className="container-fluid px-5">
            <div className="row g-4">
                <SearchSidebar title="Search & Filter" onSubmit={onSubmit} viewMode={viewMode} setViewMode={setViewMode}>
                    {filters}
                </SearchSidebar>
                <div className="col-lg-9">
                    <ResultsCount data={data} />
                    <ListingCardGrid data={data} emptyMessage={emptyMessage} viewMode={viewMode}>
                        {renderCard}
                    </ListingCardGrid>
                    <Pagination data={data} />
                </div>
            </div>
        </div>
    );
}
