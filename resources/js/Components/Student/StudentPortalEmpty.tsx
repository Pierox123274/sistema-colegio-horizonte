import { EmptyState } from '@/Components/Intranet/EmptyState';
import { GraduationCap } from 'lucide-react';

export default function StudentPortalEmpty({
    message,
    portalScoped,
}: {
    message: string;
    portalScoped: boolean;
}) {
    return (
        <EmptyState
            icon={GraduationCap}
            title={portalScoped ? 'Sin ficha vinculada' : 'Vista de supervisión'}
            description={message}
        />
    );
}
