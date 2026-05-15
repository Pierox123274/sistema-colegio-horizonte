import type { SelectOption } from '@/types';
import { router } from '@inertiajs/react';
import { FormEvent, useState } from 'react';

type Props = {
    filters: { academic_year_id: string; date_from: string; date_to: string };
    catalog: { academic_years: SelectOption[] };
    routeName: string;
    routeParams?: Record<string, string>;
};

export default function AnalyticsFiltersBar({ filters, catalog, routeName, routeParams = {} }: Props) {
    const [academicYearId, setAcademicYearId] = useState(filters.academic_year_id ?? '');
    const [dateFrom, setDateFrom] = useState(filters.date_from ?? '');
    const [dateTo, setDateTo] = useState(filters.date_to ?? '');

    const apply = (e?: FormEvent) => {
        e?.preventDefault();
        router.get(
            route(routeName, routeParams),
            {
                academic_year_id: academicYearId || undefined,
                date_from: dateFrom || undefined,
                date_to: dateTo || undefined,
            },
            { preserveState: true },
        );
    };

    return (
        <form
            onSubmit={apply}
            className="mb-6 grid gap-3 rounded-xl border border-plomo/15 bg-white p-4 shadow-sm md:grid-cols-4"
        >
            <label className="block text-sm md:col-span-2">
                Año académico
                <select
                    className="mt-1 w-full rounded-lg border border-plomo/25 px-3 py-2"
                    value={academicYearId}
                    onChange={(e) => setAcademicYearId(e.target.value)}
                >
                    <option value="">Año activo / todos</option>
                    {catalog.academic_years.map((y) => (
                        <option key={y.value} value={y.value}>
                            {y.label}
                        </option>
                    ))}
                </select>
            </label>
            <label className="block text-sm">
                Desde
                <input
                    type="date"
                    className="mt-1 w-full rounded-lg border border-plomo/25 px-3 py-2"
                    value={dateFrom}
                    onChange={(e) => setDateFrom(e.target.value)}
                />
            </label>
            <label className="block text-sm">
                Hasta
                <input
                    type="date"
                    className="mt-1 w-full rounded-lg border border-plomo/25 px-3 py-2"
                    value={dateTo}
                    onChange={(e) => setDateTo(e.target.value)}
                />
            </label>
            <div className="md:col-span-4">
                <button
                    type="submit"
                    className="rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white"
                >
                    Aplicar filtros
                </button>
            </div>
        </form>
    );
}
