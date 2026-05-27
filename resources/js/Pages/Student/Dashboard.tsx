import { AppCard } from '@/Components/App/AppCard';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import { AppStatCard } from '@/Components/App/AppStatCard';
import StudentPortalEmpty from '@/Components/Student/StudentPortalEmpty';
import RecentAnnouncementsPanel from '@/Components/Announcements/RecentAnnouncementsPanel';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import StudentLayout from '@/Layouts/StudentLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import {
    ArrowRight,
    CalendarCheck,
    ClipboardCheck,
    GraduationCap,
    Wallet,
} from 'lucide-react';

type DashboardProps = PageProps<{
    academic_year: { id: number; name: string; year: number; is_active: boolean } | null;
    student: {
        id: number;
        code: string;
        full_name: string;
        document_number: string | null;
    } | null;
    enrollment: {
        enrollment_code: string;
        academic_year: { name: string; year: number } | null;
        grade: { name: string } | null;
        section: { name: string } | null;
    } | null;
    stats: {
        grade_records_count: number;
        attendance_records_count: number;
        payments_count: number;
        recent_grades: Array<{
            id: number;
            score: string;
            subject: string | null;
            evaluation: string | null;
            period: string | null;
        }>;
    };
    academic_history: Array<{
        id: number;
        enrollment_code: string;
        status: string;
        academic_year: { name: string; year: number } | null;
        grade: { name: string } | null;
        section: { name: string } | null;
    }>;
    has_student: boolean;
    portal_scoped: boolean;
    empty_message: string;
    links: Record<string, string>;
    lms: {
        classrooms_count: number;
        pending_assignments: number;
        upcoming_exams: number;
    };
}>;

