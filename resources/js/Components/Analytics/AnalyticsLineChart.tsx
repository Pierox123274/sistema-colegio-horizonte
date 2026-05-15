import { Card } from '@/Components/Intranet/Card';
import {
    CartesianGrid,
    Line,
    LineChart,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';

type Point = { label: string; value: number };

export default function AnalyticsLineChart({
    title,
    data,
    color = '#c41e3a',
}: {
    title: string;
    data: Point[];
    color?: string;
}) {
    if (data.length === 0) {
        return (
            <Card>
                <h3 className="mb-2 text-sm font-bold text-navy-900">{title}</h3>
                <p className="text-sm text-plomo">Sin datos para el período seleccionado.</p>
            </Card>
        );
    }

    return (
        <Card>
            <h3 className="mb-4 text-sm font-bold text-navy-900">{title}</h3>
            <div className="h-64 w-full">
                <ResponsiveContainer width="100%" height="100%">
                    <LineChart data={data} margin={{ top: 8, right: 8, left: 0, bottom: 0 }}>
                        <CartesianGrid strokeDasharray="3 3" stroke="#e5e7eb" />
                        <XAxis dataKey="label" tick={{ fontSize: 11 }} />
                        <YAxis tick={{ fontSize: 11 }} />
                        <Tooltip />
                        <Line type="monotone" dataKey="value" stroke={color} strokeWidth={2} dot={false} />
                    </LineChart>
                </ResponsiveContainer>
            </div>
        </Card>
    );
}
