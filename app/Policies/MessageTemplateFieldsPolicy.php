<?php

namespace App\Policies;

use App\Models\MessageTemplateFields;
use App\Models\User;

class MessageTemplateFieldsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->roles()->whereHas('permissions', function ($query) {
            $query->where('title', 'message_template_fields_access');
        })->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MessageTemplateFields $messageTemplateFields): bool
    {
        return $user->roles()->whereHas('permissions', function ($query) {
            $query->where('title', 'message_template_fields_show');
        })->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->roles()->whereHas('permissions', function ($query) {
            $query->where('title', 'message_template_fields_create');
        })->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MessageTemplateFields $messageTemplateFields): bool
    {
        return $user->roles()->whereHas('permissions', function ($query) {
            $query->where('title', 'message_template_fields_edit');
        })->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MessageTemplateFields $messageTemplateFields): bool
    {
        return $user->roles()->whereHas('permissions', function ($query) {
            $query->where('title', 'message_template_fields_delete');
        })->exists();
    }

    public function deleteAny(User $user): bool
    {
        return $user->roles()->whereHas('permissions', function ($query) {
            $query->where('title', 'message_template_fields_delete');
        })->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MessageTemplateFields $messageTemplateFields): bool
    {
        return $user->roles()->whereHas('permissions', function ($query) {
            $query->where('title', 'message_template_fields_delete');
        })->exists();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MessageTemplateFields $messageTemplateFields): bool
    {
        return $user->roles()->whereHas('permissions', function ($query) {
            $query->where('title', 'message_template_fields_delete');
        })->exists();
    }
}
