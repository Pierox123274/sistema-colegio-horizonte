<?php

namespace App\Policies\Cms;

use App\Models\Cms\CmsNews;
use App\Models\User;

class CmsNewsPolicy extends CmsPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canAccessCms($user);
    }

    public function view(User $user, CmsNews $news): bool
    {
        return $this->canAccessCms($user);
    }

    public function create(User $user): bool
    {
        return $this->isEditor($user);
    }

    public function update(User $user, CmsNews $news): bool
    {
        return $this->isEditor($user);
    }

    public function delete(User $user, CmsNews $news): bool
    {
        return $this->isAdmin($user);
    }
}
