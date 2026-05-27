import { Link } from '@inertiajs/react';
import { BookOpen, ClipboardList, FileText, LayoutGrid, Sparkles } from 'lucide-react';

const tools = [
    {
        title: 'Generador de exámenes',
        href: 'teacher.ai-copilot.exams',
        icon: ClipboardList,
        desc: 'Preguntas, alternativas y explicaciones al banco.',
    },
    {
        title: 'Generador de tareas',
        href: 'teacher.ai-copilot.assignments',
        icon: FileText,
        desc: 'Instrucciones, objetivos y criterios para LMS.',
    },
    {
        title: 'Rúbricas analíticas',
        href: 'teacher.ai-copilot.rubrics',
        icon: BookOpen,
        desc: 'Criterios, niveles y ponderaciones.',
    },
    {
        title: 'Insights docente',
        href: 'teacher.ai-insights.index',
        icon: Sparkles,
        desc: 'Riesgo académico y narrativa IA.',
    },
    {
        title: 'Panel pedagógico',
        href: 'teacher.pedagogical-panel.index',
        icon: LayoutGrid,
        desc: 'Adaptive learning y diagnósticos.',
    },
];

export default function AIAssistantPanel() {
    return (
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {tools.map((tool) => {
                const Icon = tool.icon;
                return (
                    <Link
                        key={tool.href}
                        href={route(tool.href)}
                        className="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-brand-yellow hover:shadow-md"
                    >
                        <Icon className="mb-2 h-6 w-6 text-navy group-hover:text-brand-yellow" />
                        <h3 className="text-sm font-semibold text-navy">{tool.title}</h3>
                        <p className="mt-1 text-xs text-plomo">{tool.desc}</p>
                    </Link>
                );
            })}
        </div>
    );
}
