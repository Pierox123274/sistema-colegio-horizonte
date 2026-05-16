import { Card } from '@/Components/Intranet/Card';
import { EmptyState } from '@/Components/Intranet/EmptyState';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { ListTodo } from 'lucide-react';

type Row = {
    id: number;
    uuid: string | null;
    connection: string | null;
    queue: string | null;
    exception: string | null;
    failed_at: string | null;
};

type P = PageProps<{
    failed_jobs: {
        data: Row[];
        links: { url: string | null; label: string; active: boolean }[];
        meta: { total: number; current_page: number };
    };
    queue_driver: string;
}>;

export default function SystemJobs() {
    const { failed_jobs, queue_driver } = usePage<P>().props;
    const rows = failed_jobs.data ?? [];

    return (
        <IntranetLayout title="Colas y jobs">
            <Head title="Jobs — Horizonte" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[{ label: 'Administración' }, { label: 'Colas y jobs' }]}
                />
                <SectionTitle
                    title="Colas y jobs fallidos"
                    description={`Driver de cola: ${queue_driver}. Revise fallidos antes de reintentar en producción.`}
                />

                <Card className="mb-6">
                    <div className="flex flex-wrap items-center gap-3 text-sm">
                        <span className="rounded-full bg-navy-50 px-3 py-1 font-semibold text-navy-900 ring-1 ring-navy-100">
                            Driver: {queue_driver}
                        </span>
                        <span className="rounded-full bg-plomo/10 px-3 py-1 font-medium text-navy-900">
                            Total fallidos: {failed_jobs.meta?.total ?? 0}
                        </span>
                    </div>
                </Card>

                {rows.length === 0 ? (
                    <EmptyState
                        icon={ListTodo}
                        title="Sin jobs fallidos"
                        description="Cuando un job falle, aparecerá aquí con el mensaje de excepción."
                    />
                ) : (
                    <TableContainer title="Jobs fallidos recientes">
                        <div className="overflow-x-auto">
                            <table className="min-w-full text-sm">
                                <thead>
                                    <tr className="border-b border-plomo/15 text-left text-xs uppercase text-plomo">
                                        <th className="px-4 py-3">Fecha</th>
                                        <th className="px-4 py-3">Cola</th>
                                        <th className="px-4 py-3">Conexión</th>
                                        <th className="px-4 py-3">Excepción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {rows.map((r) => (
                                        <tr key={r.id} className="border-b border-plomo/10 align-top">
                                            <td className="whitespace-nowrap px-4 py-3 text-plomo">
                                                {r.failed_at
                                                    ? new Date(r.failed_at).toLocaleString()
                                                    : '—'}
                                            </td>
                                            <td className="px-4 py-3 font-mono text-xs">{r.queue}</td>
                                            <td className="px-4 py-3 font-mono text-xs">{r.connection}</td>
                                            <td className="max-w-xl px-4 py-3 text-xs text-rose-800">
                                                <pre className="whitespace-pre-wrap break-words">
                                                    {r.exception}
                                                </pre>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </TableContainer>
                )}
            </PageContainer>
        </IntranetLayout>
    );
}
