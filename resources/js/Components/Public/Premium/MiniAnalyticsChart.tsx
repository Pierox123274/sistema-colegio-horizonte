import {
    Area,
    AreaChart,
    Bar,
    BarChart,
    ResponsiveContainer,
    Tooltip,
    XAxis,
} from 'recharts';

const lineData = [
    { m: 'Ene', v: 72 },
    { m: 'Feb', v: 78 },
    { m: 'Mar', v: 81 },
    { m: 'Abr', v: 85 },
    { m: 'May', v: 88 },
    { m: 'Jun', v: 92 },
];

const barData = [
    { m: 'Lun', v: 4 },
    { m: 'Mar', v: 7 },
    { m: 'Mié', v: 5 },
    { m: 'Jue', v: 9 },
    { m: 'Vie', v: 6 },
];

type MiniAnalyticsChartProps = {
    variant?: 'area' | 'bar';
    dark?: boolean;
};

export function MiniAnalyticsChart({ variant = 'area', dark = false }: MiniAnalyticsChartProps) {
    const stroke = dark ? '#e4bc0f' : '#0c2340';
    const fill = dark ? 'rgba(228, 188, 15, 0.25)' : 'rgba(12, 35, 64, 0.12)';

    return (
        <div className="h-32 w-full">
            <ResponsiveContainer width="100%" height="100%">
                {variant === 'area' ? (
                    <AreaChart data={lineData} margin={{ top: 4, right: 4, left: 0, bottom: 0 }}>
                        <defs>
                            <linearGradient id="areaFill" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stopColor={fill} />
                                <stop offset="100%" stopColor="transparent" />
                            </linearGradient>
                        </defs>
                        <XAxis dataKey="m" hide />
                        <Tooltip
                            contentStyle={{
                                borderRadius: 12,
                                border: 'none',
                                fontSize: 12,
                                background: dark ? '#0f172a' : '#fff',
                            }}
                        />
                        <Area
                            type="monotone"
                            dataKey="v"
                            stroke={stroke}
                            fill="url(#areaFill)"
                            strokeWidth={2}
                        />
                    </AreaChart>
                ) : (
                    <BarChart data={barData} margin={{ top: 4, right: 4, left: 0, bottom: 0 }}>
                        <XAxis dataKey="m" tick={{ fontSize: 10, fill: dark ? '#94a3b8' : '#64748b' }} />
                        <Bar dataKey="v" fill={stroke} radius={[6, 6, 0, 0]} />
                    </BarChart>
                )}
            </ResponsiveContainer>
        </div>
    );
}
