<?php

namespace App\Policies\Cms;

use App\Models\Cms\CmsHeroSlide;
use App\Models\User;

class CmsHeroSlidePolicy extends CmsPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user, CmsHeroSlide $slide): bool
    {
        return $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, CmsHeroSlide $slide): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, CmsHeroSlide $slide): bool
    {
        return $this->isAdmin($user);
    }
}
