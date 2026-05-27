import { X } from 'lucide-react';
import type { PropsWithChildren, ReactNode } from 'react';

type AppDrawerProps = PropsWithChildren<{
    open: boolean;
    onClose: () => void;
    title?: ReactNode;
}>;

export function AppDrawer({ open, onClose, title, children }: AppDrawerProps) {
    if (!open) {
        return null;
    }

    return (
        <div className="fixed inset-0 z-[70]">
            <button
                type="button"
                aria-label="Cerrar panel"
                onClick={onClose}
                className="absolute inset-0 bg-navy-950/60"
            />
            <aside className="absolute right-0 top-0 h-full w-full max-w-md border-l border-slate-200 bg-white shadow-2xl dark:border-white/10 dark:bg-slate-900">
                <div className="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-white/10">
                    <h2 className="text-sm font-semibold text-navy-900 dark:text-slate-100">
                        {title}
                    </h2>
                    <button
                        type="button"
                        onClick={onClose}
                        className="rounded-md p-1 text-plomo hover:bg-slate-100 dark:hover:bg-slate-800"
                    >
                        <X className="h-4 w-4" />
                    </button>
                </div>
                <div className="h-[calc(100%-57px)] overflow-y-auto p-5">{children}</div>
            </aside>
        </div>
    );
}
