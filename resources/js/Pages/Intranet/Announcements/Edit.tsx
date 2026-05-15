import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';

type AnnouncementForm = {
    id: number;
    title: string;
    content: string;
    priority: string;
    audience_type: string;
    starts_at: string;
    ends_at: string | null;
    is_active: boolean;
    has_attachment: boolean;
    attachment_url: string | null;
    attachment_original_name: string | null;
    recipients: { id: number }[];
};

type Props = PageProps<{
    announcement: AnnouncementForm;
    catalog: { priorities: SelectOption[]; audiences: SelectOption[]; users: SelectOption[] };
}>;

export default function AnnouncementsEdit() {
    const { announcement, catalog } = usePage<Props>().props;
    const form = useForm({
        title: announcement.title,
        content: announcement.content,
        priority: announcement.priority,
        audience_type: announcement.audience_type,
        starts_at: announcement.starts_at,
        ends_at: announcement.ends_at ?? '',
        is_active: announcement.is_active,
        recipient_user_ids: announcement.recipients.map((r) => String(r.id)),
        attachment: null as File | null,
        remove_attachment: false,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.transform((data) => ({
            ...data,
            ends_at: data.ends_at || null,
        }));
        form.put(route('intranet.announcements.update', announcement.id), {
            forceFormData: true,
        });
    };

    return (
        <IntranetLayout title="Editar comunicado">
            <Head title="Editar comunicado" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Comunicados', href: route('intranet.announcements.index') },
                        {
                            label: announcement.title,
                            href: route('intranet.announcements.show', announcement.id),
                        },
                        { label: 'Editar' },
                    ]}
                />
                <SectionTitle title="Editar comunicado" />
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
                        {announcement.has_attachment && !form.data.remove_attachment && (
                            <div className="rounded-lg border border-plomo/15 bg-navy-50/40 px-3 py-2 text-sm">
                                <p>
                                    Adjunto actual:{' '}
                                    {announcement.attachment_original_name ?? 'archivo'}
                                </p>
                                {announcement.attachment_url && (
                                    <a
                                        href={announcement.attachment_url}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="font-semibold text-navy-900 hover:underline"
                                    >
                                        Ver adjunto
                                    </a>
                                )}
                                <label className="mt-2 flex items-center gap-2">
                                    <input
                                        type="checkbox"
                                        checked={form.data.remove_attachment}
                                        onChange={(e) =>
                                            form.setData('remove_attachment', e.target.checked)
                                        }
                                    />
                                    Quitar adjunto
                                </label>
                            </div>
                        )}
                        <label className="block text-sm">
                            {announcement.has_attachment && !form.data.remove_attachment
                                ? 'Reemplazar adjunto (opcional)'
                                : 'Adjunto (PDF o imagen, máx. 5 MB)'}
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
                                Guardar cambios
                            </button>
                            <Link
                                href={route('intranet.announcements.show', announcement.id)}
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
