<?php

namespace App\Policies\Cms;

use App\Models\Cms\CmsMenu;
use App\Models\User;

class CmsMenuPolicy extends CmsPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user, CmsMenu $menu): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, CmsMenu $menu): bool
    {
        return $this->isAdmin($user);
    }
}
