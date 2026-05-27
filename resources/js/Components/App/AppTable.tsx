import { TableContainer } from '@/Components/Intranet/TableContainer';
import type { ReactNode } from 'react';

type AppTableProps = {
    title?: string;
    description?: string;
    toolbar?: ReactNode;
    children: ReactNode;
    stickyHeader?: boolean;
};

export function AppTable(props: AppTableProps) {
    return <TableContainer {...props} />;
}
