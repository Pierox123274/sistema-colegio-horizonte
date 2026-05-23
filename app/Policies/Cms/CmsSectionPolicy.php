<?php

namespace App\Policies\Cms;

use App\Models\Cms\CmsSection;
use App\Models\User;

class CmsSectionPolicy extends CmsPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, CmsSection $section): bool
    {
        return $this->isAdmin($user);
    }
}
