<?php

namespace App\Policies\Cms;

use App\Models\User;

class CmsSettingPolicy extends CmsPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canAccessCms($user);
    }

    public function update(User $user): bool
    {
        return $this->isAdmin($user);
    }
}
