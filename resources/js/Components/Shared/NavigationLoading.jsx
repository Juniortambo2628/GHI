import { useEffect, useState } from 'react';
import { router } from '@inertiajs/react';

export default function NavigationLoading() {
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        const start = router.on('start', () => setLoading(true));
        const finish = router.on('finish', () => setLoading(false));
        return () => { start(); finish(); };
    }, []);

    return loading ? <div className="navigation-loading" role="progressbar" aria-label="Loading" /> : null;
}
