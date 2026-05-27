import Modal from '@/Components/Modal';
import type { PropsWithChildren, ReactNode } from 'react';

type AppModalProps = PropsWithChildren<{
    open: boolean;
    onClose: () => void;
    title?: ReactNode;
    maxWidth?: 'sm' | 'md' | 'lg' | 'xl' | '2xl';
}>;

export function AppModal({
    open,
    onClose,
    title,
    maxWidth = 'xl',
    children,
}: AppModalProps) {
    return (
        <Modal show={open} onClose={onClose} maxWidth={maxWidth}>
            <div className="bg-white dark:bg-slate-900">
                {title ? (
                    <div className="border-b border-slate-200 px-5 py-4 text-sm font-semibold text-navy-900 dark:border-white/10 dark:text-slate-100">
                        {title}
                    </div>
                ) : null}
                <div className="p-5">{children}</div>
            </div>
        </Modal>
    );
}
