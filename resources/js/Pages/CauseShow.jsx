import PublicLayout from '../Layouts/PublicLayout';
import mediaUrl from '../Components/Shared/mediaUrl';
import FallbackImage from '../Components/Shared/FallbackImage';
import StatusBadge from '../Components/Shared/StatusBadge';
import ShowPageLayout from '../Components/Shared/ShowPageLayout';
import RelatedChain from '../Components/Shared/RelatedChain';
import { Head, Link } from '@inertiajs/react';

CauseShow.layout = page => <PublicLayout>{page}</PublicLayout>;

export default function CauseShow({ cause, initiatives }) {
    return (
        <>
            <Head>
                <title>{cause.title} - Global Harmony Initiative</title>
            </Head>

            <ShowPageLayout title={cause.title} section="causes" sectionLabel="Causes" sectionUrl="/causes" image={cause.image}
                sidebar={
                    <div className="bg-light p-4 rounded mb-4">
                        <h5 className="mb-3">Details</h5>
                        <ul className="list-unstyled">
                            <li className="mb-2"><strong>Status:</strong> <StatusBadge status={cause.status} /></li>
                            {cause.icon && <li className="mb-2"><strong>Icon:</strong> <i className={`bi bi-${cause.icon}`}></i> {cause.icon}</li>}
                            <li className="mb-2"><strong>Initiatives:</strong> {initiatives.length}</li>
                        </ul>
                    </div>
                }>
                {cause.quote && (
                    <blockquote className="blockquote mb-4">
                        <p className="fst-italic text-primary">"{cause.quote}"</p>
                    </blockquote>
                )}
                <div className="mb-4">
                    {cause.description}
                </div>
            </ShowPageLayout>

            <RelatedChain currentType="cause" initiatives={initiatives} />
        </>
    );
}
