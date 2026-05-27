import { AppCard } from '@/Components/App/AppCard';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import MeetingCard, { type MeetingCardData } from '@/Components/Meetings/MeetingCard';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';

type Props = PageProps<{
    upcoming: MeetingCardData[];
    history: MeetingCardData[];
    can_create: boolean;
}>;

export default function TeacherMeetingsIndex({ upcoming, history, can_create }: Props) {
    return (
        <TeacherLayout title="Videoclases">
            <Head title="Videoclases — Docente" />
            <PageContainer>
                <AppPageHeader
                    title="Videoclases y reuniones"
                    description="Clases virtuales, tutorías y sesiones académicas con enlaces institucionales seguros."
                    eyebrow="Aula virtual"
                    actions={
                        can_create ? (
                            <Link
                                href={route('teacher.meetings.create')}
                                className="inline-flex min-h-11 items-center gap-2 rounded-xl bg-navy-900 px-4 py-2.5 text-sm font-semibold text-white dark:bg-brand-yellow dark:text-navy-950"
                            >
                                <Plus className="h-4 w-4" />
                                Nueva videoclase
                            </Link>
                        ) : null
                    }
                />

                <section className="mb-8">
                    <h2 className="mb-3 text-sm font-bold uppercase tracking-wider text-navy-900 dark:text-slate-100">
                        Próximas sesiones
                    </h2>
                    {upcoming.length === 0 ? (
                        <AppCard>
                            <p className="text-sm text-plomo dark:text-slate-400">
                                No tienes videoclases programadas.
                            </p>
                        </AppCard>
                    ) : (
                        <div className="grid gap-4 md:grid-cols-2">
                            {upcoming.map((m) => (
                                <MeetingCard key={m.id} meeting={m} />
                            ))}
                        </div>
                    )}
                </section>

                {history.length > 0 ? (
                    <section>
                        <h2 className="mb-3 text-sm font-bold uppercase tracking-wider text-navy-900 dark:text-slate-100">
                            Historial reciente
                        </h2>
                        <div className="grid gap-4 md:grid-cols-2">
                            {history.map((m) => (
                                <MeetingCard key={m.id} meeting={m} />
                            ))}
                        </div>
                    </section>
                ) : null}
            </PageContainer>
        </TeacherLayout>
    );
}
