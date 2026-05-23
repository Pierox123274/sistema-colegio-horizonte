<?php

namespace App\Policies\Cms;

use App\Models\Cms\CmsPage;
use App\Models\User;

class CmsPagePolicy extends CmsPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canAccessCms($user);
    }

    public function view(User $user, CmsPage $page): bool
    {
        return $this->canAccessCms($user);
    }

    public function create(User $user): bool
    {
        return $this->isEditor($user);
    }

    public function update(User $user, CmsPage $page): bool
    {
        return $this->isEditor($user);
    }

    public function delete(User $user, CmsPage $page): bool
    {
        return $this->isAdmin($user);
    }
}
