import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

type P = PageProps<{
    movement: {
        id: number;
        type: string;
        status: string;
        quantity: string;
        previous_stock: string;
        new_stock: string;
        reason: string;
        observations: string | null;
        created_at: string;
        product?: {
            id: number;
            code: string;
            name: string;
            unit: string;
            category?: { id: number; code: string; name: string };
        };
        created_by_user?: { id: number; name: string };
    };
}>;

export default function InventoryMovementShow() {
    const { movement } = usePage<P>().props;

    return (
        <IntranetLayout title={`MOV-${movement.id}`}>
            <Head title={`Movimiento #${movement.id}`} />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Inventario' },
                        {
                            label: 'Movimientos',
                            href: route('intranet.inventory.movements.index'),
                        },
                        { label: `MOV-${movement.id}` },
                    ]}
                />
                <SectionTitle
                    title={`Movimiento #${movement.id}`}
                    description="Detalle completo de trazabilidad del movimiento."
                    actions={
                        <Link
                            href={route('intranet.inventory.movements.index')}
                            className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                        >
                            Listado
                        </Link>
                    }
                />

                <Card>
                    <dl className="grid gap-4 md:grid-cols-2">
                        <div>
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Producto
                            </dt>
                            <dd className="mt-1 text-sm font-medium text-navy-900">
                                {movement.product
                                    ? `${movement.product.code} - ${movement.product.name}`
                                    : '—'}
                            </dd>
                        </div>
                        <div>
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Tipo
                            </dt>
                            <dd className="mt-1 text-sm uppercase text-navy-900">
                                {movement.type}
                            </dd>
                        </div>
                        <div>
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Estado
                            </dt>
                            <dd className="mt-1 text-sm text-navy-900">
                                {movement.status === 'anulado'
                                    ? 'Anulado'
                                    : 'Registrado'}
                            </dd>
                        </div>
                        <div>
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Cantidad
                            </dt>
                            <dd className="mt-1 font-mono text-sm text-navy-900">
                                {Number(movement.quantity).toFixed(2)}
                            </dd>
                        </div>
                        <div>
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Stock (antes / después)
                            </dt>
                            <dd className="mt-1 font-mono text-sm text-navy-900">
                                {Number(movement.previous_stock).toFixed(2)} →{' '}
                                {Number(movement.new_stock).toFixed(2)}
                            </dd>
                        </div>
                        <div className="md:col-span-2">
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Motivo
                            </dt>
                            <dd className="mt-1 text-sm text-navy-900">
                                {movement.reason}
                            </dd>
                        </div>
                        <div className="md:col-span-2">
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Observaciones
                            </dt>
                            <dd className="mt-1 text-sm text-plomo">
                                {movement.observations ?? 'Sin observaciones'}
                            </dd>
                        </div>
                        <div>
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Registrado por
                            </dt>
                            <dd className="mt-1 text-sm text-navy-900">
                                {movement.created_by_user?.name ?? '—'}
                            </dd>
                        </div>
                        <div>
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Fecha
                            </dt>
                            <dd className="mt-1 text-sm text-navy-900">
                                {movement.created_at
                                    ?.slice(0, 19)
                                    ?.replace('T', ' ')}
                            </dd>
                        </div>
                    </dl>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}

