import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEvent, useMemo } from 'react';

type Section = {
    id: number;
    section_key: string;
    title: string | null;
    payload: Record<string, unknown>;
    is_active: boolean;
    sort_order: number;
};

type SectionFormRow = {
    section_key: string;
    title: string;
    payload_json: string;
    is_active: boolean;
    sort_order: number;
};

type Props = { sections: Section[] };

export default function CmsHomepageEdit({ sections }: Props) {
    const initial = useMemo<SectionFormRow[]>(
        () =>
            sections.map((s) => ({
                section_key: s.section_key,
                title: s.title ?? '',
                payload_json: JSON.stringify(s.payload, null, 2),
                is_active: s.is_active,
                sort_order: s.sort_order,
            })),
        [sections],
    );

    const { data, setData, put, processing, transform } = useForm<{ sections: SectionFormRow[] }>({
        sections: initial,
    });

    transform((form) => ({
        sections: form.sections.map((row) => ({
            section_key: row.section_key,
            title: row.title,
            payload: JSON.parse(row.payload_json) as Record<string, unknown>,
            is_active: row.is_active,
            sort_order: row.sort_order,
        })),
    }));

    return (
        <IntranetLayout>
            <Head title="CMS — Homepage" />
            <PageContainer>
                <SectionTitle
                    title="Página de inicio"
                    description="Secciones dinámicas: estadísticas, teasers y CTAs."
                />
                <form
                    onSubmit={(e: FormEvent) => {
                        e.preventDefault();
                        put(route('intranet.cms.homepage.update'));
                    }}
                    className="mt-6 space-y-6"
                >
                    {data.sections.map((section, index) => (
                        <Card key={section.section_key} className="p-6">
                            <h3 className="font-semibold text-navy-900">
                                {section.section_key}
                            </h3>
                            <input
                                value={section.title}
                                onChange={(e) => {
                                    const next = [...data.sections];
                                    next[index] = { ...next[index], title: e.target.value };
                                    setData('sections', next);
                                }}
                                className="mt-3 w-full rounded-lg border-slate-300 text-sm"
                                placeholder="Título interno"
                            />
                            <textarea
                                value={section.payload_json}
                                onChange={(e) => {
                                    const next = [...data.sections];
                                    next[index] = {
                                        ...next[index],
                                        payload_json: e.target.value,
                                    };
                                    setData('sections', next);
                                }}
                                className="mt-3 w-full rounded-lg border-slate-300 font-mono text-xs"
                                rows={8}
                            />
                            <label className="mt-3 flex items-center gap-2 text-sm">
                                <input
                                    type="checkbox"
                                    checked={section.is_active}
                                    onChange={(e) => {
                                        const next = [...data.sections];
                                        next[index] = {
                                            ...next[index],
                                            is_active: e.target.checked,
                                        };
                                        setData('sections', next);
                                    }}
                                />
                                Activa
                            </label>
                        </Card>
                    ))}
                    <button
                        type="submit"
                        disabled={processing}
                        className="rounded-lg bg-navy-900 px-6 py-2.5 text-sm font-semibold text-white"
                    >
                        Guardar inicio
                    </button>
                </form>
            </PageContainer>
        </IntranetLayout>
    );
}
