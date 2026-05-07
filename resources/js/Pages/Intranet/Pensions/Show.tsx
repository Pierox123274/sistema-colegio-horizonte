import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { TableContainer } from '@/Components/Intranet/TableContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { CalendarClock, Pencil } from 'lucide-react';
import type { ReactNode } from 'react';

type PaymentRow = {
    id: number;
    payment_code: string;
    amount: string;
    payment_method: string;
    paid_at: string;
    status: string;
};

type PensionShow = {
    id: number;
    month: number;
    year: number;
    amount: string;
    due_date: string;
    status: string;
    observations: string | null;
    enrollment?: {
        enrollment_code: string;
        academic_year?: { name: string; year: number };
        student?: { code: string; first_name: string; last_name: string };
    };
    payment_concept?: {
        code: string;
        name: string;
        type?: string;
    };
    payments?: PaymentRow[];
};

const STATUS_BADGE: Record<string, string> = {
    pendiente: 'bg-amber-50 text-amber-900 ring-amber-200',
    parcial: 'bg-sky-50 text-sky-900 ring-sky-200',
    pagado: 'bg-emerald-50 text-emerald-900 ring-emerald-200',
    vencido: 'bg-rose-50 text-rose-900 ring-rose-200',
    anulado: 'bg-plomo/15 text-plomo ring-plomo/25',
};

const PENSION_STATUS_LABEL: Record<string, string> = {
    pendiente: 'Pendiente de pago',
    parcial: 'Pago parcial',
    pagado: 'Pagado',
    vencido: 'Vencido',
    anulado: 'Anulado',
};

const CONCEPT_TYPE_LABEL: Record<string, string> = {
    matricula: 'Matrícula',
    pension: 'Pensión',
    uniforme: 'Uniforme',
    libro: 'Libro',
    otro: 'Otro',
};

const PAY_STATUS: Record<string, string> = {
    registrado: 'bg-emerald-50 text-emerald-900 ring-emerald-200',
    anulado: 'bg-plomo/15 text-plomo ring-plomo/25',
};

function EmptyValue({
    message,
    subtle,
}: {
    message: string;
    subtle?: boolean;
}) {
    return (
        <span
            className={`italic ${subtle ? 'text-plomo/70' : 'text-plomo'}`}
            title={message}
        >
            {message}
        </span>
    );
}

type FieldRowProps = {
    label: string;
    children: ReactNode;
};

function FieldRow({ label, children }: FieldRowProps) {
    return (
        <div className="grid gap-1 border-b border-plomo/10 py-4 last:border-b-0 sm:grid-cols-[minmax(0,11rem)_1fr] sm:items-start sm:gap-6">
            <dt className="text-[11px] font-semibold uppercase tracking-wide text-plomo">
                {label}
            </dt>
            <dd className="min-w-0 text-sm leading-snug text-navy-900">
                {children}
            </dd>
        </div>
    );
}

type P = PageProps<{
    pension: PensionShow;
    pending_amount: string;
}>;

