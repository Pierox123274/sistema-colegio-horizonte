import {
    createContext,
    useCallback,
    useContext,
    useEffect,
    useMemo,
    useState,
    type PropsWithChildren,
} from 'react';

type Theme = 'light' | 'dark';

type PublicThemeContextValue = {
    theme: Theme;
    toggleTheme: () => void;
    isDark: boolean;
};

const PublicThemeContext = createContext<PublicThemeContextValue | null>(null);

const STORAGE_KEY = 'horizonte-public-theme';

function readStoredTheme(): Theme {
    if (typeof window === 'undefined') {
        return 'light';
    }
    const stored = window.localStorage.getItem(STORAGE_KEY);
    if (stored === 'dark' || stored === 'light') {
        return stored;
    }
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

export function PublicThemeProvider({ children }: PropsWithChildren) {
    const [theme, setTheme] = useState<Theme>(() => readStoredTheme());

    useEffect(() => {
        const root = document.documentElement;
        root.classList.toggle('dark', theme === 'dark');
        window.localStorage.setItem(STORAGE_KEY, theme);
    }, [theme]);

    const toggleTheme = useCallback(() => {
        setTheme((t) => (t === 'dark' ? 'light' : 'dark'));
    }, []);

    const value = useMemo(
        () => ({
            theme,
            toggleTheme,
            isDark: theme === 'dark',
        }),
        [theme, toggleTheme],
    );

    return (
        <PublicThemeContext.Provider value={value}>{children}</PublicThemeContext.Provider>
    );
}

export function usePublicTheme(): PublicThemeContextValue {
    const ctx = useContext(PublicThemeContext);
    if (ctx === null) {
        throw new Error('usePublicTheme must be used within PublicThemeProvider');
    }
    return ctx;
}
