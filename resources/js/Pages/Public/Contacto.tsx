import { Clock, Mail, MapPin, Phone } from 'lucide-react';
import { PageHero } from '@/Components/Public/ui/PageHero';
import { InstitutionalCTA } from '@/Components/Public/ui/InstitutionalCTA';
import PublicLayout from '@/Layouts/PublicLayout';

export default function Contacto() {
    return (
        <PublicLayout title="Contacto — I.E.P. Horizonte" description="Ubicación, horarios y formulario.">
            <PageHero
                title="Contacto"
                subtitle="Estamos para atender a familias, postulantes y visitantes."
                breadcrumbs={[{ label: 'Contacto' }]}
            />
            <section className="section-institutional py-16 sm:py-20">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="grid gap-12 lg:grid-cols-2">
                        <div className="space-y-6">
                            <div className="flex gap-4 rounded-2xl border border-slate-200/70 bg-white p-6 dark:border-white/10 dark:bg-slate-900/60">
                                <MapPin className="h-6 w-6 shrink-0 text-institutional-gold" />
                                <div>
                                    <p className="font-semibold text-institutional-blue-900 dark:text-white">
                                        Dirección
                                    </p>
                                    <p className="text-sm text-plomo">Av. Institucional 123, San Isidro, Lima</p>
                                </div>
                            </div>
                            <div className="flex gap-4 rounded-2xl border border-slate-200/70 bg-white p-6 dark:border-white/10 dark:bg-slate-900/60">
                                <Phone className="h-6 w-6 shrink-0 text-institutional-gold" />
                                <div>
                                    <p className="font-semibold text-institutional-blue-900 dark:text-white">
                                        Teléfono
                                    </p>
                                    <p className="text-sm text-plomo">+51 1 234 5678</p>
                                </div>
                            </div>
                            <div className="flex gap-4 rounded-2xl border border-slate-200/70 bg-white p-6 dark:border-white/10 dark:bg-slate-900/60">
                                <Mail className="h-6 w-6 shrink-0 text-institutional-gold" />
                                <div>
                                    <p className="font-semibold text-institutional-blue-900 dark:text-white">
                                        Correo
                                    </p>
                                    <p className="text-sm text-plomo">admision@horizonte.edu.pe</p>
                                </div>
                            </div>
                            <div className="flex gap-4 rounded-2xl border border-slate-200/70 bg-white p-6 dark:border-white/10 dark:bg-slate-900/60">
                                <Clock className="h-6 w-6 shrink-0 text-institutional-gold" />
                                <div>
                                    <p className="font-semibold text-institutional-blue-900 dark:text-white">
                                        Horario
                                    </p>
                                    <p className="text-sm text-plomo">
                                        Lun–vie 7:30–16:30 · Sáb admisión 9:00–13:00
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div className="mb-6 aspect-[4/3] overflow-hidden rounded-2xl border border-slate-200/70 bg-gradient-to-br from-institutional-blue-800 to-institutional-blue-900 flex items-center justify-center text-center text-sm text-white/60">
                                Mapa (Google Maps — demo)
                            </div>
                            <form className="space-y-4 rounded-2xl border border-slate-200/70 bg-institutional-surface-alt p-6 dark:border-white/10">
                                <p className="font-semibold text-institutional-blue-900 dark:text-white">
                                    Solicitar información
                                </p>
                                <input
                                    type="text"
                                    placeholder="Nombre completo"
                                    className="w-full rounded-xl border border-slate-200/80 bg-white px-4 py-3 text-sm dark:border-white/10 dark:bg-slate-900"
                                />
                                <input
                                    type="email"
                                    placeholder="Correo electrónico"
                                    className="w-full rounded-xl border border-slate-200/80 bg-white px-4 py-3 text-sm dark:border-white/10 dark:bg-slate-900"
                                />
                                <textarea
                                    placeholder="Mensaje"
                                    rows={4}
                                    className="w-full rounded-xl border border-slate-200/80 bg-white px-4 py-3 text-sm dark:border-white/10 dark:bg-slate-900"
                                />
                                <button
                                    type="button"
                                    className="w-full rounded-2xl bg-institutional-blue-900 py-3 text-sm font-semibold text-white transition hover:bg-institutional-blue-800"
                                >
                                    Enviar (demo)
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
            <InstitutionalCTA primaryLabel="Admisión 2026" primaryHref={route('public.admision')} />
        </PublicLayout>
    );
}
