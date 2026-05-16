import { Card } from '@/Components/Intranet/Card';
import { EmptyState } from '@/Components/Intranet/EmptyState';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { Archive } from 'lucide-react';

type Row = { name: string; size_label: string; modified_at: string };

type P = PageProps<{
    backups: Row[];
}>;

export default function SystemBackups() {
    const { backups, flash } = usePage<P>().props;

    const queueBackup = () => {
        router.post(route('intranet.system.backups.store'));
    };

    return (
        <IntranetLayout title="Respaldos">
            <Head title="Respaldos — Horizonte" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[{ label: 'Administración' }, { label: 'Respaldos' }]}
                />
                {flash?.success ? (
                    <div className="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                        {flash.success}
                    </div>
                ) : null}
                <SectionTitle
                    title="Respaldos institucionales"
                    description="Archivos ZIP en storage/app/backups (base de datos y carpeta public opcional)."
                    actions={
                        <button
                            type="button"
                            onClick={queueBackup}
                            className="rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white hover:bg-navy-800"
                        >
                            Encolar respaldo
                        </button>
                    }
                />

                {backups.length === 0 ? (
                    <EmptyState
                        icon={Archive}
                        title="Sin respaldos aún"
                        description="Ejecute un respaldo manual o espere el programador nocturno."
                    />
                ) : (
                    <TableContainer title="Archivos">
                        <table className="min-w-full text-sm">
                            <thead>
                                <tr className="border-b border-plomo/15 text-left text-xs uppercase text-plomo">
                                    <th className="px-4 py-3">Archivo</th>
                                    <th className="px-4 py-3">Tamaño</th>
                                    <th className="px-4 py-3">Modificado</th>
                                </tr>
                            </thead>
                            <tbody>
                                {backups.map((b) => (
                                    <tr key={b.name} className="border-b border-plomo/10">
                                        <td className="px-4 py-3 font-mono text-xs font-medium">{b.name}</td>
                                        <td className="px-4 py-3">{b.size_label}</td>
                                        <td className="px-4 py-3 text-plomo">
                                            {new Date(b.modified_at).toLocaleString()}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </TableContainer>
                )}

                <Card className="mt-6">
                    <p className="text-sm text-plomo">
                        Los respaldos MySQL requieren <code className="rounded bg-navy-50 px-1">mysqldump</code>{' '}
                        en el servidor (configurable con DEVOPS_MYSQLDUMP_PATH). En SQLite se incluye el archivo
                        de base dentro del ZIP.
                    </p>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
