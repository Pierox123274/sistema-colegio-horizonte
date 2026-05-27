import { BellOff } from 'lucide-react';

export default function NotificationEmptyState() {
    return (
        <div className="rounded-xl border border-dashed border-plomo/20 bg-white p-8 text-center">
            <BellOff className="mx-auto h-8 w-8 text-plomo-light" />
            <p className="mt-3 text-sm font-semibold text-navy-900">No tienes notificaciones en este filtro</p>
            <p className="mt-1 text-xs text-plomo">Cuando se generen alertas académicas u operativas aparecerán aquí.</p>
        </div>
    );
}
