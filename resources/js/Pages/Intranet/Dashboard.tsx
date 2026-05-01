import IntranetLayout from '@/Layouts/IntranetLayout';
import { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';

function roleSummary(roles: string[]): string {
    if (roles.includes('Administrador')) {
        return 'Acceso de administración. En fases siguientes verás módulos de configuración, usuarios y auditoría.';
    }
    if (roles.includes('Secretaria')) {
        return 'Acceso orientado a matrículas, pensiones y secretaría (en construcción).';
    }
    if (roles.includes('Docente')) {
        return 'Acceso orientado a cursos y evaluaciones (en construcción).';
    }
    if (roles.includes('Apoderado')) {
        return 'Acceso orientado a pagos y seguimiento del estudiante (en construcción).';
    }
    return 'Acceso de estudiante: progreso y tutor (en construcción).';
}

export default function Dashboard() {
    const { auth } = usePage<PageProps>().props;
    const roles = auth.user?.roles ?? [];

    return (
        <IntranetLayout title="Panel">
            <Head title="Intranet" />

            <div className="mx-auto max-w-5xl space-y-6">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-slate-900">
                        Bienvenido, {auth.user?.name}
                    </h1>
                    <p className="mt-1 text-sm text-slate-600">
                        Panel temporal de la intranet. El diseño final llegará en
                        fases posteriores.
                    </p>
                </div>

                <div className="grid gap-4 sm:grid-cols-2">
                    <div className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 className="text-sm font-semibold uppercase tracking-wide text-slate-500">
                            Tu contexto
                        </h2>
                        <p className="mt-2 text-sm text-slate-700">
                            {roleSummary(roles)}
                        </p>
                    </div>
                    <div className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 className="text-sm font-semibold uppercase tracking-wide text-slate-500">
                            Seguridad
                        </h2>
                        <p className="mt-2 text-sm text-slate-700">
                            Las rutas de esta área exigen autenticación, correo
                            verificado (si aplica) y un rol de intranet
                            asignado.
                        </p>
                    </div>
                </div>
            </div>
        </IntranetLayout>
    );
}
