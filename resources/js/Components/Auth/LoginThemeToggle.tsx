import { usePublicTheme } from '@/Components/Public/Premium/PublicThemeProvider';
import { Moon, Sun } from 'lucide-react';

/**
 * Toggle de tema para la pantalla de login.
 * Ubicar solo en el header de página, nunca dentro del card del formulario.
 */
export function LoginThemeToggle() {
    const { isDark, toggleTheme } = usePublicTheme();
    const label = isDark ? 'Activar modo claro' : 'Activar modo oscuro';

    return (
        <button
            type="button"
            onClick={toggleTheme}
            title={label}
            aria-label={label}
            className="group inline-flex h-9 w-9 items-center justify-center rounded-full text-slate-600 transition hover:bg-slate-900/5 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/50 focus-visible:ring-offset-2 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white dark:focus-visible:ring-offset-slate-950"
        >
            {isDark ? (
                <Sun className="h-[1.125rem] w-[1.125rem] transition group-hover:scale-105" />
            ) : (
                <Moon className="h-[1.125rem] w-[1.125rem] transition group-hover:scale-105" />
            )}
        </button>
    );
}
