import type { ReactNode } from 'react';

type AppTab = {
    key: string;
    label: string;
    content: ReactNode;
};

type AppTabsProps = {
    tabs: AppTab[];
    active: string;
    onChange: (key: string) => void;
};

export function AppTabs({ tabs, active, onChange }: AppTabsProps) {
    const activeTab = tabs.find((t) => t.key === active) ?? tabs[0];

    return (
        <div>
            <div className="flex flex-wrap gap-2 border-b border-slate-200 pb-2 dark:border-white/10">
                {tabs.map((tab) => (
                    <button
                        key={tab.key}
                        type="button"
                        onClick={() => onChange(tab.key)}
                        className={`rounded-lg px-3 py-1.5 text-sm font-medium transition ${
                            tab.key === active
                                ? 'bg-navy-900 text-white'
                                : 'text-plomo hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'
                        }`}
                    >
                        {tab.label}
                    </button>
                ))}
            </div>
            <div className="pt-4">{activeTab?.content}</div>
        </div>
    );
}