export default function PensionsShow() {
    const { pension, pending_amount } = usePage<P>().props;
    const payments = pension.payments ?? [];

    const periodLabel = `${pension.year}-${String(pension.month).padStart(2, '0')}`;
    const student = pension.enrollment?.student;
    const enrollmentCode = pension.enrollment?.enrollment_code;
    const academicYear = pension.enrollment?.academic_year;
    const concept = pension.payment_concept;

    const dueFormatted =
        pension.due_date && pension.due_date.length >= 10
            ? pension.due_date.slice(0, 10)
            : null;

    const statusLabel =
        PENSION_STATUS_LABEL[pension.status] ?? pension.status;

    return (
        <IntranetLayout title={`Pensión ${periodLabel}`}>
            <Head title={`Pensión ${periodLabel} — Horizonte`} />

            <PageContainer>
                <header className="mb-6 flex flex-col gap-4 md:mb-8 md:flex-row md:items-start md:justify-between">
                    <div className="min-w-0">
                        <h1 className="text-2xl font-bold tracking-tight text-navy-900 md:text-3xl">
                            Obligación {periodLabel}
                        </h1>
                        <p className="mt-1 max-w-2xl text-sm text-plomo md:text-base">
                            {concept
                                ? `${concept.name} · código ${concept.code}`
                                : 'Detalle de pensión por matrícula y periodo'}
                        </p>
                    </div>
                    <div className="flex w-full shrink-0 flex-wrap items-center gap-x-4 gap-y-3 md:w-auto md:justify-end">
                            <Link
                                href={route('intranet.pensions.index')}
                                className="inline-flex items-center text-sm font-semibold leading-none text-navy-900 underline-offset-2 hover:underline"
                            >
                                Listado
                            </Link>
                            <div className="flex items-center gap-3">
                                <span
                                    className={`inline-flex rounded-full px-3 py-1.5 text-xs font-semibold leading-none ring-1 ring-inset ${
                                        STATUS_BADGE[pension.status] ??
                                        'bg-plomo/10 text-plomo ring-plomo/20'
                                    }`}
                                >
                                    {statusLabel}
                                </span>
                                <Link
                                    href={route(
                                        'intranet.pensions.edit',
                                        pension.id,
                                    )}
                                    className="inline-flex h-10 items-center gap-2 rounded-lg border border-plomo/20 bg-white px-3 text-sm font-semibold leading-none shadow-sm hover:bg-navy-50/80"
                                >
                                    <Pencil className="h-4 w-4 shrink-0" />
                                    Editar
                                </Link>
                            </div>
                    </div>
                </header>

                <div className="mb-8 grid gap-4 sm:grid-cols-3">
                    <Card className="border-plomo/15 p-5 shadow-sm">
                        <p className="text-[11px] font-semibold uppercase tracking-wide text-plomo">
                            Saldo pendiente
                        </p>
                        <p className="mt-2 font-mono text-2xl font-bold tabular-nums text-navy-900">
                            S/{' '}
                            {Number(pending_amount).toFixed(2)}
                        </p>
                        <p className="mt-2 text-xs leading-relaxed text-plomo">
                            Monto aún por cobrar según pagos registrados.
                        </p>
                    </Card>
                    <Card className="border-plomo/15 p-5 shadow-sm">
                        <p className="text-[11px] font-semibold uppercase tracking-wide text-plomo">
                            Monto de la obligación
                        </p>
                        <p className="mt-2 font-mono text-2xl font-bold tabular-nums text-navy-900">
                            S/{' '}
                            {Number(pension.amount).toFixed(2)}
                        </p>
                        <p className="mt-2 text-xs leading-relaxed text-plomo">
                            Importe fijado para este mes en la matrícula.
                        </p>
                    </Card>
                    <Card className="border-plomo/15 p-5 shadow-sm">
                        <p className="text-[11px] font-semibold uppercase tracking-wide text-plomo">
                            Estado
                        </p>
                        <div className="mt-3">
                            <span
                                className={`inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset ${
                                    STATUS_BADGE[pension.status] ??
                                    'bg-plomo/10 text-plomo ring-plomo/20'
                                }`}
                            >
                                {statusLabel}
                            </span>
                        </div>
                        <p className="mt-3 flex items-start gap-2 text-xs leading-relaxed text-plomo">
                            <CalendarClock
                                className="mt-0.5 h-3.5 w-3.5 shrink-0 text-plomo/80"
                                aria-hidden
                            />
                            {dueFormatted ? (
                                <>
                                    Fecha de vencimiento:{' '}
                                    <time
                                        dateTime={dueFormatted}
                                        className="font-mono text-navy-900"
                                    >
                                        {dueFormatted}
                                    </time>
                                    .
                                </>
                            ) : (
                                <span className="italic text-plomo/80">
                                    Fecha de vencimiento no registrada en esta
                                    obligación.
                                </span>
                            )}
                        </p>
                    </Card>
                </div>

                <div className="mb-8 grid gap-6 lg:grid-cols-2">
                    <Card className="border-plomo/15 p-0 shadow-sm">
                        <div className="border-b border-plomo/10 bg-navy-50/60 px-5 py-4">
                            <h2 className="text-sm font-semibold text-navy-900">
                                Estudiante y matrícula
                            </h2>
                            <p className="mt-0.5 text-xs text-plomo">
                                Quién debe y en qué ciclo está inscrito.
                            </p>
                        </div>
                        <dl className="px-5 pb-2 pt-1">
                            <FieldRow label="Estudiante">
                                {student ? (
                                    <div className="space-y-1">
                                        <span className="block font-semibold text-navy-900">
                                            {student.first_name}{' '}
                                            {student.last_name}
                                        </span>
                                        <span className="inline-block rounded bg-plomo/10 px-2 py-0.5 font-mono text-xs text-navy-900">
                                            Código: {student.code}
                                        </span>
                                    </div>
                                ) : (
                                    <EmptyValue message="No hay estudiante vinculado a esta matrícula en el sistema." />
                                )}
                            </FieldRow>
                            <FieldRow label="Código de matrícula">
                                {enrollmentCode ? (
                                    <span className="font-mono text-sm font-medium">
                                        {enrollmentCode}
                                    </span>
                                ) : (
                                    <EmptyValue message="Sin código de matrícula en esta ficha. Verifique la relación con enrollments." />
                                )}
                            </FieldRow>
                            <FieldRow label="Año académico">
                                {academicYear?.name ? (
                                    <div className="space-y-0.5">
                                        <span className="font-medium">
                                            {academicYear.name}
                                        </span>
                                        <span className="block text-xs text-plomo">
                                            Año calendario: {academicYear.year}
                                        </span>
                                    </div>
                                ) : (
                                    <EmptyValue message="El año académico no está disponible. Revise la matrícula asociada." />
                                )}
                            </FieldRow>
                        </dl>
                    </Card>

                    <Card className="border-plomo/15 p-0 shadow-sm">
                        <div className="border-b border-plomo/10 bg-navy-50/60 px-5 py-4">
                            <h2 className="text-sm font-semibold text-navy-900">
                                Concepto de pago
                            </h2>
                            <p className="mt-0.5 text-xs text-plomo">
                                Qué se cobra y cómo está catalogado.
                            </p>
                        </div>
                        <dl className="px-5 pb-2 pt-1">
                            <FieldRow label="Nombre">
                                {concept?.name ? (
                                    <span className="font-medium leading-snug">
                                        {concept.name}
                                    </span>
                                ) : (
                                    <EmptyValue message="No hay concepto de pago asociado a esta pensión." />
                                )}
                            </FieldRow>
                            <FieldRow label="Código del concepto">
                                {concept?.code ? (
                                    <span className="font-mono text-sm font-semibold">
                                        {concept.code}
                                    </span>
                                ) : (
                                    <EmptyValue message="Sin código de concepto registrado." />
                                )}
                            </FieldRow>
                            <FieldRow label="Tipo">
                                {concept?.type ? (
                                    <span className="inline-flex rounded-md bg-white px-2.5 py-1 text-xs font-semibold ring-1 ring-plomo/20">
                                        {CONCEPT_TYPE_LABEL[concept.type] ??
                                            concept.type}
                                    </span>
                                ) : (
                                    <EmptyValue message="Tipo de concepto no definido." />
                                )}
                            </FieldRow>
                            <FieldRow label="Vencimiento">
                                {dueFormatted ? (
                                    <time
                                        dateTime={dueFormatted}
                                        className="font-mono text-sm font-medium"
                                    >
                                        {dueFormatted}
                                    </time>
                                ) : (
                                    <EmptyValue message="No se registró fecha de vencimiento para esta obligación." />
                                )}
                            </FieldRow>
                        </dl>
                    </Card>
                </div>

                {pension.observations ? (
                    <Card className="mb-8 border-plomo/15 p-5 shadow-sm">
                        <h2 className="text-sm font-semibold text-navy-900">
                            Observaciones
                        </h2>
                        <p className="mt-3 whitespace-pre-wrap text-sm leading-relaxed text-navy-900">
                            {pension.observations}
                        </p>
                    </Card>
                ) : null}

                <TableContainer
                    title="Pagos asociados"
                    description={`${payments.length} movimiento${payments.length === 1 ? '' : 's'} registrados contra esta pensión.`}
                >
                    {payments.length === 0 ? (
                        <div className="px-6 py-10 text-center">
                            <p className="text-sm font-medium text-navy-900">
                                Aún no hay cobros registrados
                            </p>
                            <p className="mx-auto mt-2 max-w-md text-sm leading-relaxed text-plomo">
                                Cuando se registre un pago vinculado a esta
                                pensión, aparecerá aquí con código, monto y
                                método.
                            </p>
                            <Link
                                href={route('intranet.payments.create')}
                                className="mt-4 inline-flex text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                            >
                                Ir a registrar pago
                            </Link>
                        </div>
                    ) : (
                        <table className="min-w-full text-left text-sm">
                            <thead className="border-b border-plomo/10 bg-navy-50/80 text-xs font-semibold uppercase tracking-wide text-plomo">
                                <tr>
                                    <th className="px-4 py-3">Código</th>
                                    <th className="px-4 py-3">Monto</th>
                                    <th className="px-4 py-3">Método</th>
                                    <th className="px-4 py-3">Fecha</th>
                                    <th className="px-4 py-3">Estado</th>
                                    <th className="px-4 py-3 text-right">
                                        Acción
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-plomo/10">
                                {payments.map((p) => (
                                    <tr
                                        key={p.id}
                                        className="hover:bg-navy-50/40"
                                    >
                                        <td className="px-4 py-3 font-mono text-xs">
                                            {p.payment_code}
                                        </td>
                                        <td className="px-4 py-3 font-mono text-xs tabular-nums">
                                            S/{' '}
                                            {Number(p.amount).toFixed(2)}
                                        </td>
                                        <td className="px-4 py-3 capitalize">
                                            {p.payment_method}
                                        </td>
                                        <td className="px-4 py-3 text-xs tabular-nums">
                                            {p.paid_at?.slice(0, 16)?.replace(
                                                'T',
                                                ' ',
                                            )}
                                        </td>
                                        <td className="px-4 py-3">
                                            <span
                                                className={`inline-flex rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ${
                                                    PAY_STATUS[p.status] ??
                                                    'bg-plomo/10'
                                                }`}
                                            >
                                                {p.status}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-right">
                                            <Link
                                                href={route(
                                                    'intranet.payments.show',
                                                    p.id,
                                                )}
                                                className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                                            >
                                                Ver
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </TableContainer>
            </PageContainer>
        </IntranetLayout>
    );
}
