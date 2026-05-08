import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import SecondaryButton from '@/Components/SecondaryButton';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';

type PaymentShow = {
    id: number;
    payment_code: string;
    amount: string;
    payment_method: string;
    paid_at: string;
    status: string;
    observations: string | null;
    student?: {
        id: number;
        code: string;
        first_name: string;
        last_name: string;
    };
    guardian?: { id: number; first_name: string; last_name: string };
    enrollment?: { id: number; enrollment_code: string };
    pension?: {
        id: number;
        month: number;
        year: number;
        payment_concept?: { code: string; name: string };
    };
    payment_concept?: { code: string; name: string };
};

type P = PageProps<{ payment: PaymentShow }>;

const STATUS_BADGE: Record<string, string> = {
    registrado: 'bg-emerald-50 text-emerald-900 ring-emerald-200',
    anulado: 'bg-plomo/15 text-plomo ring-plomo/25',
};

export default function PaymentsShow() {
    const { payment, flash } = usePage<
        P & { flash?: { success?: string } }
    >().props;

    const canCancel = payment.status === 'registrado';

    const confirmCancel = () => {
        if (
            !window.confirm(
                '¿Anular este pago? El estado de la pensión asociada se recalculará.',
            )
        ) {
            return;
        }
        router.post(route('intranet.payments.cancel', payment.id));
    };

    return (
        <IntranetLayout title={payment.payment_code}>
            <Head title={`${payment.payment_code} — Pago`} />

            <PageContainer>
                {flash?.success ? (
                    <div className="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                        {flash.success}
                    </div>
                ) : null}

                <SectionTitle
                    title={payment.payment_code}
                    description="Detalle del cobro registrado."
                    actions={
                        <div className="flex flex-wrap gap-3">
                            <a
                                href={route('intranet.payments.receipt', payment.id)}
                                target="_blank"
                                rel="noreferrer"
                                className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                            >
                                Ver comprobante
                            </a>
                            <a
                                href={route(
                                    'intranet.payments.receipt.pdf',
                                    payment.id,
                                )}
                                target="_blank"
                                rel="noreferrer"
                                className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                            >
                                Descargar PDF
                            </a>
                            <a
                                href={route(
                                    'intranet.payments.receipt.ticket',
                                    payment.id,
                                )}
                                target="_blank"
                                rel="noreferrer"
                                className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                            >
                                Imprimir ticket
                            </a>
                            <Link
                                href={route('intranet.payments.index')}
                                className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                            >
                                Listado
                            </Link>
                        </div>
                    }
                />

                <div className="mb-6 flex flex-wrap items-center gap-4">
                    <span
                        className={`inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 ${
                            STATUS_BADGE[payment.status] ?? 'bg-plomo/10'
                        }`}
                    >
                        {payment.status}
                    </span>
                    <p className="font-mono text-2xl font-bold text-navy-900">
                        S/{' '}
                        {Number(payment.amount).toFixed(2)}
                    </p>
                </div>

                <Card className="mb-6">
                    <dl className="divide-y divide-plomo/10">
                        <div className="grid gap-2 py-3 sm:grid-cols-3">
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Fecha y hora
                            </dt>
                            <dd className="sm:col-span-2">
                                {payment.paid_at?.slice(0, 19)?.replace(
                                    'T',
                                    ' ',
                                )}
                            </dd>
                        </div>
                        <div className="grid gap-2 py-3 sm:grid-cols-3">
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Método
                            </dt>
                            <dd className="sm:col-span-2">
                                {payment.payment_method}
                            </dd>
                        </div>
                        <div className="grid gap-2 py-3 sm:grid-cols-3">
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Concepto
                            </dt>
                            <dd className="sm:col-span-2">
                                {payment.payment_concept
                                    ? `${payment.payment_concept.code} — ${payment.payment_concept.name}`
                                    : '—'}
                            </dd>
                        </div>
                        <div className="grid gap-2 py-3 sm:grid-cols-3">
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Estudiante
                            </dt>
                            <dd className="sm:col-span-2">
                                {payment.student ? (
                                    <Link
                                        href={route(
                                            'intranet.students.show',
                                            payment.student.id,
                                        )}
                                        className="font-medium text-navy-900 underline-offset-2 hover:underline"
                                    >
                                        {payment.student.first_name}{' '}
                                        {payment.student.last_name}
                                        <span className="ml-2 font-mono text-xs text-plomo">
                                            {payment.student.code}
                                        </span>
                                    </Link>
                                ) : (
                                    '—'
                                )}
                            </dd>
                        </div>
                        <div className="grid gap-2 py-3 sm:grid-cols-3">
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Apoderado
                            </dt>
                            <dd className="sm:col-span-2">
                                {payment.guardian
                                    ? `${payment.guardian.first_name} ${payment.guardian.last_name}`
                                    : '—'}
                            </dd>
                        </div>
                        <div className="grid gap-2 py-3 sm:grid-cols-3">
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Matrícula
                            </dt>
                            <dd className="sm:col-span-2">
                                {payment.enrollment ? (
                                    <Link
                                        href={route(
                                            'intranet.enrollments.show',
                                            payment.enrollment.id,
                                        )}
                                        className="font-mono text-navy-900 underline-offset-2 hover:underline"
                                    >
                                        {payment.enrollment.enrollment_code}
                                    </Link>
                                ) : (
                                    '—'
                                )}
                            </dd>
                        </div>
                        <div className="grid gap-2 py-3 sm:grid-cols-3">
                            <dt className="text-xs font-semibold uppercase text-plomo">
                                Pensión
                            </dt>
                            <dd className="sm:col-span-2">
                                {payment.pension ? (
                                    <Link
                                        href={route(
                                            'intranet.pensions.show',
                                            payment.pension.id,
                                        )}
                                        className="text-navy-900 underline-offset-2 hover:underline"
                                    >
                                        {payment.pension.year}-
                                        {String(
                                            payment.pension.month,
                                        ).padStart(2, '0')}
                                        {payment.pension.payment_concept
                                            ? ` — ${payment.pension.payment_concept.name}`
                                            : ''}
                                    </Link>
                                ) : (
                                    '—'
                                )}
                            </dd>
                        </div>
                        {payment.observations ? (
                            <div className="grid gap-2 py-3 sm:grid-cols-3">
                                <dt className="text-xs font-semibold uppercase text-plomo">
                                    Observaciones
                                </dt>
                                <dd className="whitespace-pre-wrap sm:col-span-2">
                                    {payment.observations}
                                </dd>
                            </div>
                        ) : null}
                    </dl>
                </Card>

                {canCancel ? (
                    <div className="flex flex-wrap gap-3">
                        <SecondaryButton type="button" onClick={confirmCancel}>
                            Anular pago
                        </SecondaryButton>
                    </div>
                ) : (
                    <p className="text-sm text-plomo">
                        Este pago está anulado y no puede modificarse desde la
                        interfaz.
                    </p>
                )}
            </PageContainer>
        </IntranetLayout>
    );
}
