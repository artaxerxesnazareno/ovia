import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import { initializeTheme } from './hooks/use-appearance';

// Importar os componentes de páginas para que o Vite os inclua no manifesto
import './pages/Index';
import './pages/dashboard';
import './pages/welcome';

// Importar os componentes de autenticação
import './pages/auth/confirm-password';
import './pages/auth/forgot-password';
import './pages/auth/login';
import './pages/auth/register';
import './pages/auth/reset-password';
import './pages/auth/verify-email';

// Importar os componentes de configurações
import './pages/settings/appearance';
import './pages/settings/password';
import './pages/settings/profile';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => title ? `${title} - ${appName}` : appName,
    resolve: (name) => resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(<App {...props} />);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on load...
initializeTheme();
