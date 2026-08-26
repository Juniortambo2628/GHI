import { useEffect, useRef } from 'react';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

export default function AnalyticsChart({ views }) {
    const canvas = useRef(null);

    useEffect(() => {
        if (!canvas.current) return undefined;
        const chart = new Chart(canvas.current, {
            type: 'line',
            data: {
                labels: views.map(day => day.date),
                datasets: [{
                    label: 'Page views',
                    data: views.map(day => day.views),
                    borderColor: '#000656',
                    backgroundColor: 'rgba(0, 6, 86, 0.12)',
                    fill: true,
                    tension: 0.35,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } },
            },
        });
        return () => chart.destroy();
    }, [views]);

    return <div className="admin-chart"><canvas ref={canvas} aria-label="Page views over time" role="img"></canvas></div>;
}
