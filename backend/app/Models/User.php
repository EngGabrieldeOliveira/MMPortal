<?php

namespace App\Models;

use App\Enums\UserRole;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public function hasRole(UserRole|string ...$roles): bool
    {
        $roleValues = array_map(fn (UserRole|string $role) => $role instanceof UserRole ? $role->value : $role, $roles);

        return in_array($this->roleValue(), $roleValues, true);
    }

    /** @return BelongsToMany<Permission, $this> */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')->withPivot('allowed')->withTimestamps();
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->hasRole(UserRole::Administrador)) {
            return true;
        }

        $override = $this->permissions()->where('key', $permission)->first();
        if ($override) {
            return (bool) $override->pivot->getAttribute('allowed');
        }

        return DB::table('role_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('role_permissions.role', $this->roleValue())
            ->where('permissions.key', $permission)
            ->exists();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    private function roleValue(): string
    {
        $role = $this->getRawOriginal('role');

        return is_string($role) ? $role : UserRole::Comercial->value;
    }
}
