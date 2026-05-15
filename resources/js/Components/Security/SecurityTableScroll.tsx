import { TableContainer } from '@/Components/Intranet/TableContainer';
import type { ReactNode } from 'react';

type Props = {
    title: string;
    description?: string;
    toolbar?: ReactNode;
    children: ReactNode;
    footer?: ReactNode;
};

export default function SecurityTableScroll({
    title,
    description,
    toolbar,
    children,
    footer,
}: Props) {
    return (
        <TableContainer title={title} description={description} toolbar={toolbar}>
            <div className="-mx-4 overflow-x-auto sm:mx-0">
                <div className="inline-block min-w-full align-middle px-4 sm:px-0">
                    {children}
                </div>
            </div>
            {footer ? (
                <div className="border-t border-plomo/10 px-4 py-3 sm:px-6">{footer}</div>
            ) : null}
        </TableContainer>
    );
}
