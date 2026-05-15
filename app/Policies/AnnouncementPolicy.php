<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\Announcement;
use App\Models\User;
use App\Services\AnnouncementService;

class AnnouncementPolicy
{
    public function __construct(
        private readonly AnnouncementService $announcements
    ) {}

    public function viewAny(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function view(User $user, Announcement $announcement): bool
    {
        if ($user->hasRole(IntranetRole::Administrador->value)) {
            return true;
        }

        return $this->announcements->userCanView($announcement, $user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function update(User $user, Announcement $announcement): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function markRead(User $user, Announcement $announcement): bool
    {
        return $this->announcements->userCanView($announcement, $user);
    }
}
