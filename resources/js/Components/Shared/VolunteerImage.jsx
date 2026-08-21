export default function VolunteerImage({ src, title, role }) {
    return (
        <div className="col-lg-6">
            <div className="volunteer-img">
                <img src={src} className="img-fluid w-100 impact-card-img" alt={title} loading="lazy" width="400" height="500" />
                <div className="volunteer-title">
                    <h5 className="mb-2 text-white">{title}</h5>
                    <p className="mb-0 text-white">{role}</p>
                </div>
            </div>
        </div>
    );
}
