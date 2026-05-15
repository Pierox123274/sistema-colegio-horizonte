import { Card } from '@/Components/Intranet/Card';
import {
    Bar,
    BarChart,
    CartesianGrid,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';

type Point = { label: string; value: number; average?: number };

export default function AnalyticsBarChart({
    title,
    data,
    dataKey = 'value',
    color = '#1a2744',
}: {
    title: string;
    data: Point[];
    dataKey?: string;
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
                    <BarChart data={data} margin={{ top: 8, right: 8, left: 0, bottom: 0 }}>
                        <CartesianGrid strokeDasharray="3 3" stroke="#e5e7eb" />
                        <XAxis dataKey="label" tick={{ fontSize: 11 }} interval={0} angle={-20} textAnchor="end" height={60} />
                        <YAxis tick={{ fontSize: 11 }} />
                        <Tooltip />
                        <Bar dataKey={dataKey} fill={color} radius={[4, 4, 0, 0]} />
                    </BarChart>
                </ResponsiveContainer>
            </div>
        </Card>
    );
}
