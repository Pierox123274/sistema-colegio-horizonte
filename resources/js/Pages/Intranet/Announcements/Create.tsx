import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';

type Props = PageProps<{
    catalog: { priorities: SelectOption[]; audiences: SelectOption[]; users: SelectOption[] };
}>;

export default function AnnouncementsCreate() {
    const { catalog } = usePage<Props>().props;
    const form = useForm({
        title: '',
        content: '',
        priority: 'media',
        audience_type: 'all',
        starts_at: new Date().toISOString().slice(0, 16),
        ends_at: '',
        is_active: true,
        recipient_user_ids: [] as string[],
        attachment: null as File | null,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post(route('intranet.announcements.store'), { forceFormData: true });
    };

    return (
        <IntranetLayout title="Nuevo comunicado">
            <Head title="Nuevo comunicado" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Comunicados', href: route('intranet.announcements.index') },
                        { label: 'Nuevo' },
                    ]}
                />
                <SectionTitle title="Nuevo comunicado" />
                <Card>
                    <form onSubmit={submit} className="grid gap-4">
                        <label className="block text-sm">
                            Título
                            <input
                                className="mt-1 w-full rounded-lg border border-plomo/25 px-3 py-2"
                                value={form.data.title}
                                onChange={(e) => form.setData('title', e.target.value)}
                                required
                            />
                        </label>
                        <label className="block text-sm">
                            Contenido
                            <textarea
                                className="mt-1 w-full rounded-lg border border-plomo/25 px-3 py-2"
                                rows={6}
                                value={form.data.content}
                                onChange={(e) => form.setData('content', e.target.value)}
                                required
                            />
                        </label>
                        <div className="grid gap-4 md:grid-cols-2">
                            <label className="block text-sm">
                                Prioridad
                                <select
                                    className="mt-1 w-full rounded-lg border border-plomo/25 px-3 py-2"
                                    value={form.data.priority}
                                    onChange={(e) => form.setData('priority', e.target.value)}
                                >
                                    {catalog.priorities.map((p) => (
                                        <option key={p.value} value={p.value}>
                                            {p.label}
                                        </option>
                                    ))}
                                </select>
                            </label>
                            <label className="block text-sm">
                                Audiencia
                                <select
                                    className="mt-1 w-full rounded-lg border border-plomo/25 px-3 py-2"
                                    value={form.data.audience_type}
                                    onChange={(e) => form.setData('audience_type', e.target.value)}
                                >
                                    {catalog.audiences.map((a) => (
                                        <option key={a.value} value={a.value}>
                                            {a.label}
                                        </option>
                                    ))}
                                </select>
                            </label>
                        </div>
                        {form.data.audience_type === 'custom_users' && (
                            <label className="block text-sm">
                                Destinatarios (Ctrl+clic)
                                <select
                                    multiple
                                    className="mt-1 h-40 w-full rounded-lg border border-plomo/25 px-3 py-2"
                                    value={form.data.recipient_user_ids}
                                    onChange={(e) =>
                                        form.setData(
                                            'recipient_user_ids',
                                            Array.from(e.target.selectedOptions).map((o) => o.value),
                                        )
                                    }
                                >
                                    {catalog.users.map((u) => (
                                        <option key={u.value} value={u.value}>
                                            {u.label}
                                        </option>
                                    ))}
                                </select>
                            </label>
                        )}
                        <div className="grid gap-4 md:grid-cols-2">
                            <label className="block text-sm">
                                Publicación
                                <input
                                    type="datetime-local"
                                    className="mt-1 w-full rounded-lg border border-plomo/25 px-3 py-2"
                                    value={form.data.starts_at}
                                    onChange={(e) => form.setData('starts_at', e.target.value)}
                                    required
                                />
                            </label>
                            <label className="block text-sm">
                                Expiración (opcional)
                                <input
                                    type="datetime-local"
                                    className="mt-1 w-full rounded-lg border border-plomo/25 px-3 py-2"
                                    value={form.data.ends_at}
                                    onChange={(e) => form.setData('ends_at', e.target.value)}
                                />
                            </label>
                        </div>
                        <label className="block text-sm">
                            Adjunto (PDF o imagen, máx. 5 MB)
                            <input
                                type="file"
                                accept=".pdf,image/*"
                                className="mt-1 w-full text-sm"
                                onChange={(e) =>
                                    form.setData('attachment', e.target.files?.[0] ?? null)
                                }
                            />
                        </label>
                        <label className="flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                checked={form.data.is_active}
                                onChange={(e) => form.setData('is_active', e.target.checked)}
                            />
                            Publicar activo
                        </label>
                        <div className="flex gap-3">
                            <button
                                type="submit"
                                disabled={form.processing}
                                className="rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white"
                            >
                                Guardar comunicado
                            </button>
                            <Link
                                href={route('intranet.announcements.index')}
                                className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold"
                            >
                                Cancelar
                            </Link>
                        </div>
                    </form>
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
