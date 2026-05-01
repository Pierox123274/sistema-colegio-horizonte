import {
    BarChart3,
    ClipboardList,
    LayoutDashboard,
    Package,
    School,
    Settings,
    ShoppingCart,
    User,
    UserCircle,
    Users,
    Wallet,
    type LucideIcon,
} from 'lucide-react';

const NAV_ICONS: Record<string, LucideIcon> = {
    'layout-dashboard': LayoutDashboard,
    school: School,
    'graduation-cap': School,
    users: Users,
    user: User,
    'user-circle': UserCircle,
    'clipboard-list': ClipboardList,
    wallet: Wallet,
    package: Package,
    'shopping-cart': ShoppingCart,
    'bar-chart-3': BarChart3,
    settings: Settings,
};

export function intranetNavIcon(key?: string | null): LucideIcon {
    if (key && NAV_ICONS[key]) {
        return NAV_ICONS[key];
    }
    return LayoutDashboard;
}

export function statsIcon(key: string): LucideIcon {
    return intranetNavIcon(key);
}
