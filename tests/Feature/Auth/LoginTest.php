<?php

namespace Tests\Feature\Auth;

use App\Models\Batch;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('TO SKD Bimbel')
            ->assertSee('Masuk');
    }

    public function test_admin_can_login_and_access_dashboard(): void
    {
        $admin = User::factory()->create([
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->post(route('login.post'), [
            'username' => 'admin',
            'password' => 'admin123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Administrator');
    }

    public function test_student_can_login_and_access_dashboard(): void
    {
        $batch = Batch::create([
            'name' => 'Batch Test',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'username' => 'budi.santoso',
            'password' => Hash::make('siswa123'),
            'role' => 'student',
            'is_active' => true,
        ]);

        Student::create([
            'user_id' => $user->id,
            'batch_id' => $batch->id,
            'full_name' => 'Budi Santoso',
        ]);

        $this->post(route('login.post'), [
            'username' => 'budi.santoso',
            'password' => 'siswa123',
        ])->assertRedirect(route('student.dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Halo, Budi Santoso');
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->from(route('login'))
            ->post(route('login.post'), [
                'username' => 'admin',
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_inactive_user_is_logged_out_when_accessing_web_routes(): void
    {
        $user = User::factory()->create([
            'username' => 'inactive.admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => false,
        ]);

        $this->post(route('login.post'), [
            'username' => 'inactive.admin',
            'password' => 'admin123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_admin_cannot_access_student_dashboard(): void
    {
        $admin = User::factory()->create([
            'username' => 'admin',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('student.dashboard'))
            ->assertForbidden();
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $student = User::factory()->create([
            'username' => 'student',
            'role' => 'student',
            'is_active' => true,
        ]);

        $this->actingAs($student)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_user_can_logout(): void
    {
        $admin = User::factory()->create([
            'username' => 'admin',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
