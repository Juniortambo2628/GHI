export default function CounterItem({ icon, label, value }) {
    return (
        <div className="col-md-6 col-lg-6 col-xl-3">
            <div className="counter-item text-center p-5">
                <i className={`bi bi-${icon} text-white counter-icon`}></i>
                <h3 className="text-white my-4">{label}</h3>
                <div className="counter-counting">
                    <span className="text-white fs-2 fw-bold">{(value || 0).toLocaleString()}</span>
                    <span className="h1 fw-bold text-white">+</span>
                </div>
            </div>
        </div>
    );
}
