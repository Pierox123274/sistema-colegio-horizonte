import { Card } from '@/Components/Intranet/Card';
import { PageContainer } from '@/Components/Intranet/PageContainer';
import IntranetLayout from '@/Layouts/IntranetLayout';
import { PageProps } from '@/types';
import { Head } from '@inertiajs/react';
import DeleteUserForm from './Partials/DeleteUserForm';
import UpdatePasswordForm from './Partials/UpdatePasswordForm';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm';

export default function Edit({
    mustVerifyEmail,
    status,
}: PageProps<{ mustVerifyEmail: boolean; status?: string }>) {
    return (
        <IntranetLayout title="Mi perfil">
            <Head title="Mi perfil" />

            <PageContainer width="default">
                <div className="mx-auto max-w-3xl space-y-6">
                    <Card>
                        <UpdateProfileInformationForm
                            mustVerifyEmail={mustVerifyEmail}
                            status={status}
                            className="max-w-xl"
                        />
                    </Card>

                    <Card>
                        <UpdatePasswordForm className="max-w-xl" />
                    </Card>

                    <Card>
                        <DeleteUserForm className="max-w-xl" />
                    </Card>
                </div>
            </PageContainer>
        </IntranetLayout>
    );
}
