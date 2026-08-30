import PublicLayout from '../Layouts/PublicLayout';
import sanitizeHtml from '../Components/Shared/sanitizeHtml';
import mediaUrl from '../Components/Shared/mediaUrl';
import FallbackImage from '../Components/Shared/FallbackImage';
import StatusBadge from '../Components/Shared/StatusBadge';
import ShowPageLayout from '../Components/Shared/ShowPageLayout';
import RelatedChain from '../Components/Shared/RelatedChain';
import { Head, Link } from '@inertiajs/react';

InitiativeShow.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function InitiativeShow({ initiative, events }) {
    const causes = initiative.causes || [];
    return (
        <>
            <Head title={`${initiative.title} - Global Harmony Initiative`} />

            <ShowPageLayout title={initiative.title} section="initiatives" sectionLabel="Initiatives" sectionUrl="/initiatives" image={initiative.image}
                sidebar={
                    <div className="bg-light p-4 rounded mb-4">
                        <h5 className="mb-3">Details</h5>
                        <ul className="list-unstyled">
                            <li className="mb-2"><strong>Category:</strong> {initiative.category}</li>
                            <li className="mb-2"><strong>Status:</strong> <StatusBadge status={initiative.status} /></li>
                            {causes.length > 0 && (
                                <li className="mb-2"><strong>Causes:</strong> {causes.map(c => c.title).join(', ')}</li>
                            )}
                            <li className="mb-2"><strong>Events:</strong> {events.length}</li>
                        </ul>
                    </div>
                }>
                <div className="mb-4" dangerouslySetInnerHTML={{ __html: sanitizeHtml(initiative.description || '') }} />
                {initiative.content && (
                    <div className="mb-4" dangerouslySetInnerHTML={{ __html: sanitizeHtml(initiative.content) }} />
                )}
            </ShowPageLayout>

            <RelatedChain currentType="initiative" cause={causes} events={events} />
        </>
    );
}
