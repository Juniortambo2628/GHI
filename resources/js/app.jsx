import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { UploadProvider } from './Contexts/UploadContext';
import UploadProgressWidget from './Components/Shared/UploadProgressWidget';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.jsx`, import.meta.glob('./Pages/**/*.jsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(
            <UploadProvider>
                <App {...props} />
                <UploadProgressWidget />
            </UploadProvider>
        );
    },
    progress: {
        color: '#4F46E5',
    },
});
