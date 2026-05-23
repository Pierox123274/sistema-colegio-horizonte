import { CmsImagePicker } from '@/Components/Intranet/Cms/CmsImagePicker';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEvent } from 'react';

type Props = {
    settings: Record<string, unknown>;
};

function settingValue(settings: Record<string, unknown>, key: string, fallback = ''): string {
    const raw = settings[key];
    if (raw && typeof raw === 'object' && 'value' in (raw as object)) {
        return String((raw as { value?: string }).value ?? fallback);
    }
    if (typeof raw === 'string') {
        return raw;
    }

    return fallback;
}

export default function CmsSettingsIndex({ settings }: Props) {
    const { data, setData, put, processing } = useForm({
        settings: {
            school_name: { value: settingValue(settings, 'school_name', 'I.E.P. Horizonte') },
            school_tagline: { value: settingValue(settings, 'school_tagline') },
            logo_path: { value: settingValue(settings, 'logo_path') },
            favicon_path: { value: settingValue(settings, 'favicon_path') },
            contact: {
                phone: (settings.contact as { phone?: string })?.phone ?? '',
                email: (settings.contact as { email?: string })?.email ?? '',
                address: (settings.contact as { address?: string })?.address ?? '',
            },
        },
    });

    return (
        <IntranetLayout>
            <Head title="CMS — Configuración" />
            <PageContainer>
                <SectionTitle
                    title="Configuración institucional"
                    description="Nombre, logo, contacto y datos globales del sitio."
                />
                <Card className="mt-6 max-w-2xl p-6">
                    <form
                        onSubmit={(e: FormEvent) => {
                            e.preventDefault();
                            put(route('intranet.cms.settings.update'));
                        }}
                        className="space-y-6"
                    >
                        <input
                            value={data.settings.school_name.value}
                            onChange={(e) =>
                                setData('settings', {
                                    ...data.settings,
                                    school_name: { value: e.target.value },
                                })
                            }
                            className="w-full rounded-lg border-slate-300"
                            placeholder="Nombre del colegio"
                        />
                        <textarea
                            value={data.settings.school_tagline.value}
                            onChange={(e) =>
                                setData('settings', {
                                    ...data.settings,
                                    school_tagline: { value: e.target.value },
                                })
                            }
                            rows={2}
                            className="w-full rounded-lg border-slate-300"
                            placeholder="Eslogan"
                        />
                        <CmsImagePicker
                            label="Logo institucional"
                            value={data.settings.logo_path.value}
                            onChange={(path) =>
                                setData('settings', {
                                    ...data.settings,
                                    logo_path: { value: path },
                                })
                            }
                        />
                        <CmsImagePicker
                            label="Favicon"
                            value={data.settings.favicon_path.value}
                            onChange={(path) =>
                                setData('settings', {
                                    ...data.settings,
                                    favicon_path: { value: path },
                                })
                            }
                        />
                        <button
                            type="submit"
                            disabled={processing}
                            className="rounded-lg bg-navy-900 px-6 py-2.5 text-sm font-semibold text-white"
                        >
                            Guardar configuración
                        </button>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
