import { AppCard } from '@/Components/App/AppCard';
import { AppPageHeader } from '@/Components/App/AppPageHeader';
import NotificationPreferencesForm from '@/Components/Notifications/NotificationPreferencesForm';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import type { PageProps, SelectOption } from '@/types';
import { Head, usePage } from '@inertiajs/react';

type Props = PageProps<{
    preferences: {
        in_app_enabled: boolean;
        email_enabled: boolean;
        frequency: string;
        category_settings: Record<string, boolean>;
    };
    catalog: {
        categories: SelectOption[];
        frequencies: SelectOption[];
    };
}>;

export default function NotificationSettingsPage() {
    const { preferences, catalog } = usePage<Props>().props;

    return (
        <IntranetLayout title="Preferencias de notificaciones">
            <Head title="Preferencias de notificaciones — Horizonte" />
            <PageContainer>
                <AppPageHeader
                    title="Preferencias de notificaciones"
                    description="Configura canales, categorías y frecuencia de avisos operativos institucionales."
                    eyebrow="Comunicación"
                />

                <AppCard>
                    <NotificationPreferencesForm
                        preferences={preferences}
                        categories={catalog.categories}
                        frequencies={catalog.frequencies}
                    />
                </AppCard>
            </PageContainer>
        </IntranetLayout>
    );
}
