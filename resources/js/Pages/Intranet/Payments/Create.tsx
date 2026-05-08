import { Card } from '@/Components/Intranet/Card';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type {
    EnrollmentStudentPreview,
    EnrollmentStudentSearchHit,
    PageProps,
    SelectOption,
} from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import axios from 'axios';
import { Loader2, Search, User } from 'lucide-react';
import type { FormEventHandler } from 'react';
import {
    useCallback,
    useEffect,
    useMemo,
    useRef,
    useState,
} from 'react';

const inputClass =
    'mt-1 block w-full rounded-md border-plomo/25 shadow-sm focus:border-navy-900 focus:ring-navy-900';

const SEARCH_MIN = 2;
const DEBOUNCE_MS = 320;

function localDatetimeInputValue(d = new Date()): string {
    const pad = (n: number) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

type PensionSummaryRow = {
    id: number;
    payment_concept_id: number;
    enrollment_id: number;
    enrollment_code: string;
    month: number;
    year: number;
    due_date: string;
    amount: string;
    pending: string;
    status: string;
    concept: string | null;
};

type RecentPayment = {
    id: number;
    payment_code: string;
    amount: string;
    paid_at: string;
    status: string;
    payment_concept?: { name: string };
};

type SummaryResp = {
    pensions: PensionSummaryRow[];
    payments_recent: RecentPayment[];
};

type P = PageProps<{
    catalog: {
        concepts: SelectOption[];
        methods: SelectOption[];
        statuses: SelectOption[];
    };
}>;

export default function PaymentsCreate() {
    const { catalog } = usePage<P>().props;

    const form = useForm({
        student_id: '',
        guardian_id: '',
        enrollment_id: '',
        pension_id: '',
        payment_concept_id: catalog.concepts[0]?.value ?? '',
        amount: '',
        payment_method: catalog.methods[0]?.value ?? 'efectivo',
        paid_at: localDatetimeInputValue(),
        observations: '',
    });

    const [searchText, setSearchText] = useState('');
    const [searchHits, setSearchHits] = useState<EnrollmentStudentSearchHit[]>(
        [],
    );
    const [searchLoading, setSearchLoading] = useState(false);
    const [searchOpen, setSearchOpen] = useState(false);
    const [searchError, setSearchError] = useState<string | null>(null);
    const [pickLoading, setPickLoading] = useState(false);

    const [preview, setPreview] = useState<EnrollmentStudentPreview | null>(
        null,
    );
    const [summary, setSummary] = useState<SummaryResp | null>(null);

    const searchWrapRef = useRef<HTMLDivElement>(null);

    const guardianOptions = preview?.guardians ?? [];
    const allowedPaymentMethods = useMemo(
        () => new Set(catalog.methods.map((m) => m.value)),
        [catalog.methods],
    );

    useEffect(() => {
        const allowed = new Set(guardianOptions.map((g) => g.value));
        if (form.data.guardian_id && !allowed.has(form.data.guardian_id)) {
            form.setData('guardian_id', '');
        }
    }, [form.data.guardian_id, guardianOptions]);

    useEffect(() => {
        const q = searchText.trim();
        if (q.length < SEARCH_MIN) {
            setSearchHits([]);
            setSearchLoading(false);
            setSearchError(null);
            return;
        }

        setSearchLoading(true);
        setSearchError(null);
        const id = window.setTimeout(() => {
            axios
                .get<{ students: EnrollmentStudentSearchHit[] }>(
                    route('intranet.payments.students.search'),
                    { params: { q } },
                )
                .then((res) => {
                    setSearchHits(res.data.students ?? []);
                })
                .catch(() => {
                    setSearchError('No se pudo cargar la búsqueda.');
                    setSearchHits([]);
                })
                .finally(() => {
                    setSearchLoading(false);
                });
        }, DEBOUNCE_MS);

        return () => clearTimeout(id);
    }, [searchText]);

    useEffect(() => {
        const onDocDown = (e: MouseEvent) => {
            const el = searchWrapRef.current;
            if (!el || !searchOpen) {
                return;
            }
            if (e.target instanceof Node && !el.contains(e.target)) {
                setSearchOpen(false);
            }
        };
        document.addEventListener('mousedown', onDocDown);
        return () => document.removeEventListener('mousedown', onDocDown);
    }, [searchOpen]);

    const pickStudent = useCallback(async (hit: EnrollmentStudentSearchHit) => {
        setPickLoading(true);
        setSearchOpen(false);
        setSearchError(null);
        try {
            const [sumRes, prevRes] = await Promise.all([
                axios.get<SummaryResp>(
                    route('intranet.payments.students.summary', hit.id),
                ),
                axios.get<{ preview: EnrollmentStudentPreview }>(
                    route('intranet.enrollments.students.preview', hit.id),
                ),
            ]);
            setSummary(sumRes.data);
            setPreview(prevRes.data.preview);
            form.setData({
                student_id: String(hit.id),
                guardian_id: '',
                enrollment_id: '',
                pension_id: '',
                payment_concept_id:
                    sumRes.data.pensions[0]?.payment_concept_id != null
                        ? String(sumRes.data.pensions[0].payment_concept_id)
                        : catalog.concepts[0]?.value ?? '',
                amount:
                    sumRes.data.pensions[0]?.pending != null
                        ? sumRes.data.pensions[0].pending
                        : '',
                payment_method: form.data.payment_method,
                paid_at: localDatetimeInputValue(),
                observations: form.data.observations,
            });
            setSearchText('');
            setSearchHits([]);
        } catch {
            setSearchError('No se pudo cargar el estudiante.');
            setSummary(null);
            setPreview(null);
        } finally {
            setPickLoading(false);
        }
    }, []);

    const pensionOptions = useMemo(() => {
        const rows = summary?.pensions ?? [];
        return rows.filter(
            (p) =>
                p.status !== 'anulado' &&
                Number.parseFloat(p.pending) > 0.001,
        );
    }, [summary]);

    const onPensionChange = (pensionId: string) => {
        if (!pensionId) {
            form.setData((prev) => ({
                ...prev,
                pension_id: '',
            }));
            return;
        }
        const row = summary?.pensions.find(
            (p) => String(p.id) === pensionId,
        );
        if (!row) {
            form.setData((prev) => ({
                ...prev,
                pension_id: pensionId,
            }));
            return;
        }
        form.setData((prev) => ({
            ...prev,
            pension_id: pensionId,
            enrollment_id: String(row.enrollment_id),
            payment_concept_id: String(row.payment_concept_id),
            amount: row.pending,
        }));
    };

    const canSubmit = useMemo(() => {
        const studentId = form.data.student_id.trim();
        const conceptId = form.data.payment_concept_id.trim();
        const amount = Number.parseFloat(form.data.amount);
        const hasValidAmount = Number.isFinite(amount) && amount > 0;
        const paymentMethod = form.data.payment_method.trim();
        const hasValidMethod = allowedPaymentMethods.has(paymentMethod);
        const paidAt = form.data.paid_at.trim();
        const hasValidPaidAt =
            paidAt.length > 0 &&
            !Number.isNaN(new Date(paidAt).getTime());

        return (
            studentId.length > 0 &&
            conceptId.length > 0 &&
            hasValidAmount &&
            hasValidMethod &&
            hasValidPaidAt
        );
    }, [
        allowedPaymentMethods,
        form.data.amount,
        form.data.paid_at,
        form.data.payment_concept_id,
        form.data.payment_method,
        form.data.student_id,
    ]);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.post(route('intranet.payments.store'));
    };

    return (
        <IntranetLayout title="Registrar pago">
            <Head title="Registrar pago — Horizonte" />

            <PageContainer>
                <SectionTitle
                    title="Registrar pago"
                    description="Busque al estudiante, revise deuda y registre el cobro."
                    actions={
                        <Link
                            href={route('intranet.payments.index')}
                            className="text-sm font-semibold text-navy-900 underline-offset-2 hover:underline"
                        >
                            Volver
                        </Link>
                    }
                />

                <div className="relative mb-6" ref={searchWrapRef}>
                    <InputLabel value="Estudiante *" />
                    <div className="relative mt-1">
                        <Search
                            className="pointer-events-none absolute left-3 top-1/2 z-10 h-4 w-4 -translate-y-1/2 text-plomo"
                            strokeWidth={2}
                            aria-hidden
                        />
                        <input
                            type="search"
                            autoComplete="off"
                            enterKeyHint="search"
                            placeholder="Mín. 2 caracteres: código, nombre o documento"
                            value={searchText}
                            onChange={(e) => {
                                setSearchText(e.target.value);
                                setSearchOpen(true);
                            }}
                            onFocus={() => setSearchOpen(true)}
                            disabled={pickLoading}
                            className="box-border block h-11 w-full rounded-md border border-plomo/25 bg-white py-2 pl-10 pr-10 text-sm leading-normal text-navy-900 shadow-sm placeholder:text-plomo/55 focus:border-navy-900 focus:outline-none focus:ring-1 focus:ring-navy-900 disabled:cursor-not-allowed disabled:bg-plomo/5"
                        />
                        {(searchLoading || pickLoading) && (
                            <Loader2
                                className="pointer-events-none absolute right-3 top-1/2 z-10 h-4 w-4 -translate-y-1/2 animate-spin text-plomo"
                                aria-hidden
                            />
                        )}
                    </div>
                    {searchError ? (
                        <p className="mt-1 text-sm text-red-600">
                            {searchError}
                        </p>
                    ) : null}
                    {searchOpen && searchHits.length > 0 ? (
                        <ul className="absolute z-20 mt-1 max-h-60 w-full overflow-auto rounded-md border border-plomo/20 bg-white py-1 shadow-lg">
                            {searchHits.map((h) => (
                                <li key={h.id}>
                                    <button
                                        type="button"
                                        className="flex w-full items-start gap-2 px-3 py-2 text-left text-sm hover:bg-navy-50"
                                        onClick={() => void pickStudent(h)}
                                    >
                                        <User className="mt-0.5 h-4 w-4 shrink-0 text-plomo" />
                                        <span>
                                            <span className="font-medium text-navy-900">
                                                {h.first_name} {h.last_name}
                                            </span>
                                            <span className="ml-2 font-mono text-xs text-plomo">
                                                {h.code}
                                            </span>
                                        </span>
                                    </button>
                                </li>
                            ))}
                        </ul>
                    ) : null}
                </div>

                {preview ? (
                    <Card className="mb-6">
                        <div className="flex flex-wrap items-center gap-3">
                            <div>
                                <p className="text-xs font-semibold uppercase text-plomo">
                                    Seleccionado
                                </p>
                                <p className="font-semibold text-navy-900">
                                    {preview.first_name} {preview.last_name}{' '}
                                    <span className="font-mono text-sm text-plomo">
                                        ({preview.code})
                                    </span>
                                </p>
                            </div>
                        </div>
                    </Card>
                ) : null}

                {summary && preview ? (
                    <Card className="mb-6">
                        <h3 className="text-sm font-semibold text-navy-900">
                            Deuda por pensión
                        </h3>
                        <div className="mt-3 overflow-x-auto">
                            <table className="min-w-full text-left text-xs">
                                <thead className="border-b text-plomo">
                                    <tr>
                                        <th className="py-2 pr-2">Periodo</th>
                                        <th className="py-2 pr-2">Matrícula</th>
                                        <th className="py-2 pr-2">Concepto</th>
                                        <th className="py-2 pr-2">Pendiente</th>
                                        <th className="py-2">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {summary.pensions.length === 0 ? (
                                        <tr>
                                            <td
                                                colSpan={5}
                                                className="py-3 text-plomo"
                                            >
                                                Sin obligaciones de pensión
                                                registradas.
                                            </td>
                                        </tr>
                                    ) : (
                                        summary.pensions.map((p) => (
                                            <tr
                                                key={p.id}
                                                className="border-t border-plomo/10"
                                            >
                                                <td className="py-2 pr-2 font-mono">
                                                    {p.year}-
                                                    {String(p.month).padStart(
                                                        2,
                                                        '0',
                                                    )}
                                                </td>
                                                <td className="py-2 pr-2 font-mono">
                                                    {p.enrollment_code}
                                                </td>
                                                <td className="py-2 pr-2">
                                                    {p.concept ?? '—'}
                                                </td>
                                                <td className="py-2 pr-2 font-mono">
                                                    S/{' '}
                                                    {Number(p.pending).toFixed(
                                                        2,
                                                    )}
                                                </td>
                                                <td className="py-2">
                                                    {p.status}
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>

                        <h3 className="mt-6 text-sm font-semibold text-navy-900">
                            Pagos recientes
                        </h3>
                        <ul className="mt-2 space-y-1 text-xs text-plomo">
                            {(summary.payments_recent ?? []).slice(0, 8).map(
                                (p) => (
                                    <li key={p.id}>
                                        <span className="font-mono">
                                            {p.payment_code}
                                        </span>{' '}
                                        — S/{' '}
                                        {Number(p.amount).toFixed(2)} —{' '}
                                        {p.payment_concept?.name ?? '—'} —{' '}
                                        {p.status}
                                    </li>
                                ),
                            )}
                        </ul>
                    </Card>
                ) : null}

                <Card>
                    <form onSubmit={submit} className="space-y-6">
                        <div className="grid gap-6 sm:grid-cols-2">
                            <div className="sm:col-span-2">
                                <InputLabel
                                    htmlFor="guardian_id"
                                    value="Apoderado (opcional)"
                                />
                                <select
                                    id="guardian_id"
                                    className={inputClass}
                                    value={form.data.guardian_id}
                                    onChange={(e) =>
                                        form.setData(
                                            'guardian_id',
                                            e.target.value,
                                        )
                                    }
                                    disabled={!preview}
                                >
                                    <option value="">—</option>
                                    {guardianOptions.map((g) => (
                                        <option key={g.value} value={g.value}>
                                            {g.label}
                                        </option>
                                    ))}
                                </select>
                                <InputError
                                    message={form.errors.guardian_id}
                                    className="mt-1"
                                />
                            </div>

                            <div className="sm:col-span-2">
                                <InputLabel
                                    htmlFor="pension_id"
                                    value="Pagar pensión (opcional)"
                                />
                                <select
                                    id="pension_id"
                                    className={inputClass}
                                    value={form.data.pension_id}
                                    onChange={(e) =>
                                        onPensionChange(e.target.value)
                                    }
                                    disabled={!summary}
                                >
                                    <option value="">
                                        Sin vínculo a pensión
                                    </option>
                                    {pensionOptions.map((p) => (
                                        <option
                                            key={p.id}
                                            value={String(p.id)}
                                        >
                                            {p.year}-
                                            {String(p.month).padStart(2, '0')}{' '}
                                            {p.enrollment_code} — pend. S/{' '}
                                            {Number(p.pending).toFixed(2)}
                                        </option>
                                    ))}
                                </select>
                                <InputError
                                    message={form.errors.pension_id}
                                    className="mt-1"
                                />
                            </div>

                            <div>
                                <InputLabel
                                    htmlFor="enrollment_id"
                                    value="Matrícula (opcional)"
                                />
                                <select
                                    id="enrollment_id"
                                    className={inputClass}
                                    value={form.data.enrollment_id}
                                    onChange={(e) =>
                                        form.setData(
                                            'enrollment_id',
                                            e.target.value,
                                        )
                                    }
                                    disabled={!summary}
                                >
                                    <option value="">—</option>
                                    {Array.from(
                                        new Map(
                                            (summary?.pensions ?? []).map(
                                                (p) => [
                                                    p.enrollment_id,
                                                    p,
                                                ],
                                            ),
                                        ).values(),
                                    ).map((p) => (
                                        <option
                                            key={p.enrollment_id}
                                            value={String(p.enrollment_id)}
                                        >
                                            {p.enrollment_code}
                                        </option>
                                    ))}
                                </select>
                                <InputError
                                    message={form.errors.enrollment_id}
                                    className="mt-1"
                                />
                            </div>

                            <div>
                                <InputLabel
                                    htmlFor="payment_concept_id"
                                    value="Concepto *"
                                />
                                <select
                                    id="payment_concept_id"
                                    className={inputClass}
                                    value={form.data.payment_concept_id}
                                    onChange={(e) =>
                                        form.setData(
                                            'payment_concept_id',
                                            e.target.value,
                                        )
                                    }
                                >
                                    {catalog.concepts.map((c) => (
                                        <option key={c.value} value={c.value}>
                                            {c.label}
                                        </option>
                                    ))}
                                </select>
                                <InputError
                                    message={form.errors.payment_concept_id}
                                    className="mt-1"
                                />
                            </div>

                            <div>
                                <InputLabel
                                    htmlFor="amount"
                                    value="Monto (S/) *"
                                />
                                <TextInput
                                    id="amount"
                                    type="number"
                                    min="0.01"
                                    step="0.01"
                                    className={inputClass}
                                    value={form.data.amount}
                                    onChange={(e) =>
                                        form.setData('amount', e.target.value)
                                    }
                                />
                                <InputError
                                    message={form.errors.amount}
                                    className="mt-1"
                                />
                            </div>

                            <div>
                                <InputLabel
                                    htmlFor="payment_method"
                                    value="Método *"
                                />
                                <select
                                    id="payment_method"
                                    className={inputClass}
                                    value={form.data.payment_method}
                                    onChange={(e) =>
                                        form.setData(
                                            'payment_method',
                                            e.target.value,
                                        )
                                    }
                                >
                                    {catalog.methods.map((m) => (
                                        <option key={m.value} value={m.value}>
                                            {m.label}
                                        </option>
                                    ))}
                                </select>
                                <InputError
                                    message={form.errors.payment_method}
                                    className="mt-1"
                                />
                            </div>

                            <div className="sm:col-span-2">
                                <InputLabel
                                    htmlFor="paid_at"
                                    value="Fecha y hora del pago *"
                                />
                                <TextInput
                                    id="paid_at"
                                    type="datetime-local"
                                    className={inputClass}
                                    value={form.data.paid_at}
                                    onChange={(e) =>
                                        form.setData('paid_at', e.target.value)
                                    }
                                />
                                <InputError
                                    message={form.errors.paid_at}
                                    className="mt-1"
                                />
                            </div>

                            <div className="sm:col-span-2">
                                <InputLabel
                                    htmlFor="observations"
                                    value="Observaciones"
                                />
                                <textarea
                                    id="observations"
                                    rows={3}
                                    className={inputClass}
                                    value={form.data.observations}
                                    onChange={(e) =>
                                        form.setData(
                                            'observations',
                                            e.target.value,
                                        )
                                    }
                                />
                                <InputError
                                    message={form.errors.observations}
                                    className="mt-1"
                                />
                            </div>
                        </div>

                        <InputError message={form.errors.student_id} />

                        <div className="flex justify-end gap-3">
                            <PrimaryButton
                                disabled={
                                    form.processing ||
                                    !canSubmit
                                }
                            >
                                Registrar pago
                            </PrimaryButton>
                        </div>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
