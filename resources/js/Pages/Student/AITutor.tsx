import StudentPortalEmpty from '@/Components/Student/StudentPortalEmpty';
import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import { SectionTitle } from '@/Components/Intranet/SectionTitle';
import StudentLayout from '@/Layouts/StudentLayout';
import type { FlashMessages, PageProps } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/react';
import {
    AlertCircle,
    Bot,
    Loader2,
    RotateCcw,
    Send,
    Sparkles,
    User,
} from 'lucide-react';
import type { FormEvent, KeyboardEvent } from 'react';
import { useEffect, useMemo, useRef, useState } from 'react';

const STORAGE_KEY = 'horizonte.student.ai-tutor.thread.v1';

const WELCOME_TEXT =
    'Hola, soy tu tutor inteligente institucional. Puedo ayudarte con tareas, comprensión de temas, hábitos de estudio y recomendaciones académicas.';

type PortalCtx = {
    student: { id: number; full_name: string; code: string } | null;
    has_student: boolean;
    portal_scoped: boolean;
    empty_message: string;
};

type Insight = {
    risk: {
        level: string;
        score: number;
        average: number | null;
        attendance_pct: number | null;
    };
    recommendations: string[];
    ai_summary: string | null;
    generated_at: string;
} | null;

type ChatMessage = {
    id: string;
    role: 'user' | 'assistant';
    content: string;
    at: string;
    meta?: { cached?: boolean; success?: boolean };
};

type AITutorProps = PageProps<{
    portal: PortalCtx;
    insight: Insight;
    ai_enabled: boolean;
    provider: string;
}>;

function welcomeMessage(): ChatMessage {
    return {
        id: 'welcome',
        role: 'assistant',
        content: WELCOME_TEXT,
        at: new Date().toISOString(),
    };
}

function loadThreadFromStorage(): ChatMessage[] {
    if (typeof window === 'undefined') {
        return [welcomeMessage()];
    }
    try {
        const raw = sessionStorage.getItem(STORAGE_KEY);
        if (!raw) {
            return [welcomeMessage()];
        }
        const parsed = JSON.parse(raw) as unknown;
        if (!Array.isArray(parsed) || parsed.length === 0) {
            return [welcomeMessage()];
        }
        const cleaned: ChatMessage[] = parsed
            .filter(
                (m: unknown) =>
                    m &&
                    typeof m === 'object' &&
                    'id' in m &&
                    'role' in m &&
                    'content' in m &&
                    'at' in m &&
                    ((m as ChatMessage).role === 'user' ||
                        (m as ChatMessage).role === 'assistant'),
            )
            .map((m) => m as ChatMessage);
        if (cleaned.length === 0) {
            return [welcomeMessage()];
        }
        const hasWelcome = cleaned.some((m) => m.id === 'welcome');
        if (!hasWelcome) {
            return [welcomeMessage(), ...cleaned];
        }
        return cleaned;
    } catch {
        return [welcomeMessage()];
    }
}

function formatMsgTime(iso: string): string {
    try {
        return new Date(iso).toLocaleString('es-PE', {
            day: 'numeric',
            month: 'short',
            hour: '2-digit',
            minute: '2-digit',
        });
    } catch {
        return '';
    }
}