export default function StudentDashboard() {
    const {
        academic_year,
        student,
        enrollment,
        stats,
        academic_history,
        has_student,
        portal_scoped,
        empty_message,
        links,
        lms,
    } = usePage<DashboardProps>().props;

    const tools = [
        { href: route('student.classrooms.index'), label: 'Aula virtual', desc: 'Tareas y evaluaciones', icon: GraduationCap },
        { href: route('student.calendar.index'), label: 'Calendario', desc: 'Próximas fechas', icon: CalendarCheck },
        { href: links.grades, label: 'Mis notas', desc: 'Calificaciones y promedios', icon: ClipboardCheck },
        { href: links.attendance, label: 'Mi asistencia', desc: 'Historial y porcentaje', icon: CalendarCheck },
        { href: links.payments, label: 'Mis pagos', desc: 'Pensiones y comprobantes', icon: Wallet },
        { href: links.profile, label: 'Mi perfil', desc: 'Datos personales y matrícula', icon: GraduationCap },
    ];

    return (
        <StudentLayout title="Inicio">
            <Head title="Portal estudiante" />
            <PageContainer>
                <AppPageHeader
                    title={student ? `Hola, ${student.full_name}` : 'Portal estudiante'}
                    description={
                        academic_year
                            ? `Año académico ${academic_year.name} (${academic_year.year})`
                            : 'Consulta tu información académica y financiera.'
                    }
                    eyebrow="Portal estudiante"
                />

                {!has_student ? (
                    <StudentPortalEmpty message={empty_message} portalScoped={portal_scoped} />
                ) : (
                    <>
                        <RecentAnnouncementsPanel />

                        {enrollment && (
                            <AppCard className="mb-6 border-l-4 border-l-brand-yellow">
                                <p className="text-xs font-semibold uppercase tracking-wide text-plomo">
                                    Matrícula actual
                                </p>
                                <p className="mt-1 text-lg font-bold text-navy-900">
                                    {enrollment.grade?.name ?? '—'} — Sección {enrollment.section?.name ?? '—'}
                                </p>
                                <p className="text-sm text-plomo">
                                    Código {enrollment.enrollment_code} · {student?.code}
                                </p>
                            </AppCard>
                        )}

                        <div className="mb-8 grid gap-4 sm:grid-cols-3">
                            <AppStatCard
                                title="Registros de notas"
                                value={String(stats.grade_records_count)}
                                icon={ClipboardCheck}
                                accent="navy"
                            />
                            <AppStatCard
                                title="Registros de asistencia"
                                value={String(stats.attendance_records_count)}
                                icon={CalendarCheck}
                                accent="yellow"
                            />
                            <AppStatCard
                                title="Pagos registrados"
                                value={String(stats.payments_count)}
                                icon={Wallet}
                                accent="red"
                            />
                        </div>

                        {lms && lms.classrooms_count > 0 ? (
                            <div className="mb-8 grid gap-4 sm:grid-cols-3">
                                <AppStatCard
                                    title="Aulas virtuales"
                                    value={String(lms.classrooms_count)}
                                    icon={GraduationCap}
                                    accent="navy"
                                />
                                <AppStatCard
                                    title="Tareas pendientes"
                                    value={String(lms.pending_assignments)}
                                    icon={ClipboardCheck}
                                    accent="yellow"
                                />
                                <AppStatCard
                                    title="Evaluaciones"
                                    value={String(lms.upcoming_exams)}
                                    icon={CalendarCheck}
                                    accent="red"
                                />
                            </div>
                        ) : null}

                        <div className="mb-8 grid gap-4 md:grid-cols-2">
                            <AppCard>
                                <h3 className="text-sm font-semibold text-navy-900">Accesos rápidos</h3>
                                <ul className="mt-4 space-y-3">
                                    {tools.map((tool) => (
                                        <li key={tool.href}>
                                            <Link
                                                href={tool.href}
                                                className="flex items-center justify-between rounded-lg border border-plomo/15 px-4 py-3 transition hover:border-navy-200 hover:bg-navy-50/50"
                                            >
                                                <span>
                                                    <span className="block font-semibold text-navy-900">
                                                        {tool.label}
                                                    </span>
                                                    <span className="text-xs text-plomo">{tool.desc}</span>
                                                </span>
                                                <ArrowRight className="h-4 w-4 text-plomo" />
                                            </Link>
                                        </li>
                                    ))}
                                </ul>
                            </AppCard>

                            <AppCard>
                                <h3 className="text-sm font-semibold text-navy-900">Últimas notas</h3>
                                {stats.recent_grades.length === 0 ? (
                                    <p className="mt-4 text-sm text-plomo">Aún no hay calificaciones registradas.</p>
                                ) : (
                                    <ul className="mt-4 divide-y divide-plomo/10">
                                        {stats.recent_grades.map((row) => (
                                            <li key={row.id} className="flex justify-between py-2 text-sm">
                                                <span>
                                                    <span className="font-medium text-navy-900">
                                                        {row.subject ?? 'Curso'}
                                                    </span>
                                                    <span className="block text-xs text-plomo">
                                                        {row.evaluation ?? '—'} · {row.period ?? '—'}
                                                    </span>
                                                </span>
                                                <span className="font-bold text-navy-900">{row.score}</span>
                                            </li>
                                        ))}
                                    </ul>
                                )}
                            </AppCard>
                        </div>

                        {academic_history.length > 0 && (
                            <AppCard>
                                <h3 className="text-sm font-semibold text-navy-900">Historial académico</h3>
                                <div className="mt-4 overflow-x-auto">
                                    <table className="min-w-full text-sm">
                                        <thead>
                                            <tr className="border-b border-plomo/15 text-left text-xs uppercase text-plomo">
                                                <th className="px-3 py-2">Año</th>
                                                <th className="px-3 py-2">Grado</th>
                                                <th className="px-3 py-2">Sección</th>
                                                <th className="px-3 py-2">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {academic_history.map((row) => (
                                                <tr key={row.id} className="border-b border-plomo/10">
                                                    <td className="px-3 py-2">
                                                        {row.academic_year?.name ?? '—'} ({row.academic_year?.year ?? '—'})
                                                    </td>
                                                    <td className="px-3 py-2">{row.grade?.name ?? '—'}</td>
                                                    <td className="px-3 py-2">{row.section?.name ?? '—'}</td>
                                                    <td className="px-3 py-2 capitalize">{row.status}</td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </AppCard>
                        )}
                    </>
                )}
            </PageContainer>
        </StudentLayout>
    );
}
