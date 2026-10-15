<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role = 'owner'): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $role.'@example.test',
            'password' => 'correct-password',
            'role' => $role,
        ]);
    }

    public function test_valid_owner_and_staff_credentials_sign_in(): void
    {
        foreach (['owner', 'staff'] as $role) {
            $user = $this->makeUser($role);

            $this->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'correct-password',
            ])->assertRedirect(route('admin.orders'));

            $this->assertAuthenticatedAs($user);
            auth()->logout();
        }
    }

    public function test_wrong_password_returns_safe_validation_error(): void
    {
        $user = $this->makeUser();

        $this->from(route('login'))->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_route_throttles_repeated_attempts(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login.store'), [
                'email' => 'unknown@example.test',
                'password' => 'wrong-password',
            ])->assertRedirect();
        }

        $this->post(route('login.store'), [
            'email' => 'unknown@example.test',
            'password' => 'wrong-password',
        ])->assertTooManyRequests();
    }

    public function test_account_without_dashboard_role_cannot_sign_in(): void
    {
        $user = $this->makeUser('customer');

        $this->from(route('login'))->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'correct-password',
        ])->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_pages_require_authentication(): void
    {
        $this->get(route('admin.orders'))->assertRedirect(route('login'));
    }

    public function test_dashboard_rejects_users_without_a_dashboard_role(): void
    {
        $user = $this->makeUser('customer');

        $this->actingAs($user)->get(route('admin.orders'))->assertForbidden();
    }

    public function test_staff_cannot_open_owner_only_pages(): void
    {
        $staff = $this->makeUser('staff');

        $this->actingAs($staff)->get(route('admin.report'))->assertForbidden();
        $this->actingAs($staff)->get(route('admin.settings'))->assertForbidden();
    }

    public function test_logout_clears_authentication_and_redirects_to_storefront(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }
}
