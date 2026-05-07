import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { Pencil } from 'lucide-react';

type Concept = {
    id: number;
    code: string;
    name: string;
    description: string | null;
    default_amount: string;
    type: string;
    is_active: boolean;
    pensions_count?: number;
    payments_count?: number;
};

type P = PageProps<{ concept: Concept }>;

export default function PaymentConceptsShow() {
    const { concept } = usePage<P>().props;

    return (
        <IntranetLayout title={concept.code}>
            <Head title={`${concept.code} — Concepto`} />

            <PageContainer>
                <SectionTitle
                    title={concept.name}
                    description={`Código ${concept.code}`}
                    actions={
                        <div className="flex gap-3">
                            <Link
                                href={route(
                                    'intranet.payment-concepts.index',
                                )}
                                className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                            >
                                Listado
                            </Link>
                            <Link
                                href={route(
                                    'intranet.payment-concepts.edit',
                                    concept.id,
                                )}
                                className="inline-flex items-center gap-2 rounded-lg border border-plomo/20 bg-white px-3 py-1.5 text-sm font-semibold shadow-sm"
                            >
                                <Pencil className="h-4 w-4" />
                                Editar
                            </Link>
                        </div>
                    }
                />

                <Card>
                    <dl className="divide-y divide-plomo/10">
                        <div className="grid gap-2 py-3 sm:grid-cols-3">
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Tipo
                            </dt>
                            <dd className="sm:col-span-2">{concept.type}</dd>
                        </div>
                        <div className="grid gap-2 py-3 sm:grid-cols-3">
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Monto referencial
                            </dt>
                            <dd className="font-mono sm:col-span-2">
                                S/{' '}
                                {Number(concept.default_amount).toFixed(2)}
                            </dd>
                        </div>
                        <div className="grid gap-2 py-3 sm:grid-cols-3">
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Estado
                            </dt>
                            <dd className="sm:col-span-2">
                                {concept.is_active ? 'Activo' : 'Inactivo'}
                            </dd>
                        </div>
                        {(concept.pensions_count !== undefined ||
                            concept.payments_count !== undefined) && (
                            <div className="grid gap-2 py-3 sm:grid-cols-3">
                                <dt className="text-xs font-semibold uppercase text-plomo">
                                    Uso
                                </dt>
                                <dd className="text-sm sm:col-span-2">
                                    Pensiones: {concept.pensions_count ?? 0} ·
                                    Pagos: {concept.payments_count ?? 0}
                                </dd>
                            </div>
                        )}
                        {concept.description ? (
                            <div className="py-3">
                                <dt className="text-xs font-semibold uppercase text-plomo">
                                    Descripción
                                </dt>
                                <dd className="mt-2 whitespace-pre-wrap text-sm">
                                    {concept.description}
                                </dd>
                            </div>
                        ) : null}
                    </dl>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
