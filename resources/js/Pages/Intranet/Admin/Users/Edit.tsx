import { Card } from '@/Components/Intranet/Card';
import { IntranetBreadcrumbs } from '@/Components/Intranet/IntranetBreadcrumbs';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import PrimaryButton from '@/Components/PrimaryButton';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler } from 'react';

type Props = PageProps<{
    user: { id: number; name: string; email: string; is_active: boolean; role: string };
    catalog: { roles: SelectOption[] };
}>;

export default function AdminUsersEdit() {
    const { user, catalog } = usePage<Props>().props;

    const form = useForm({
        name: user.name,
        email: user.email,
        password: '',
        password_confirmation: '',
        role: user.role,
        is_active: user.is_active,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        form.put(route('intranet.admin.users.update', user.id));
    };

    return (
        <IntranetLayout title="Editar usuario">
            <Head title="Administración — Editar usuario" />
            <PageContainer>
                <IntranetBreadcrumbs
                    items={[
                        { label: 'Administración' },
                        { label: 'Usuarios', href: route('intranet.admin.users.index') },
                        { label: 'Editar' },
                    ]}
                />
                <SectionTitle
                    title="Editar usuario"
                    description="Actualice datos, rol o estado. Deje la contraseña en blanco para no cambiarla."
                    actions={
                        <Link
                            href={route('intranet.admin.users.index')}
                            className="text-sm font-semibold text-navy-900 underline"
                        >
                            Volver
                        </Link>
                    }
                />

                <Card>
                    <form onSubmit={submit} className="grid max-w-xl gap-4">
                        <div>
                            <label className="text-xs font-semibold uppercase text-plomo">
                                Nombre
                            </label>
                            <input
                                value={form.data.name}
                                onChange={(e) => form.setData('name', e.target.value)}
                                className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            />
                            {form.errors.name ? (
                                <p className="mt-1 text-xs text-red-600">{form.errors.name}</p>
                            ) : null}
                        </div>
                        <div>
                            <label className="text-xs font-semibold uppercase text-plomo">
                                Correo
                            </label>
                            <input
                                type="email"
                                value={form.data.email}
                                onChange={(e) => form.setData('email', e.target.value)}
                                className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            />
                            {form.errors.email ? (
                                <p className="mt-1 text-xs text-red-600">{form.errors.email}</p>
                            ) : null}
                        </div>
                        <div>
                            <label className="text-xs font-semibold uppercase text-plomo">
                                Nueva contraseña (opcional)
                            </label>
                            <input
                                type="password"
                                value={form.data.password}
                                onChange={(e) => form.setData('password', e.target.value)}
                                className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            />
                            {form.errors.password ? (
                                <p className="mt-1 text-xs text-red-600">{form.errors.password}</p>
                            ) : null}
                        </div>
                        <div>
                            <label className="text-xs font-semibold uppercase text-plomo">
                                Confirmar contraseña
                            </label>
                            <input
                                type="password"
                                value={form.data.password_confirmation}
                                onChange={(e) =>
                                    form.setData('password_confirmation', e.target.value)
                                }
                                className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            />
                        </div>
                        <div>
                            <label className="text-xs font-semibold uppercase text-plomo">
                                Rol
                            </label>
                            <select
                                value={form.data.role}
                                onChange={(e) => form.setData('role', e.target.value)}
                                className="mt-1 w-full rounded-md border border-plomo/20 px-3 py-2 text-sm"
                            >
                                {catalog.roles.map((r) => (
                                    <option key={r.value} value={r.value}>
                                        {r.label}
                                    </option>
                                ))}
                            </select>
                            {form.errors.role ? (
                                <p className="mt-1 text-xs text-red-600">{form.errors.role}</p>
                            ) : null}
                        </div>
                        <label className="flex items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                checked={form.data.is_active}
                                onChange={(e) => form.setData('is_active', e.target.checked)}
                            />
                            Usuario activo
                        </label>
                        {form.errors.is_active ? (
                            <p className="text-xs text-red-600">{form.errors.is_active}</p>
                        ) : null}

                        <div className="flex gap-3 pt-2">
                            <PrimaryButton disabled={form.processing}>Guardar cambios</PrimaryButton>
                            <Link
                                href={route('intranet.admin.users.index')}
                                className="py-2 text-sm text-plomo hover:text-navy-900"
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
