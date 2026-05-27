import { AppCard } from '@/Components/App/AppCard';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, useForm } from '@inertiajs/react';

type Props = PageProps<{
    classrooms: Array<{ id: number; label: string }>;
    catalog: {
        providers: SelectOption[];
        types: SelectOption[];
    };
    google_meet_fallback_available: boolean;
}>;

export default function TeacherMeetingsCreate({
    classrooms,
    catalog,
    google_meet_fallback_available,
}: Props) {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        description: '',
        meeting_type: 'virtual_class',
        provider: 'google_meet',
        scheduled_at: '',
        duration_minutes: 60,
        virtual_classroom_id: '',
        join_url: '',
        waiting_room_enabled: true,
        recording_allowed: false,
        is_recurring: false,
        is_private: true,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('teacher.meetings.store'));
    };

    const linkRequired =
        data.provider === 'manual' ||
        data.provider === '' ||
        data.provider === 'zoom' ||
        data.provider === 'teams' ||
        (data.provider === 'google_meet' && !google_meet_fallback_available);

    return (
        <TeacherLayout title="Nueva videoclase">
            <Head title="Nueva videoclase" />
            <PageContainer width="default">
                <AppPageHeader
                    title="Programar videoclase"
                    description="Cree la reunión en Google Meet, Zoom o Teams y pegue aquí el enlace para sus estudiantes."
                    eyebrow="Videoclases"
                />

                <AppCard>
                    <form onSubmit={submit} className="space-y-5">
                        <div>
                            <InputLabel value="Título" />
                            <TextInput
                                className="mt-1 block w-full"
                                value={data.title}
                                onChange={(e) => setData('title', e.target.value)}
                                required
                            />
                            <InputError message={errors.title} className="mt-1" />
                        </div>

                        <div>
                            <InputLabel value="Descripción" />
                            <textarea
                                className="mt-1 w-full rounded-lg border border-plomo/20 bg-white px-3 py-2 text-sm dark:border-white/15 dark:bg-slate-900 dark:text-slate-100"
                                rows={3}
                                value={data.description}
                                onChange={(e) => setData('description', e.target.value)}
                            />
                        </div>

                        <div className="grid gap-4 sm:grid-cols-2">
                            <div>
                                <InputLabel value="Tipo de reunión" />
                                <select
                                    className="mt-1 w-full rounded-lg border border-plomo/20 bg-white px-3 py-2 text-sm dark:border-white/15 dark:bg-slate-900"
                                    value={data.meeting_type}
                                    onChange={(e) => setData('meeting_type', e.target.value)}
                                >
                                    {catalog.types.map((t) => (
                                        <option key={t.value} value={t.value}>
                                            {t.label}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <InputLabel value="Plataforma (referencia)" />
                                <select
                                    className="mt-1 w-full rounded-lg border border-plomo/20 bg-white px-3 py-2 text-sm dark:border-white/15 dark:bg-slate-900"
                                    value={data.provider}
                                    onChange={(e) => setData('provider', e.target.value)}
                                >
                                    {catalog.providers.map((p) => (
                                        <option key={p.value} value={p.value}>
                                            {p.label}
                                        </option>
                                    ))}
                                </select>
                                <p className="mt-1 text-xs text-plomo dark:text-slate-400">
                                    Solo etiqueta la reunión; el acceso es el enlace que pegue abajo.
                                </p>
                            </div>
                        </div>

                        <div className="rounded-xl border border-brand-yellow/30 bg-brand-yellow/5 p-4 dark:border-brand-yellow/20 dark:bg-brand-yellow/10">
                            <InputLabel value="Enlace de la videoclase" />
                            <p className="mb-2 text-sm text-plomo dark:text-slate-300">
                                Pegue aquí el enlace de la reunión creado en Google Meet, Zoom o Teams.
                            </p>
                            <TextInput
                                type="url"
                                className="mt-1 block w-full"
                                placeholder="https://meet.google.com/xxx-xxxx-xxx"
                                value={data.join_url}
                                onChange={(e) => setData('join_url', e.target.value)}
                                required={linkRequired}
                            />
                            <InputError message={errors.join_url} className="mt-1" />
                            {data.provider === 'google_meet' && google_meet_fallback_available ? (
                                <p className="mt-2 text-xs text-plomo dark:text-slate-400">
                                    Opcional si deja vacío: se usará la sala institucional configurada
                                    (MEETING_GOOGLE_ROOM_CODE).
                                </p>
                            ) : null}
                        </div>

                        <div className="grid gap-4 sm:grid-cols-2">
                            <div>
                                <InputLabel value="Fecha y hora" />
                                <TextInput
                                    type="datetime-local"
                                    className="mt-1 block w-full"
                                    value={data.scheduled_at}
                                    onChange={(e) => setData('scheduled_at', e.target.value)}
                                    required
                                />
                                <InputError message={errors.scheduled_at} className="mt-1" />
                            </div>
                            <div>
                                <InputLabel value="Duración (min)" />
                                <TextInput
                                    type="number"
                                    min={15}
                                    max={480}
                                    className="mt-1 block w-full"
                                    value={data.duration_minutes}
                                    onChange={(e) =>
                                        setData('duration_minutes', Number(e.target.value))
                                    }
                                />
                            </div>
                        </div>

                        <div>
                            <InputLabel value="Aula virtual (opcional)" />
                            <select
                                className="mt-1 w-full rounded-lg border border-plomo/20 bg-white px-3 py-2 text-sm dark:border-white/15 dark:bg-slate-900"
                                value={data.virtual_classroom_id}
                                onChange={(e) => setData('virtual_classroom_id', e.target.value)}
                            >
                                <option value="">Sin aula vinculada</option>
                                {classrooms.map((c) => (
                                    <option key={c.id} value={c.id}>
                                        {c.label}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div className="flex flex-wrap gap-4 text-sm">
                            <label className="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    checked={data.waiting_room_enabled}
                                    onChange={(e) =>
                                        setData('waiting_room_enabled', e.target.checked)
                                    }
                                />
                                Sala de espera
                            </label>
                            <label className="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    checked={data.recording_allowed}
                                    onChange={(e) => setData('recording_allowed', e.target.checked)}
                                />
                                Grabación permitida
                            </label>
                        </div>

                        <PrimaryButton disabled={processing}>Programar videoclase</PrimaryButton>
                    </form>
                </AppCard>
            </PageContainer>
        </TeacherLayout>
    );
}
