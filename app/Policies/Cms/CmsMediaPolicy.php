<?php

namespace App\Policies\Cms;

use App\Models\Cms\CmsMedia;
use App\Models\User;

class CmsMediaPolicy extends CmsPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canAccessCms($user);
    }

    public function create(User $user): bool
    {
        return $this->canAccessCms($user);
    }

    public function delete(User $user, CmsMedia $media): bool
    {
        return $this->isAdmin($user);
    }
}
