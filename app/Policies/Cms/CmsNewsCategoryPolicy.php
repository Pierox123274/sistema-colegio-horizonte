<?php

namespace App\Policies\Cms;

use App\Models\Cms\CmsNewsCategory;
use App\Models\User;

class CmsNewsCategoryPolicy extends CmsPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canAccessCms($user);
    }

    public function create(User $user): bool
    {
        return $this->isEditor($user);
    }

    public function update(User $user, CmsNewsCategory $category): bool
    {
        return $this->isEditor($user);
    }

    public function delete(User $user, CmsNewsCategory $category): bool
    {
        return $this->isAdmin($user);
    }
}
