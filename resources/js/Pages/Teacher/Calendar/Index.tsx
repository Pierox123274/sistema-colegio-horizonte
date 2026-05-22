import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import TeacherLayout from '@/Layouts/TeacherLayout';
import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';

type Event = { id: number; title: string; event_type: string; starts_at: string };

type Props = PageProps<{ events: Event[]; view: string }>;

export default function TeacherCalendarIndex() {
    const { events } = usePage<Props>().props;

    return (
        <TeacherLayout title="Calendario">
            <Head title="Calendario académico" />
            <PageContainer>
                <SectionTitle title="Calendario" description="Tareas, exámenes y eventos de sus secciones." />
                <Card>
                    <ul className="divide-y divide-slate-100">
                        {events.length === 0 ? (
                            <li className="py-4 text-sm text-plomo">Sin eventos este mes.</li>
                        ) : (
                            events.map((e) => (
                                <li key={e.id} className="flex justify-between py-3 text-sm">
                                    <span className="font-medium">{e.title}</span>
                                    <span className="text-plomo capitalize">
                                        {e.event_type} — {new Date(e.starts_at).toLocaleDateString()}
                                    </span>
                                </li>
                            ))
                        )}
                    </ul>
                </Card>
            </PageContainer>
        </TeacherLayout>
    );
}
