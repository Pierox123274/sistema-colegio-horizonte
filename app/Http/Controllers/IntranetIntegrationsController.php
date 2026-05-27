<?php

namespace App\Http\Controllers;

use App\Integrations\Services\ExternalStorageService;
use App\Integrations\Services\InstitutionMailService;
use App\Integrations\Services\IntegrationHealthService;
use App\Integrations\Services\IntegrationRegistry;
use App\Integrations\Services\WebhookService;
use App\Models\IntegrationEmailLog;
use App\Models\IntegrationWebhookLog;
use App\Support\IntegrationsDashboard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IntranetIntegrationsController extends Controller
{
    public function index(
        IntegrationRegistry $registry,
        IntegrationHealthService $health,
        InstitutionMailService $mail,
        ExternalStorageService $storage,
    ): Response {
        $this->authorize('viewIntegrations', IntegrationsDashboard::class);

        $webhooks = IntegrationWebhookLog::query()->orderByDesc('id')->limit(10)->get([
            'id', 'provider', 'event_type', 'status', 'signature_valid', 'created_at',
        ]);
        $emails = IntegrationEmailLog::query()->orderByDesc('id')->limit(10)->get([
            'id', 'mailable_class', 'status', 'attempts', 'mailer', 'created_at',
        ]);

        return Inertia::render('Intranet/Integrations/Index', [
            'providers' => $registry->providerCards(),
            'health' => $health->snapshot(),
            'mail' => $mail->health(),
            'storage' => $storage->status(),
            'recent_webhooks' => $webhooks,
            'recent_emails' => $emails,
            'integrations_enabled' => (bool) config('integrations.enabled'),
        ]);
    }

    public function replayWebhook(
        Request $request,
        IntegrationWebhookLog $webhookLog,
        WebhookService $webhooks,
    ): RedirectResponse {
        $this->authorize('viewIntegrations', IntegrationsDashboard::class);

        $user = $request->user();
        abort_if($user === null, 403);

        $ok = $webhooks->replay($webhookLog, $user);

        return redirect()
            ->route('intranet.integrations.index')
            ->with($ok ? 'success' : 'error', $ok
                ? 'Webhook marcado para replay.'
                : 'Límite de replay alcanzado.');
    }
}
