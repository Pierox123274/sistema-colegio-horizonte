<?php

namespace App\Policies\Cms;

use App\Models\Cms\CmsGallery;
use App\Models\User;

class CmsGalleryPolicy extends CmsPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canAccessCms($user);
    }

    public function view(User $user, CmsGallery $gallery): bool
    {
        return $this->canAccessCms($user);
    }

    public function create(User $user): bool
    {
        return $this->isEditor($user);
    }

    public function update(User $user, CmsGallery $gallery): bool
    {
        return $this->isEditor($user);
    }

    public function delete(User $user, CmsGallery $gallery): bool
    {
        return $this->isAdmin($user);
    }

    public function uploadImages(User $user, CmsGallery $gallery): bool
    {
        return $this->isEditor($user);
    }
}
