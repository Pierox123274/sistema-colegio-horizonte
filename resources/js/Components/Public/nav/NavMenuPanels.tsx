import { Link } from '@inertiajs/react';
import { ArrowRight, ChevronRight } from 'lucide-react';
import {
    admisionMenu,
    admissionTimeline,
    navHref,
    nosotrosMenu,
    nivelesMenu,
    portalMenu,
    type NavRouteItem,
    vidaEscolarMenu,
} from './publicNavConfig';
import { useNavDropdownStyles } from './NavDropdownStylesContext';

export function NosotrosMenuPanel() {
    const s = useNavDropdownStyles();
    return (
        <ul className={`grid w-[32rem] max-w-[90vw] grid-cols-2 gap-1.5 ${s.panelInner}`}>
            {nosotrosMenu.map((item) => (
                <NavMenuLink key={item.routeName} item={item} />
            ))}
        </ul>
    );
}

function NavMenuLink({ item }: { item: NavRouteItem }) {
    const s = useNavDropdownStyles();
    const Icon = item.icon;
    return (
        <li>
            <Link href={navHref(item)} className={s.itemLink}>
                <span className={s.iconWrap}>
                    <Icon className="h-5 w-5" />
                </span>
                <span className="min-w-0 flex-1">
                    <span className={`flex items-center justify-between gap-2 ${s.itemTitle}`}>
                        {item.label}
                        <ChevronRight className={s.chevron} />
                    </span>
                    {item.description ? (
                        <span className={s.itemDesc}>{item.description}</span>
                    ) : null}
                </span>
            </Link>
        </li>
    );
}

export function NivelesMenuPanel() {
    const s = useNavDropdownStyles();
    return (
        <div className={`grid w-[42rem] max-w-[92vw] gap-3 sm:grid-cols-3 ${s.panelInner}`}>
            {nivelesMenu.map((level) => {
                const Icon = level.icon;
                return (
                    <Link key={level.key} href={navHref(level)} className={s.nivelCard}>
                        <span className={s.iconWrap}>
                            <Icon className="h-5 w-5" />
                        </span>
                        <h4 className={s.nivelCardTitle}>{level.label}</h4>
                        <p className={s.nivelCardGrades}>{level.grades}</p>
                        <p className={`mt-2 flex-1 ${s.itemDesc}`}>
                            {level.description}
                        </p>
                        <span className={s.nivelCardCta}>
                            Conocer más
                            <ArrowRight className="h-3.5 w-3.5" />
                        </span>
                    </Link>
                );
            })}
        </div>
    );
}

export function AdmisionMenuPanel() {
    const s = useNavDropdownStyles();
    return (
        <div className={`grid w-[36rem] max-w-[92vw] md:grid-cols-5 ${s.panelInner}`}>
            <div
                className={`space-y-1 border-b pb-2 md:col-span-2 md:border-b-0 md:border-r md:pr-2 ${s.divider}`}
            >
                {admisionMenu.map((item) => (
                    <NavMenuLink key={item.routeName} item={item} />
                ))}
            </div>
            <div className="pt-1 md:col-span-3 md:pl-2">
                <p className={s.sectionLabel}>Cronograma 2026</p>
                <ul className="relative mt-4 space-y-1">
                    {admissionTimeline.map((step, i) => (
                        <li key={step.title} className={s.timelineItem}>
                            {i < admissionTimeline.length - 1 ? (
                                <span
                                    className="absolute left-[1.35rem] top-10 h-[calc(100%-0.75rem)] w-px bg-yellow-400/40"
                                    aria-hidden
                                />
                            ) : null}
                            <span className={s.timelineBadge}>{step.date}</span>
                            <div>
                                <p className={s.timelineTitle}>{step.title}</p>
                                <p className={s.itemDesc}>
                                    {step.desc}
                                </p>
                            </div>
                        </li>
                    ))}
                </ul>
            </div>
        </div>
    );
}

export function VidaEscolarMenuPanel() {
    const s = useNavDropdownStyles();
    return (
        <div className={`grid w-[28rem] max-w-[92vw] md:grid-cols-2 ${s.panelInner}`}>
            <ul className="space-y-1">
                {vidaEscolarMenu.map((item) => (
                    <NavMenuLink key={item.routeName} item={item} />
                ))}
            </ul>
            <div className={`hidden gap-2 md:grid md:grid-cols-2 md:border-l md:pl-3 ${s.divider}`}>
                {s.galleryTile.map((tone, i) => (
                    <div
                        key={i}
                        className={`rounded-xl ${tone} min-h-[4.5rem] ${i === 0 ? 'col-span-2 min-h-[5rem]' : ''}`}
                        aria-hidden
                    />
                ))}
            </div>
        </div>
    );
}

export function PortalMenuPanel() {
    const s = useNavDropdownStyles();
    return (
        <ul className={`w-64 space-y-1 ${s.panelInner}`}>
            {portalMenu.map((item) => {
                const Icon = item.icon;
                return (
                    <li key={item.label}>
                        <Link href={navHref(item)} className={s.itemLink}>
                            <span className={s.iconWrapSm}>
                                <Icon className="h-4 w-4" />
                            </span>
                            <span className={s.itemTitle}>{item.label}</span>
                        </Link>
                    </li>
                );
            })}
        </ul>
    );
}
