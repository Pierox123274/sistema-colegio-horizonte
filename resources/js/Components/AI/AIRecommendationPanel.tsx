import { Card } from '@/Components/Intranet/Card';
import { AlertTriangle } from 'lucide-react';

type Predictive = {
    students_visible: number;
    high_risk: number;
    flags: Record<string, number>;
    suggested_interventions: string[];
};

type Props = {
    data: Predictive | null;
    loading?: boolean;
};

export default function AIRecommendationPanel({ data, loading }: Props) {
    return (
        <Card>
            <h3 className="mb-3 flex items-center gap-2 text-sm font-semibold text-navy">
                <AlertTriangle className="h-4 w-4 text-amber-600" />
                Recomendaciones predictivas
            </h3>
            {loading && <p className="text-sm text-plomo">Analizando señales académicas…</p>}
            {!loading && data && (
                <div className="space-y-3 text-sm">
                    <p className="text-plomo">
                        {data.students_visible} estudiantes visibles · {data.high_risk} en riesgo alto
                    </p>
                    <ul className="list-inside list-disc text-slate-700">
                        {data.suggested_interventions.length === 0 ? (
                            <li>Sin intervenciones urgentes sugeridas.</li>
                        ) : (
                            data.suggested_interventions.map((item) => <li key={item}>{item}</li>)
                        )}
                    </ul>
                </div>
            )}
        </Card>
    );
}
