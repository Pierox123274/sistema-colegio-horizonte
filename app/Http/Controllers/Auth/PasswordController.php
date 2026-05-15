<?php

namespace App\Http\Controllers\Auth;

use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\AuditSeverity;
use App\Http\Controllers\Controller;
use App\Services\AuditService;
use App\Services\SessionSecurityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
        private readonly SessionSecurityService $sessions,
    ) {}

    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();
        abort_if($user === null, 403);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->sessions->invalidateOtherSessions($user, (string) $request->session()->getId());

        $this->audit->log(
            AuditAction::Update,
            AuditModule::Auth,
            $user,
            description: 'Contraseña actualizada; otras sesiones invalidadas',
            severity: AuditSeverity::Warning,
            request: $request,
        );

        return back();
    }
}
