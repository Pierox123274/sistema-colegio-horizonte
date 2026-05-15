import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';

type RoleRow = { name: string };

type UserRow = {
    id: number;
    name: string;
    email: string;
    is_active: boolean;
    roles: RoleRow[];
};

type Props = PageProps<{
    users: {
        data: UserRow[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: { search: string; role: string };
    catalog: { roles: SelectOption[] };
}>;

export default function AdminUsersIndex() {
    const { users, filters, catalog } = usePage<Props>().props;

    return (
        <IntranetLayout title="Usuarios">
            <Head title="Administración — Usuarios" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[{ label: 'Administración' }, { label: 'Usuarios' }]}
                />
                <SectionTitle
                    title="Usuarios del sistema"
                    description="Alta, roles y estado activo/inactivo (solo administrador)."
                    actions={
                        <Link
                            href={route('intranet.admin.users.create')}
                            className="rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm"
                        >
                            Nuevo usuario
                        </Link>
                    }
                />

                <Card className="mb-4">
                    <form
                        className="flex flex-wrap gap-3"
                        onSubmit={(e) => {
                            e.preventDefault();
                            const fd = new FormData(e.currentTarget);
                            router.get(route('intranet.admin.users.index'), Object.fromEntries(fd), {
                                preserveState: true,
                            });
                        }}
                    >
                        <input
                            name="search"
                            defaultValue={filters.search}
                            placeholder="Nombre o correo"
                            className="min-w-[200px] flex-1 rounded-md border border-plomo/20 px-3 py-2 text-sm"
                        />
                        <select
                            name="role"
                            defaultValue={filters.role}
                            className="rounded-md border border-plomo/20 px-3 py-2 text-sm"
                        >
                            <option value="">Todos los roles</option>
                            {catalog.roles.map((r) => (
                                <option key={r.value} value={r.value}>
                                    {r.label}
                                </option>
                            ))}
                        </select>
                        <button
                            type="submit"
                            className="rounded-lg border border-plomo/20 px-4 py-2 text-sm font-semibold text-navy-900"
                        >
                            Filtrar
                        </button>
                    </form>
                </Card>

                <Card>
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-sm">
                            <thead>
                                <tr className="border-b border-plomo/15 text-left">
                                    <th className="px-3 py-2">Usuario</th>
                                    <th className="px-3 py-2">Correo</th>
                                    <th className="px-3 py-2">Roles</th>
                                    <th className="px-3 py-2">Estado</th>
                                    <th className="px-3 py-2">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                {users.data.map((u) => (
                                    <tr key={u.id} className="border-b border-plomo/10">
                                        <td className="px-3 py-2 font-medium text-navy-900">
                                            {u.name}
                                        </td>
                                        <td className="px-3 py-2 text-plomo">{u.email}</td>
                                        <td className="px-3 py-2">
                                            {u.roles?.length
                                                ? u.roles.map((r) => r.name).join(', ')
                                                : '—'}
                                        </td>
                                        <td className="px-3 py-2">
                                            <span
                                                className={`rounded-full px-2 py-1 text-xs font-semibold ${
                                                    u.is_active
                                                        ? 'bg-emerald-100 text-emerald-700'
                                                        : 'bg-red-100 text-red-700'
                                                }`}
                                            >
                                                {u.is_active ? 'Activo' : 'Inactivo'}
                                            </span>
                                        </td>
                                        <td className="px-3 py-2">
                                            <Link
                                                href={route('intranet.admin.users.edit', u.id)}
                                                className="text-xs font-semibold text-navy-900 underline"
                                            >
                                                Editar
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                    {users.links && users.links.length > 3 ? (
                        <nav className="mt-4 flex flex-wrap justify-center gap-1">
                            {users.links.map((link, i) =>
                                link.url ? (
                                    <button
                                        key={i}
                                        type="button"
                                        className={`rounded-md px-3 py-1.5 text-sm ${
                                            link.active
                                                ? 'bg-navy-900 font-semibold text-white'
                                                : 'border border-plomo/20 bg-white'
                                        }`}
                                        onClick={() => router.visit(link.url!)}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ) : (
                                    <span
                                        key={i}
                                        className="px-3 py-1.5 text-sm text-plomo"
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ),
                            )}
                        </nav>
                    ) : null}
                </Card>
            </PageContainer>
        </IntranetLayout>
    );
}
