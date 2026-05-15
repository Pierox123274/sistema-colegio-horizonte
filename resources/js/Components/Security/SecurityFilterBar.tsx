import { Filter, RotateCcw } from 'lucide-react';
import type { FormEvent, ReactNode } from 'react';

type Props = {
    children: ReactNode;
    onSubmit: (e?: FormEvent) => void;
    onClear: () => void;
    columns?: 2 | 3 | 4 | 6;
};

const colClass = {
    2: 'md:grid-cols-2',
    3: 'md:grid-cols-3',
    4: 'md:grid-cols-4',
    6: 'lg:grid-cols-6',
};

export default function SecurityFilterBar({
    children,
    onSubmit,
    onClear,
    columns = 4,
}: Props) {
    return (
        <form
            onSubmit={onSubmit}
            className={`mb-6 grid gap-3 rounded-xl border border-plomo/15 bg-white p-4 shadow-sm ${colClass[columns]}`}
        >
            {children}
            <div className={`flex flex-wrap items-center gap-2 ${columns >= 4 ? 'lg:col-span-6 md:col-span-4' : 'md:col-span-4'}`}>
                <button
                    type="submit"
                    className="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-navy-900 px-4 text-sm font-semibold text-white hover:bg-navy-800"
                >
                    <Filter className="h-4 w-4" aria-hidden />
                    Filtrar
                </button>
                <button
                    type="button"
                    onClick={onClear}
                    className="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-plomo/25 bg-white px-4 text-sm font-semibold text-navy-900 hover:bg-navy-50"
                >
                    <RotateCcw className="h-4 w-4" aria-hidden />
                    Limpiar
                </button>
            </div>
        </form>
    );
}
