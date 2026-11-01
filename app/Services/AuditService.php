<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log a change to an auditable entity.
     *
     * @param User $actor The user performing the action
     * @param Model $auditable The model being changed
     * @param string $action The action being performed (e.g., 'update_identity', 'create', 'delete')
     * @param array $old Old values before change
     * @param array $new New values after change
     * @param string|null $ip IP address (defaults to current request IP)
     */
    public static function logChange(
        User $actor,
        Model $auditable,
        string $action,
        array $old,
        array $new,
        ?string $ip = null
    ): AuditLog {
        return AuditLog::create([
            'actor_user_id' => $actor->id,
            'action' => $action,
            'auditable_type' => $auditable->getMorphClass(),
            'auditable_id' => $auditable->getKey(),
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => $ip ?? Request::ip(),
        ]);
    }
}