export default function AITutor() {
    const { portal, insight, ai_enabled, provider } = usePage<AITutorProps>().props;
    const flash = usePage<AITutorProps>().props.flash;
    const firstName = useMemo(() => {
        const full = portal.student?.full_name?.trim();
        if (!full) {
            return 'Estudiante';
        }
        return full.split(/\s+/)[0] ?? 'Estudiante';
    }, [portal.student?.full_name]);

    const [messages, setMessages] = useState<ChatMessage[]>(() => [welcomeMessage()]);
    const [hydrated, setHydrated] = useState(false);
    const [sendError, setSendError] = useState<string | null>(null);
    const pendingUserIdRef = useRef<string | null>(null);
    const endRef = useRef<HTMLDivElement | null>(null);

    const { data, setData, post, processing, errors, reset, clearErrors } = useForm({
        message: '',
    });

    useEffect(() => {
        setMessages(loadThreadFromStorage());
        setHydrated(true);
    }, []);

    useEffect(() => {
        if (!hydrated || typeof window === 'undefined') {
            return;
        }
        try {
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(messages));
        } catch {
            /* ignore quota */
        }
    }, [messages, hydrated]);

    useEffect(() => {
        endRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages, processing]);

    useEffect(() => {
        if (flash?.error) {
            setSendError(String(flash.error));
        }
    }, [flash?.error]);

    const clearConversation = () => {
        const next = [welcomeMessage()];
        setMessages(next);
        try {
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(next));
        } catch {
            /* ignore */
        }
        setSendError(null);
        clearErrors();
    };

    const sendToTutor = () => {
        const text = data.message.trim();
        if (!text || processing || !ai_enabled) {
            return;
        }

        setSendError(null);
        clearErrors();

        const uid = crypto.randomUUID();
        pendingUserIdRef.current = uid;
        setMessages((m) => [
            ...m,
            {
                id: uid,
                role: 'user',
                content: text,
                at: new Date().toISOString(),
            },
        ]);

        post(route('student.ai-tutor.message'), {
            preserveScroll: true,
            onSuccess: (page) => {
                pendingUserIdRef.current = null;
                const f = page.props.flash as FlashMessages | undefined;
                const reply = f?.ai?.ai_reply;
                const meta = f?.ai?.ai_meta;
                if (reply != null && reply !== '') {
                    setMessages((m) => [
                        ...m,
                        {
                            id: crypto.randomUUID(),
                            role: 'assistant',
                            content: reply,
                            at: new Date().toISOString(),
                            meta: {
                                cached: meta?.cached,
                                success: meta?.success,
                            },
                        },
                    ]);
                }
                reset('message');
            },
            onError: () => {
                const drop = pendingUserIdRef.current;
                pendingUserIdRef.current = null;
                if (drop) {
                    setMessages((m) => m.filter((x) => x.id !== drop));
                }
            },
        });
    };

    const onSubmit = (e: FormEvent) => {
        e.preventDefault();
        sendToTutor();
    };

    const onKeyDown = (e: KeyboardEvent<HTMLTextAreaElement>) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendToTutor();
        }
    };

    const inputDisabled = processing || !ai_enabled;
    const canSend = ai_enabled && !processing && data.message.trim().length > 0;

    return (
        <StudentLayout title="Tutor IA">
            <Head title="Tutor IA" />
            <PageContainer className="flex min-h-0 flex-col pb-2 lg:pb-4">
                <div className="flex shrink-0 flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <SectionTitle
                        title="Tutor inteligente"
                        description={
                            ai_enabled
                                ? `Asistente académico (proveedor: ${provider}). No sustituye a tu docente ni orientación personal.`
                                : 'El tutor IA está deshabilitado en esta instalación. Puedes revisar recomendaciones basadas en reglas.'
                        }
                    />
                    {portal.has_student && (
                        <button
                            type="button"
                            onClick={clearConversation}
                            className="inline-flex shrink-0 items-center gap-2 self-start rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-navy-900 shadow-sm transition hover:bg-slate-50"
                        >
                            <RotateCcw className="h-4 w-4" />
                            Limpiar conversación
                        </button>
                    )}
                </div>

                {!portal.has_student ? (
                    <StudentPortalEmpty message={portal.empty_message} portalScoped={portal.portal_scoped} />
                ) : (
                    <>
                        {insight && (
                            <div className="mb-2 grid gap-3 md:grid-cols-3">
                                <Card className="border-l-4 border-l-brand-yellow">
                                    <p className="text-xs font-semibold uppercase text-plomo">Nivel de atención</p>
                                    <p className="mt-1 text-2xl font-bold capitalize text-navy-900">
                                        {insight.risk.level}
                                    </p>
                                    <p className="text-sm text-plomo">Puntuación heurística: {insight.risk.score}</p>
                                </Card>
                                <Card>
                                    <p className="text-xs font-semibold uppercase text-plomo">Promedio (muestras)</p>
                                    <p className="mt-1 text-2xl font-bold text-navy-900">
                                        {insight.risk.average ?? '—'}
                                    </p>
                                </Card>
                                <Card>
                                    <p className="text-xs font-semibold uppercase text-plomo">Asistencia estimada</p>
                                    <p className="mt-1 text-2xl font-bold text-navy-900">
                                        {insight.risk.attendance_pct != null
                                            ? `${insight.risk.attendance_pct}%`
                                            : '—'}
                                    </p>
                                </Card>
                            </div>
                        )}

                        {(sendError || flash?.error) && (
                            <div
                                className="mb-3 flex items-start gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900"
                                role="alert"
                            >
                                <AlertCircle className="mt-0.5 h-5 w-5 shrink-0 text-red-600" />
                                <span>{sendError ?? flash?.error}</span>
                            </div>
                        )}

                        <div className="flex min-h-[min(560px,calc(100dvh-14rem))] flex-1 flex-col overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-sm">
                            <div className="flex min-h-0 flex-1 flex-col bg-gradient-to-b from-slate-50/90 to-white">
                                <div className="flex shrink-0 items-center gap-2 border-b border-slate-100 bg-white/80 px-4 py-3 backdrop-blur-sm">
                                    <div className="flex h-9 w-9 items-center justify-center rounded-full bg-navy-900 text-white">
                                        <Sparkles className="h-4 w-4 text-brand-yellow" aria-hidden />
                                    </div>
                                    <div className="min-w-0 flex-1">
                                        <p className="truncate text-sm font-semibold text-navy-900">
                                            Tutor IA — I.E.P. Horizonte
                                        </p>
                                        <p className="truncate text-xs text-plomo">
                                            {ai_enabled
                                                ? `En línea · ${provider}`
                                                : 'Asistente no disponible'}
                                        </p>
                                    </div>
                                </div>

                                <div className="min-h-0 flex-1 overflow-y-auto px-3 py-4 sm:px-5">
                                    <ul className="space-y-4">
                                        {messages.map((msg) => {
                                            const isUser = msg.role === 'user';
                                            return (
                                                <li
                                                    key={msg.id}
                                                    className={`flex gap-2 ${isUser ? 'justify-end' : 'justify-start'}`}
                                                >
                                                    {!isUser && (
                                                        <span
                                                            className="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-navy-900/10 text-navy-900"
                                                            aria-hidden
                                                        >
                                                            <Bot className="h-4 w-4" />
                                                        </span>
                                                    )}
                                                    <div
                                                        className={`max-w-[min(100%,32rem)] ${isUser ? 'items-end' : 'items-start'} flex flex-col gap-1`}
                                                    >
                                                        <div
                                                            className={`rounded-2xl px-4 py-3 text-sm leading-relaxed shadow-sm ${
                                                                isUser
                                                                    ? 'rounded-br-md bg-navy-900 text-white'
                                                                    : 'rounded-bl-md border border-slate-100 bg-white text-slate-800'
                                                            }`}
                                                        >
                                                            <p className="whitespace-pre-wrap break-words">{msg.content}</p>
                                                        </div>
                                                        <div
                                                            className={`flex flex-wrap items-center gap-2 px-1 text-[11px] text-plomo ${
                                                                isUser ? 'justify-end' : 'justify-start'
                                                            }`}
                                                        >
                                                            {!isUser && msg.meta?.cached && (
                                                                <span className="rounded bg-slate-200/80 px-1.5 py-0.5 text-[10px] font-medium text-slate-700">
                                                                    Caché
                                                                </span>
                                                            )}
                                                            {!isUser &&
                                                                msg.meta?.success === false &&
                                                                !msg.meta?.cached && (
                                                                    <span className="rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-medium text-amber-900">
                                                                        Respuesta limitada
                                                                    </span>
                                                                )}
                                                            <span>{formatMsgTime(msg.at)}</span>
                                                            <span className="text-plomo/80">
                                                                {isUser ? firstName : 'Tutor IA'}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    {isUser && (
                                                        <span
                                                            className="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-yellow/25 text-navy-900"
                                                            aria-hidden
                                                        >
                                                            <User className="h-4 w-4" />
                                                        </span>
                                                    )}
                                                </li>
                                            );
                                        })}
                                    </ul>

                                    {processing && (
                                        <div className="mt-4 flex justify-start gap-2">
                                            <span className="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-navy-900/10 text-navy-900">
                                                <Bot className="h-4 w-4" />
                                            </span>
                                            <div className="rounded-2xl rounded-bl-md border border-slate-100 bg-white px-4 py-3 text-sm text-plomo shadow-sm">
                                                <span className="inline-flex items-center gap-2">
                                                    <Loader2
                                                        className="h-4 w-4 animate-spin text-navy-900"
                                                        aria-hidden
                                                    />
                                                    Tutor IA escribiendo…
                                                </span>
                                            </div>
                                        </div>
                                    )}

                                    <div ref={endRef} className="h-px w-full shrink-0" aria-hidden />
                                </div>

                                <div className="border-t border-slate-200 bg-white p-3 sm:p-4">
                                    <form onSubmit={onSubmit} className="mx-auto max-w-4xl">
                                        <label className="sr-only" htmlFor="ai-tutor-message">
                                            Escribe tu mensaje para el tutor
                                        </label>
                                        <div className="flex flex-col gap-3 sm:flex-row sm:items-end">
                                            <div className="min-w-0 flex-1">
                                                <textarea
                                                    id="ai-tutor-message"
                                                    name="message"
                                                    rows={2}
                                                    value={data.message}
                                                    onChange={(e) => setData('message', e.target.value)}
                                                    onKeyDown={onKeyDown}
                                                    placeholder={
                                                        ai_enabled
                                                            ? `Escribe aquí, ${firstName}… (Enter para enviar, Shift+Enter para nueva línea)`
                                                            : 'El tutor está deshabilitado en esta instalación.'
                                                    }
                                                    maxLength={2000}
                                                    disabled={inputDisabled}
                                                    className="min-h-[3rem] w-full resize-none rounded-xl border border-slate-300 bg-slate-50/80 px-4 py-3 text-sm text-navy-900 shadow-inner placeholder:text-plomo focus:border-brand-yellow focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-yellow/35 disabled:cursor-not-allowed disabled:bg-slate-100"
                                                />
                                                {errors.message && (
                                                    <p className="mt-2 text-sm text-red-600">{errors.message}</p>
                                                )}
                                            </div>
                                            <button
                                                type="submit"
                                                disabled={!canSend}
                                                className="inline-flex h-[3.25rem] shrink-0 items-center justify-center gap-2 rounded-xl bg-navy-900 px-6 text-sm font-semibold text-white shadow-md transition hover:bg-navy-950 disabled:cursor-not-allowed disabled:bg-slate-400"
                                            >
                                                {processing ? (
                                                    <>
                                                        <Loader2 className="h-4 w-4 animate-spin" />
                                                        Enviando…
                                                    </>
                                                ) : (
                                                    <>
                                                        <Send className="h-4 w-4" aria-hidden />
                                                        Enviar
                                                    </>
                                                )}
                                            </button>
                                        </div>
                                        {!ai_enabled && (
                                            <p className="mt-3 text-center text-sm text-amber-900">
                                                El envío está deshabilitado (AI_TUTOR_ENABLED). Las recomendaciones por
                                                reglas siguen disponibles en «Recomendaciones IA».
                                            </p>
                                        )}
                                    </form>
                                </div>
                            </div>
                        </div>
                    </>
                )}
            </PageContainer>
        </StudentLayout>
    );
}
