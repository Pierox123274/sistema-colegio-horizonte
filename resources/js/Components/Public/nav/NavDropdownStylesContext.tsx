import { createContext, useContext, useMemo, type PropsWithChildren } from 'react';
import { getNavDropdownStyles, type NavDropdownStyles } from './navDropdownStyles';

const NavDropdownStylesContext = createContext<NavDropdownStyles>(getNavDropdownStyles(false));

export function NavDropdownStylesProvider({
    isDark,
    children,
}: PropsWithChildren<{ isDark: boolean }>) {
    const styles = useMemo(() => getNavDropdownStyles(isDark), [isDark]);
    return (
        <NavDropdownStylesContext.Provider value={styles}>{children}</NavDropdownStylesContext.Provider>
    );
}

export function useNavDropdownStyles(): NavDropdownStyles {
    return useContext(NavDropdownStylesContext);
}
