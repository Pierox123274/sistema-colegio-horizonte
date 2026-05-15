import StudentPortalEmpty from '@/Components/Student/StudentPortalEmpty';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import StudentLayout from '@/Layouts/StudentLayout';
import type { PageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

type Props = PageProps<{
    profile: Record<string, string | null> | null;
    enrollment: {
        enrollment_code: string;
        grade: { name: string } | null;
        section: { name: string } | null;
        academic_year: { name: string; year: number } | null;
    } | null;
    guardians: Array<{
        id: number;
        full_name: string;
        relationship: string;
        phone: string;
        email: string | null;
        is_primary: boolean;
    }>;
    academic_history: Array<{
        id: number;
        enrollment_code: string;
        status: string;
        academic_year: { name: string; year: number } | null;
        grade: { name: string } | null;
        section: { name: string } | null;
    }>;
    account: { name: string; email: string };
    has_student: boolean;
    portal_scoped: boolean;
    empty_message: string;
    profile_edit_href: string;
}>;

export default function StudentProfileShow() {
    const {
        profile,
        enrollment,
        guardians,
        academic_history,
        account,
        has_student,
        portal_scoped,
        empty_message,
        profile_edit_href,
    } = usePage<Props>().props;

    return (
        <StudentLayout title="Mi perfil">
            <Head title="Mi perfil" />
            <PageContainer>
                <SectionTitle
                    title="Mi perfil"
                    description="Datos personales, matrícula y contactos de apoderados."
                    actions={
                        <Link
                            href={profile_edit_href}
                            className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold"
                        >
                            Cuenta de acceso
                        </Link>
                    }
                />

                {!has_student || !profile ? (
                    <StudentPortalEmpty message={empty_message} portalScoped={portal_scoped} />
                ) : (
                    <div className="grid gap-6 lg:grid-cols-2">
                        <Card>
                            <h3 className="text-sm font-semibold text-navy-900">Datos del estudiante</h3>
                            <dl className="mt-4 space-y-2 text-sm">
                                <div className="flex justify-between gap-4">
                                    <dt className="text-plomo">Código</dt>
                                    <dd className="font-medium text-navy-900">{profile.code}</dd>
                                </div>
                                <div className="flex justify-between gap-4">
                                    <dt className="text-plomo">Documento</dt>
                                    <dd className="font-medium text-navy-900">
                                        {profile.document_type} {profile.document_number ?? '—'}
                                    </dd>
                                </div>
                                <div className="flex justify-between gap-4">
                                    <dt className="text-plomo">Correo</dt>
                                    <dd className="font-medium text-navy-900">{profile.email ?? '—'}</dd>
                                </div>
                                <div className="flex justify-between gap-4">
                                    <dt className="text-plomo">Teléfono</dt>
                                    <dd className="font-medium text-navy-900">{profile.phone ?? '—'}</dd>
                                </div>
                                <div className="flex justify-between gap-4">
                                    <dt className="text-plomo">Dirección</dt>
                                    <dd className="font-medium text-navy-900">{profile.address ?? '—'}</dd>
                                </div>
                            </dl>
                        </Card>

                        <Card>
                            <h3 className="text-sm font-semibold text-navy-900">Cuenta de usuario</h3>
                            <dl className="mt-4 space-y-2 text-sm">
                                <div className="flex justify-between gap-4">
                                    <dt className="text-plomo">Nombre</dt>
                                    <dd className="font-medium text-navy-900">{account.name}</dd>
                                </div>
                                <div className="flex justify-between gap-4">
                                    <dt className="text-plomo">Correo de acceso</dt>
                                    <dd className="font-medium text-navy-900">{account.email}</dd>
                                </div>
                            </dl>
                        </Card>

                        {enrollment && (
                            <Card className="lg:col-span-2">
                                <h3 className="text-sm font-semibold text-navy-900">Matrícula vigente</h3>
                                <p className="mt-2 text-sm text-navy-900">
                                    {enrollment.academic_year?.name} ({enrollment.academic_year?.year}) —{' '}
                                    {enrollment.grade?.name} · Sección {enrollment.section?.name}
                                </p>
                                <p className="text-xs text-plomo">Código {enrollment.enrollment_code}</p>
                            </Card>
                        )}

                        <Card className="lg:col-span-2">
                            <h3 className="text-sm font-semibold text-navy-900">Apoderados</h3>
                            {guardians.length === 0 ? (
                                <p className="mt-3 text-sm text-plomo">Sin apoderados registrados.</p>
                            ) : (
                                <ul className="mt-4 divide-y divide-plomo/10">
                                    {guardians.map((g) => (
                                        <li key={g.id} className="flex flex-wrap justify-between gap-2 py-3 text-sm">
                                            <span>
                                                <span className="font-semibold text-navy-900">{g.full_name}</span>
                                                <span className="block text-xs text-plomo">
                                                    {g.relationship}
                                                    {g.is_primary ? ' · Contacto principal' : ''}
                                                </span>
                                            </span>
                                            <span className="text-plomo">
                                                {g.phone}
                                                {g.email ? ` · ${g.email}` : ''}
                                            </span>
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </Card>

                        {academic_history.length > 0 && (
                            <Card className="lg:col-span-2">
                                <h3 className="text-sm font-semibold text-navy-900">Historial de matrículas</h3>
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
                                                        {row.academic_year?.name} ({row.academic_year?.year})
                                                    </td>
                                                    <td className="px-3 py-2">{row.grade?.name ?? '—'}</td>
                                                    <td className="px-3 py-2">{row.section?.name ?? '—'}</td>
                                                    <td className="px-3 py-2 capitalize">{row.status}</td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </Card>
                        )}
                    </div>
                )}
            </PageContainer>
        </StudentLayout>
    );
}
