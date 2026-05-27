import type { ReactNode } from 'react';

type Props = {
    steps: string[];
    currentStep: number;
    children: ReactNode;
};

export default function AIGenerationWizard({ steps, currentStep, children }: Props) {
    return (
        <div className="space-y-6">
            <ol className="flex flex-wrap gap-2">
                {steps.map((label, i) => (
                    <li
                        key={label}
                        className={`rounded-full px-3 py-1 text-xs font-semibold ${
                            i <= currentStep
                                ? 'bg-navy text-white'
                                : 'bg-slate-100 text-plomo'
                        }`}
                    >
                        {i + 1}. {label}
                    </li>
                ))}
            </ol>
            {children}
        </div>
    );
}
