<?php

namespace App\Http\Controllers;

use App\Integrations\Services\WebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IntegrationWebhookController extends Controller
{
    public function payments(Request $request, WebhookService $webhooks): JsonResponse
    {
        try {
            $result = $webhooks->receive('payments', $request);

            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Invalid webhook'], 400);
        }
    }

    public function mercadoPago(Request $request, WebhookService $webhooks): JsonResponse
    {
        try {
            $result = $webhooks->receive('mercadopago', $request);

            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error'], 400);
        }
    }

    public function calendar(Request $request, WebhookService $webhooks): JsonResponse
    {
        try {
            $result = $webhooks->receive('calendar', $request);

            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error'], 400);
        }
    }
}
