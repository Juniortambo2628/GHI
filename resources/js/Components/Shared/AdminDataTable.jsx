import StatusBadge from './StatusBadge';

export default function AdminDataTable({ columns, data, editUrl, deleteHandler }) {
    return (
        <div className="content-card">
            <div className="card-body p-0">
                <table className="table table-hover mb-0">
                    <thead>
                        <tr>
                            {columns.map((col, idx) => (
                                <th key={idx}>{col.header}</th>
                            ))}
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {data?.data?.map(item => (
                            <tr key={item.id}>
                                {columns.map((col, idx) => (
                                    <td key={idx}>{col.render ? col.render(item) : item[col.key]}</td>
                                ))}
                                <td>
                                    <StatusBadge status={item.status} />
                                </td>
                                <td>
                                    <a href={`${editUrl}/${item.id}/edit`} className="btn btn-sm btn-outline-primary me-1"><i className="bi bi-pencil"></i></a>
                                    <button onClick={() => deleteHandler(item.id)} className="btn btn-sm btn-outline-danger"><i className="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
