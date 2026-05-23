<?php

namespace App\Policies\Cms;

use App\Models\Cms\CmsTestimonial;
use App\Models\User;

class CmsTestimonialPolicy extends CmsPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canAccessCms($user);
    }

    public function view(User $user, CmsTestimonial $testimonial): bool
    {
        return $this->canAccessCms($user);
    }

    public function create(User $user): bool
    {
        return $this->isEditor($user);
    }

    public function update(User $user, CmsTestimonial $testimonial): bool
    {
        return $this->isEditor($user);
    }

    public function delete(User $user, CmsTestimonial $testimonial): bool
    {
        return $this->isAdmin($user);
    }
}
