<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\PaymentConcept;
use App\Models\User;

class PaymentConceptPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->financeRoles($user);
    }

    public function view(User $user, PaymentConcept $paymentConcept): bool
    {
        return $this->financeRoles($user);
    }

    public function create(User $user): bool
    {
        return $this->financeRoles($user);
    }

    public function update(User $user, PaymentConcept $paymentConcept): bool
    {
        return $this->financeRoles($user);
    }

    public function delete(User $user, PaymentConcept $paymentConcept): bool
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
