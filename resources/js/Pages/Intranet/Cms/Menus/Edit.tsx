import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

type MenuItem = {
    id: number;
    label: string;
    url: string | null;
    route_name: string | null;
    sort_order: number;
    is_active: boolean;
    children?: MenuItem[];
};

type Menu = { id: number; name: string; location: string; items: MenuItem[] };

type Props = { menu: Menu; location: string };

function flattenItems(items: MenuItem[]): MenuItem[] {
    return items.flatMap((item) => [item, ...(item.children ? flattenItems(item.children) : [])]);
}

export default function CmsMenusEdit({ menu, location }: Props) {
    const flat = flattenItems(menu.items);
    const { data, setData, put, processing } = useForm({
        items: flat.map((item) => ({
            label: item.label,
            url: item.url ?? '',
            route_name: item.route_name ?? '',
            route_params: {},
            target: '_self',
            sort_order: item.sort_order,
            is_active: item.is_active,
            children: [],
        })),
    });

    return (
        <IntranetLayout>
            <Head title={`Menú — ${menu.name}`} />
            <PageContainer>
                <SectionTitle title={menu.name} description={location} />
                <Card className="mt-6 max-w-2xl p-6">
                    <form
                        onSubmit={(e: FormEvent) => {
                            e.preventDefault();
                            put(route('intranet.cms.menus.update', menu.id));
                        }}
                        className="space-y-6"
                    >
                        {data.items.map((item, index) => (
                            <div key={index} className="rounded-lg border border-slate-200 p-4">
                                <input
                                    value={item.label}
                                    onChange={(e) => {
                                        const next = [...data.items];
                                        next[index] = { ...next[index], label: e.target.value };
                                        setData('items', next);
                                    }}
                                    className="w-full rounded-lg border-slate-300 text-sm"
                                    placeholder="Etiqueta"
                                />
                                <input
                                    value={item.url}
                                    onChange={(e) => {
                                        const next = [...data.items];
                                        next[index] = { ...next[index], url: e.target.value };
                                        setData('items', next);
                                    }}
                                    className="mt-2 w-full rounded-lg border-slate-300 font-mono text-sm"
                                    placeholder="URL"
                                />
                            </div>
                        ))}
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-lg bg-navy-900 px-6 py-2.5 text-sm font-semibold text-white"
                        >
                            Guardar menú
                        </button>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
