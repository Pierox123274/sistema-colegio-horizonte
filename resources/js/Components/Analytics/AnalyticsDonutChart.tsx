import { Card } from '@/Components/Intranet/Card';
import { Cell, Pie, PieChart, ResponsiveContainer, Tooltip } from 'recharts';

const COLORS = ['#1a2744', '#f4c430', '#c41e3a', '#6b7280', '#2563eb', '#059669'];

type Point = { label: string; value: number };

export default function AnalyticsDonutChart({ title, data }: { title: string; data: Point[] }) {
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
                    <PieChart>
                        <Pie
                            data={data}
                            dataKey="value"
                            nameKey="label"
                            cx="50%"
                            cy="50%"
                            innerRadius={55}
                            outerRadius={85}
                            paddingAngle={2}
                        >
                            {data.map((_, index) => (
                                <Cell key={index} fill={COLORS[index % COLORS.length]} />
                            ))}
                        </Pie>
                        <Tooltip />
                    </PieChart>
                </ResponsiveContainer>
            </div>
        </Card>
    );
}
