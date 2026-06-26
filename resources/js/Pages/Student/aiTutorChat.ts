import type { FlashMessages } from '@/types';
import type { Dispatch, SetStateAction } from 'react';

export type ChatMessage = {
    id: string;
    role: 'user' | 'assistant';
    content: string;
    at: string;
    meta?: { cached?: boolean; success?: boolean; fallback?: boolean };
};

export function appendAssistantMessage(
    setMessages: Dispatch<SetStateAction<ChatMessage[]>>,
    reply: string,
    meta?: ChatMessage['meta'],
): void {
    setMessages((m) => [
        ...m,
        {
            id: crypto.randomUUID(),
            role: 'assistant',
            content: reply,
            at: new Date().toISOString(),
            meta,
        },
    ]);
}

export function handleTutorMessageSuccess(
    page: { props: { flash?: FlashMessages } },
    setMessages: Dispatch<SetStateAction<ChatMessage[]>>,
    resetMessage: () => void,
): void {
    const f = page.props.flash;
    const reply = f?.ai?.ai_reply;
    const meta = f?.ai?.ai_meta;
    if (reply != null && reply !== '') {
        appendAssistantMessage(setMessages, reply, {
            cached: meta?.cached,
            success: meta?.success,
            fallback: meta?.fallback,
        });
    }
    resetMessage();
}

export function handleTutorMessageError(
    pendingId: string | null,
    setMessages: Dispatch<SetStateAction<ChatMessage[]>>,
): void {
    if (!pendingId) {
        return;
    }
    setMessages((m) => m.filter((x) => x.id !== pendingId));
}

export function formatCoachResult(
    kind: 'summary' | 'mini-quiz' | 'practice' | 'explain',
    data: Record<string, unknown>,
): string {
    if (kind === 'summary' && Array.isArray(data.summary_points)) {
        return (data.summary_points as string[]).map((p) => `• ${p}`).join('\n');
    }
    if (kind === 'explain' && data.explanation) {
        return String(data.explanation);
    }
    if (kind === 'practice' && Array.isArray(data.exercises)) {
        return (data.exercises as string[]).map((e, i) => `${i + 1}. ${e}`).join('\n');
    }
    if (kind === 'mini-quiz' && Array.isArray(data.items)) {
        return `${(data.items as unknown[]).length} preguntas generadas. Revísalas con tu docente.`;
    }
    return JSON.stringify(data, null, 2);
}
