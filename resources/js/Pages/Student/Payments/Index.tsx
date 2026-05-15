import PaymentStatusBadge from '@/Components/Student/PaymentStatusBadge';
import PensionStatusBadge from '@/Components/Student/PensionStatusBadge';
import StudentPortalEmpty from '@/Components/Student/StudentPortalEmpty';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import { StatsCard } from '@/Components/Intranet/StatsCard';
import StudentLayout from '@/Layouts/StudentLayout';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { CheckCircle2, Clock, Receipt, Wallet } from 'lucide-react';

type PensionRow = {
    id: number;
    month: number;
    year: number;
    amount: string;
    pending: string;
    status: string;
    concept: string | null;
    period_label: string;
    due_date_label: string;
    amount_label: string;
    pending_label: string;
    status_label: string;
};

type PaymentRow = {
    id: number;
    payment_code: string;
    amount_label: string;
    paid_at_label: string;
    status: string;
    status_label: string;
    payment_method_label: string;
    payment_concept?: { name: string };
};

type Summary = {
    total_count: number;
    paid_count: number;
    pending_count: number;
    pending_amount: number;
    pending_amount_label: string;
};

type Props = PageProps<{
    student: { full_name: string; code: string } | null;
    summary: Summary;
    pensions: PensionRow[];
    payments: { data: PaymentRow[] } | null;
    has_pending_pensions: boolean;
    has_student: boolean;
    portal_scoped: boolean;
    empty_message: string;
}>;

export default function StudentPaymentsIndex() {
    const {
        student,
        summary,
        pensions,
        payments,
        has_pending_pensions,
        has_student,
        portal_scoped,
        empty_message,
    } = usePage<Props>().props;

    const paymentRows = payments?.data ?? [];

    return (
        <StudentLayout title="Mis pagos">
            <Head title="Mis pagos" />
            <PageContainer>
                <SectionTitle
                    title="Mis pagos y pensiones"
                    description="Consulta el estado de tus pensiones escolares y los pagos realizados."
                />

                {!has_student ? (
                    <StudentPortalEmpty message={empty_message} portalScoped={portal_scoped} />
                ) : (
                    <>
                        {student && (
                            <p className="mb-6 text-sm text-plomo">
                                Estudiante:{' '}
                                <span className="font-semibold text-navy-900">
                                    {student.full_name}
                                </span>{' '}
                                · Código {student.code}
                            </p>
                        )}

                        <div className="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            <StatsCard
                                title="Total pensiones"
                                value={String(summary.total_count)}
                                subtitle="Registradas a tu nombre"
                                icon={Receipt}
                                accent="navy"
                            />
                            <StatsCard
                                title="Pagadas"
                                value={String(summary.paid_count)}
                                subtitle="Al día con el colegio"
                                icon={CheckCircle2}
                                accent="yellow"
                            />
                            <StatsCard
                                title="Pendientes"
                                value={String(summary.pending_count)}
                                subtitle="Por regularizar"
                                icon={Clock}
                                accent="red"
                            />
                            <StatsCard
                                title="Monto pendiente"
                                value={summary.pending_amount_label}
                                subtitle="Saldo por cubrir"
                                icon={Wallet}
                                accent="navy"
                            />
                        </div>

                        {!has_pending_pensions && summary.total_count > 0 && (
                            <div className="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                                No tienes pagos pendientes. ¡Gracias por estar al día!
                            </div>
                        )}

                        <Card className="mb-8">
                            <h3 className="text-base font-semibold text-navy-900">
                                Estado de pensiones
                            </h3>
                            <p className="mt-1 text-sm text-plomo">
                                Detalle por concepto y periodo de facturación.
                            </p>
                            {pensions.length === 0 ? (
                                <p className="mt-4 text-sm text-plomo">
                                    Aún no hay pensiones registradas a tu nombre.
                                </p>
                            ) : (
                                <div className="mt-4 overflow-x-auto">
                                    <table className="min-w-full text-sm">
                                        <thead>
                                            <tr className="border-b border-plomo/15 text-left text-xs font-semibold uppercase tracking-wide text-plomo">
                                                <th className="px-3 py-3">Concepto</th>
                                                <th className="px-3 py-3">Periodo</th>
                                                <th className="px-3 py-3">Vencimiento</th>
                                                <th className="px-3 py-3">Monto</th>
                                                <th className="px-3 py-3">Pendiente</th>
                                                <th className="px-3 py-3">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {pensions.map((row) => (
                                                <tr
                                                    key={row.id}
                                                    className="border-b border-plomo/10 hover:bg-navy-50/40"
                                                >
                                                    <td className="px-3 py-3 font-medium text-navy-900">
                                                        {row.concept ?? 'Pensión escolar'}
                                                    </td>
                                                    <td className="px-3 py-3">{row.period_label}</td>
                                                    <td className="px-3 py-3 whitespace-nowrap">
                                                        {row.due_date_label}
                                                    </td>
                                                    <td className="px-3 py-3 whitespace-nowrap">
                                                        {row.amount_label}
                                                    </td>
                                                    <td className="px-3 py-3 whitespace-nowrap font-semibold text-navy-900">
                                                        {row.pending_label}
                                                    </td>
                                                    <td className="px-3 py-3">
                                                        <PensionStatusBadge
                                                            status={row.status}
                                                            label={row.status_label}
                                                        />
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            )}
                        </Card>

                        <Card>
                            <h3 className="text-base font-semibold text-navy-900">
                                Historial de pagos
                            </h3>
                            <p className="mt-1 text-sm text-plomo">
                                Comprobantes registrados en secretaría.
                            </p>
                            {paymentRows.length === 0 ? (
                                <p className="mt-4 text-sm text-plomo">
                                    No hay pagos registrados todavía.
                                </p>
                            ) : (
                                <div className="mt-4 overflow-x-auto">
                                    <table className="min-w-full text-sm">
                                        <thead>
                                            <tr className="border-b border-plomo/15 text-left text-xs font-semibold uppercase tracking-wide text-plomo">
                                                <th className="px-3 py-3">Código</th>
                                                <th className="px-3 py-3">Concepto</th>
                                                <th className="px-3 py-3">Fecha</th>
                                                <th className="px-3 py-3">Monto</th>
                                                <th className="px-3 py-3">Método</th>
                                                <th className="px-3 py-3">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {paymentRows.map((row) => (
                                                <tr
                                                    key={row.id}
                                                    className="border-b border-plomo/10 hover:bg-navy-50/40"
                                                >
                                                    <td className="px-3 py-3 font-mono text-xs text-plomo">
                                                        {row.payment_code}
                                                    </td>
                                                    <td className="px-3 py-3">
                                                        {row.payment_concept?.name ?? '—'}
                                                    </td>
                                                    <td className="px-3 py-3 whitespace-nowrap">
                                                        {row.paid_at_label}
                                                    </td>
                                                    <td className="px-3 py-3 font-semibold text-navy-900">
                                                        {row.amount_label}
                                                    </td>
                                                    <td className="px-3 py-3">
                                                        {row.payment_method_label}
                                                    </td>
                                                    <td className="px-3 py-3">
                                                        <PaymentStatusBadge
                                                            status={row.status}
                                                            label={row.status_label}
                                                        />
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            )}
                        </Card>
                    </>
                )}
            </PageContainer>
        </StudentLayout>
    );
}
