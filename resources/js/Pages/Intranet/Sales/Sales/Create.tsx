import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/react';
import axios from 'axios';
import { Loader2, Search, User } from 'lucide-react';
import { useEffect, useMemo, useRef, useState } from 'react';

type ProductOption = { value: string; label: string; stock: string; price: string };
type P = PageProps<{ catalog: { products: ProductOption[]; methods: Array<{ value: string; label: string }> } }>;
type StudentHit = { id: number; code: string; first_name: string; last_name: string; document_number: string | null };
type StudentPreview = {
    id: number;
    code: string;
    first_name: string;
    last_name: string;
    document_number: string | null;
    educational_level: string | null;
    grade: string | null;
    section: string | null;
    guardians: Array<{ value: string; label: string }>;
};

function localDatetimeInputValue(d = new Date()): string {
    const pad = (n: number) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

export default function SalesCreate() {
    const { catalog } = usePage<P>().props;
    const form = useForm({
        student_id: '',
        guardian_id: '',
        payment_method: catalog.methods[0]?.value ?? 'efectivo',
        sold_at: localDatetimeInputValue(),
        observations: '',
        items: [{ product_id: catalog.products[0]?.value ?? '', quantity: '1', unit_price: catalog.products[0]?.price ?? '0.00' }],
    });
    const [searchText, setSearchText] = useState('');
    const [searchHits, setSearchHits] = useState<StudentHit[]>([]);
    const [searchOpen, setSearchOpen] = useState(false);
    const [searchLoading, setSearchLoading] = useState(false);
    const [searchError, setSearchError] = useState<string | null>(null);
    const [preview, setPreview] = useState<StudentPreview | null>(null);
    const searchWrapRef = useRef<HTMLDivElement>(null);

    const total = form.data.items.reduce((sum, item) => sum + Number(item.quantity || 0) * Number(item.unit_price || 0), 0);
    const canSubmit = useMemo(
        () =>
            form.data.items.length > 0 &&
            form.data.items.every((item) => item.product_id && Number(item.quantity) > 0 && Number(item.unit_price) > 0),
        [form.data.items],
    );

    useEffect(() => {
        const q = searchText.trim();
        if (q.length < 2) {
            setSearchHits([]);
            return;
        }
        setSearchLoading(true);
        const id = window.setTimeout(() => {
            axios
                .get<{ students: StudentHit[] }>(route('intranet.sales.students.search'), { params: { q } })
                .then((res) => {
                    setSearchHits(res.data.students ?? []);
                    setSearchError(null);
                })
                .catch(() => setSearchError('No se pudo buscar estudiantes'))
                .finally(() => setSearchLoading(false));
        }, 320);

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

    const pickStudent = async (hit: StudentHit) => {
        setSearchLoading(true);
        try {
            const res = await axios.get<{ preview: StudentPreview }>(route('intranet.sales.students.preview', hit.id));
            setPreview(res.data.preview);
            form.setData((prev) => ({
                ...prev,
                student_id: String(hit.id),
                guardian_id: '',
            }));
            setSearchText('');
            setSearchHits([]);
            setSearchOpen(false);
        } catch {
            setSearchError('No se pudo cargar el estudiante.');
        } finally {
            setSearchLoading(false);
        }
    };

    return (
        <IntranetLayout title="Nueva venta">
            <Head title="Nueva venta" />
            <PageContainer>
                <SectionTitle
                    title="Nueva venta"
                    description="Registre venta escolar con descuento de stock."
                />
                <Card>
                    <form onSubmit={(e) => { e.preventDefault(); form.post(route('intranet.sales.sales.store')); }} className="space-y-4">
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div className="sm:col-span-2">
                                <InputLabel value="Estudiante (opcional)" />
                                <div className="relative mt-1" ref={searchWrapRef}>
                                    <Search className="pointer-events-none absolute left-3 top-1/2 z-10 h-4 w-4 -translate-y-1/2 text-plomo" />
                                    <input
                                        type="search"
                                        className="box-border block h-11 w-full rounded-md border border-plomo/25 bg-white py-2 pl-10 pr-10 text-sm"
                                        placeholder="Buscar por código, nombres, apellidos o documento (mín. 2)"
                                        value={searchText}
                                        onChange={(e) => {
                                            setSearchText(e.target.value);
                                            setSearchOpen(true);
                                        }}
                                        onFocus={() => setSearchOpen(true)}
                                    />
                                    {searchLoading ? <Loader2 className="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 animate-spin text-plomo" /> : null}
                                    {searchOpen && searchHits.length > 0 ? (
                                        <ul className="absolute z-20 mt-1 max-h-60 w-full overflow-auto rounded-md border border-plomo/20 bg-white py-1 shadow-lg">
                                            {searchHits.map((h) => (
                                                <li key={h.id}>
                                                    <button type="button" className="flex w-full items-start gap-2 px-3 py-2 text-left text-sm hover:bg-navy-50" onClick={() => void pickStudent(h)}>
                                                        <User className="mt-0.5 h-4 w-4 shrink-0 text-plomo" />
                                                        <span>
                                                            <span className="font-medium text-navy-900">{h.first_name} {h.last_name}</span>
                                                            <span className="ml-2 font-mono text-xs text-plomo">{h.code}</span>
                                                        </span>
                                                    </button>
                                                </li>
                                            ))}
                                        </ul>
                                    ) : null}
                                </div>
                                {searchError ? <p className="mt-1 text-sm text-red-600">{searchError}</p> : null}
                                <div className="mt-2 flex gap-2">
                                    <button type="button" className="rounded border px-2 py-1 text-xs" onClick={() => {
                                        setPreview(null);
                                        setSearchText('');
                                        setSearchHits([]);
                                        form.setData((prev) => ({ ...prev, student_id: '', guardian_id: '' }));
                                    }}>
                                        Venta al público (sin estudiante)
                                    </button>
                                </div>
                                <InputError message={form.errors.student_id} className="mt-1" />
                            </div>
                            <div>
                                <InputLabel value="Apoderado (opcional)" />
                                <select
                                    className="mt-1 block w-full rounded-md border-plomo/25"
                                    value={form.data.guardian_id}
                                    onChange={(e) => form.setData('guardian_id', e.target.value)}
                                    disabled={!preview}
                                >
                                    <option value="">—</option>
                                    {(preview?.guardians ?? []).map((g) => (
                                        <option key={g.value} value={g.value}>{g.label}</option>
                                    ))}
                                </select>
                                <InputError message={form.errors.guardian_id} className="mt-1" />
                            </div>
                            <div><InputLabel value="Método de pago" /><select className="mt-1 block w-full rounded-md border-plomo/25" value={form.data.payment_method} onChange={(e) => form.setData('payment_method', e.target.value)}>{catalog.methods.map((m) => <option key={m.value} value={m.value}>{m.label}</option>)}</select></div>
                            <div><InputLabel value="Fecha venta" /><input type="datetime-local" className="mt-1 block w-full rounded-md border-plomo/25" value={form.data.sold_at} onChange={(e) => form.setData('sold_at', e.target.value)} /></div>
                        </div>
                        {preview ? (
                            <Card>
                                <p className="text-xs font-semibold uppercase text-plomo">Estudiante seleccionado</p>
                                <p className="font-semibold text-navy-900">{preview.first_name} {preview.last_name} <span className="font-mono text-xs">({preview.code})</span></p>
                                <p className="text-xs text-plomo">Documento: {preview.document_number ?? '—'} | Nivel/Grado/Sección: {(preview.educational_level ?? '—')} / {(preview.grade ?? '—')} / {(preview.section ?? '—')}</p>
                            </Card>
                        ) : null}
                        <div>
                            <InputLabel value="Productos" />
                            <div className="space-y-2">
                                {form.data.items.map((item, idx) => (
                                    <div key={idx} className="grid gap-2 sm:grid-cols-[1fr_120px_140px_auto]">
                                        <select className="rounded-md border-plomo/25" value={item.product_id} onChange={(e) => {
                                            const p = catalog.products.find((x) => x.value === e.target.value);
                                            form.setData('items', form.data.items.map((row, rowIdx) => rowIdx === idx ? { ...row, product_id: e.target.value, unit_price: p?.price ?? row.unit_price } : row));
                                        }}>
                                            {catalog.products.map((p) => <option key={p.value} value={p.value}>{p.label} | stock {Number(p.stock).toFixed(2)}</option>)}
                                        </select>
                                        <input type="number" min="0.01" step="0.01" className="rounded-md border-plomo/25" value={item.quantity} onChange={(e) => form.setData('items', form.data.items.map((row, rowIdx) => rowIdx === idx ? { ...row, quantity: e.target.value } : row))} />
                                        <input type="number" min="0.01" step="0.01" className="rounded-md border-plomo/25" value={item.unit_price} onChange={(e) => form.setData('items', form.data.items.map((row, rowIdx) => rowIdx === idx ? { ...row, unit_price: e.target.value } : row))} />
                                        <button type="button" className="rounded border px-2" onClick={() => form.setData('items', form.data.items.filter((_, rowIdx) => rowIdx !== idx))}>Quitar</button>
                                    </div>
                                ))}
                            </div>
                            <button type="button" className="mt-2 rounded border px-3 py-1 text-sm" onClick={() => form.setData('items', [...form.data.items, { product_id: catalog.products[0]?.value ?? '', quantity: '1', unit_price: catalog.products[0]?.price ?? '0.00' }])}>Agregar item</button>
                            <InputError message={form.errors.items} className="mt-1" />
                        </div>
                        <p className="text-lg font-semibold">Total: S/ {total.toFixed(2)}</p>
                        {(form.errors as Record<string, string | undefined>).cash_register ? (
                            <InputError message={(form.errors as Record<string, string | undefined>).cash_register} className="mt-1" />
                        ) : null}
                        <PrimaryButton disabled={form.processing || !canSubmit}>Registrar venta</PrimaryButton>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}

