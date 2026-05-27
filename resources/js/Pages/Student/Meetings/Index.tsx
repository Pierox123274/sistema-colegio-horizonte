import { AppCard } from '@/Components/App/AppCard';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import MeetingCard, { type MeetingCardData } from '@/Components/Meetings/MeetingCard';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import StudentLayout from '@/Layouts/StudentLayout';
import type { PageProps } from '@/types';
import { Head } from '@inertiajs/react';

type Props = PageProps<{
    upcoming: MeetingCardData[];
    history: MeetingCardData[];
}>;

export default function StudentMeetingsIndex({ upcoming, history }: Props) {
    return (
        <StudentLayout title="Videoclases">
            <Head title="Videoclases — Estudiante" />
            <PageContainer>
                <AppPageHeader
                    title="Mis videoclases"
                    description="Clases virtuales y sesiones académicas programadas por tus docentes."
                    eyebrow="Aula virtual"
                />

                <section className="mb-8">
                    <h2 className="mb-3 text-sm font-bold uppercase tracking-wider text-navy-900 dark:text-slate-100">
                        Próximas
                    </h2>
                    {upcoming.length === 0 ? (
                        <AppCard>
                            <p className="text-sm text-plomo dark:text-slate-400">
                                No tienes videoclases próximas.
                            </p>
                        </AppCard>
                    ) : (
                        <div className="grid gap-4 sm:grid-cols-2">
                            {upcoming.map((m) => (
                                <MeetingCard key={m.id} meeting={m} />
                            ))}
                        </div>
                    )}
                </section>

                {history.length > 0 ? (
                    <section>
                        <h2 className="mb-3 text-sm font-bold uppercase tracking-wider text-navy-900 dark:text-slate-100">
                            Historial
                        </h2>
                        <div className="grid gap-4 sm:grid-cols-2">
                            {history.map((m) => (
                                <MeetingCard key={m.id} meeting={m} />
                            ))}
                        </div>
                    </section>
                ) : null}
            </PageContainer>
        </StudentLayout>
    );
}
