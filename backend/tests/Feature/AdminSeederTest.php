<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_seeder_creates_the_default_administrator(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::where('email', 'admin@mmportal.local')->firstOrFail();

        $this->assertSame('Administrador', $admin->name);
        $this->assertSame(UserRole::Administrador, $admin->role);
        $this->assertTrue(Hash::check('admin123', $admin->password));
    }
}
