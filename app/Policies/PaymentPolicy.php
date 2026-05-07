<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->financeRoles($user);
    }

    public function view(User $user, Payment $payment): bool
    {
        return $this->financeRoles($user);
    }

    public function create(User $user): bool
    {
        return $this->financeRoles($user);
    }

    public function cancel(User $user, Payment $payment): bool
    {
        return $this->financeRoles($user);
    }

    private function financeRoles(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
        ]);
    }
}
