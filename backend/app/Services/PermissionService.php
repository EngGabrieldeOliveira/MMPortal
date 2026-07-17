<?php

namespace App\Services;

use App\Enums\AdministrativeEventType;
use App\Enums\UserRole;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PermissionService
{
    public function __construct(private readonly AdministrativeEventService $events) {}

    public function setRolePermission(UserRole|string $role, Permission $permission, bool $allowed = true): void
    {
        $roleValue = $role instanceof UserRole ? $role->value : $role;
        if ($allowed) {
            DB::table('role_permissions')->updateOrInsert(['role' => $roleValue, 'permission_id' => $permission->id], ['created_at' => now(), 'updated_at' => now()]);
        } else {
            DB::table('role_permissions')->where(['role' => $roleValue, 'permission_id' => $permission->id])->delete();
        }
        $this->events->record(AdministrativeEventType::PermissionUpdated, 'warning', 'seguranca', 'Permissão de perfil atualizada.', ['role' => $roleValue, 'permission' => $permission->key, 'allowed' => $allowed]);
    }

    public function setUserPermission(User $user, Permission $permission, bool $allowed): void
    {
        $user->permissions()->syncWithoutDetaching([$permission->id => ['allowed' => $allowed]]);
        $this->events->record(AdministrativeEventType::PermissionUpdated, 'warning', 'seguranca', 'Permissão individual atualizada.', ['target_user_id' => $user->id, 'permission' => $permission->key, 'allowed' => $allowed]);
    }
}
