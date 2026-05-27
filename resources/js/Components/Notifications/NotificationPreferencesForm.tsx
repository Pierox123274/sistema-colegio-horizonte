import type { SelectOption } from '@/types';
import { useForm } from '@inertiajs/react';

type Props = {
    preferences: {
        in_app_enabled: boolean;
        email_enabled: boolean;
        frequency: string;
        category_settings: Record<string, boolean>;
    };
    categories: SelectOption[];
    frequencies: SelectOption[];
};

export default function NotificationPreferencesForm({ preferences, categories, frequencies }: Props) {
    const form = useForm({
        in_app_enabled: preferences.in_app_enabled,
        email_enabled: preferences.email_enabled,
        frequency: preferences.frequency,
        category_settings: preferences.category_settings ?? {},
    });

    const toggleCategory = (key: string) => {
        form.setData('category_settings', {
            ...form.data.category_settings,
            [key]: !form.data.category_settings[key],
        });
    };

    return (
        <form
            onSubmit={(e) => {
                e.preventDefault();
                form.put(route('settings.notifications.update'));
            }}
            className="space-y-6"
        >
            <div className="grid gap-4 sm:grid-cols-2">
                <label className="flex items-center gap-2 rounded-lg border border-plomo/15 bg-white px-3 py-2 text-sm">
                    <input
                        type="checkbox"
                        checked={form.data.in_app_enabled}
                        onChange={(e) => form.setData('in_app_enabled', e.target.checked)}
                    />
                    Notificaciones in-app
                </label>
                <label className="flex items-center gap-2 rounded-lg border border-plomo/15 bg-white px-3 py-2 text-sm">
                    <input
                        type="checkbox"
                        checked={form.data.email_enabled}
                        onChange={(e) => form.setData('email_enabled', e.target.checked)}
                    />
                    Notificaciones por email
                </label>
            </div>

            <div>
                <label className="mb-2 block text-sm font-semibold text-navy-900">Frecuencia</label>
                <select
                    value={form.data.frequency}
                    onChange={(e) => form.setData('frequency', e.target.value)}
                    className="w-full rounded-lg border border-plomo/20 bg-white px-3 py-2 text-sm focus:border-navy-300 focus:outline-none"
                >
                    {frequencies.map((option) => (
                        <option key={option.value} value={option.value}>
                            {option.label}
                        </option>
                    ))}
                </select>
            </div>

            <div>
                <p className="mb-2 text-sm font-semibold text-navy-900">Categorías</p>
                <div className="grid gap-2 sm:grid-cols-2">
                    {categories.map((category) => (
                        <label
                            key={category.value}
                            className="flex items-center gap-2 rounded-lg border border-plomo/15 bg-white px-3 py-2 text-sm"
                        >
                            <input
                                type="checkbox"
                                checked={form.data.category_settings[category.value] ?? true}
                                onChange={() => toggleCategory(category.value)}
                            />
                            {category.label}
                        </label>
                    ))}
                </div>
            </div>

            <div>
                <button
                    type="submit"
                    disabled={form.processing}
                    className="rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-navy-800 disabled:opacity-60"
                >
                    Guardar preferencias
                </button>
            </div>
        </form>
    );
}
