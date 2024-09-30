<?php

namespace App\Policies;

use App\Models\MessageTemplate;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MessageTemplatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->roles()->whereHas('permissions', function ($query) {
            $query->where('title', 'message_template_access');
        })->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MessageTemplate $messageTemplate): bool
    {
        return $user->roles()->whereHas('permissions', function ($query) {
            $query->where('title', 'message_template_show');
        })->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->roles()->whereHas('permissions', function ($query) {
            $query->where('title', 'message_template_create');
        })->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MessageTemplate $messageTemplate): bool
    {
        return $user->roles()->whereHas('permissions', function ($query) {
            $query->where('title', 'message_template_edit');
        })->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MessageTemplate $messageTemplate): bool
    {
        return $user->roles()->whereHas('permissions', function ($query) {
            $query->where('title', 'message_template_delete');
        })->exists();
    }
    public function deleteAny(User $user): bool
    {
        return $user->roles()->whereHas('permissions', function ($query) {
            $query->where('title', 'message_template_delete');
        })->exists();
    }
    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MessageTemplate $messageTemplate): bool
    {
        return $user->roles()->whereHas('permissions', function ($query) {
            $query->where('title', 'message_template_delete');
        })->exists();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MessageTemplate $messageTemplate): bool
    {
        return $user->roles()->whereHas('permissions', function ($query) {
            $query->where('title', 'message_template_delete');
        })->exists();
    }
}
