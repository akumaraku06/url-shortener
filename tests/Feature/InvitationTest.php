<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_invite_an_admin_into_a_new_company(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $company = Company::factory()->create();

        $response = $this->actingAs($superAdmin)->post(route('invitations.store'), [
            'company_id' => $company->id,
            'email' => 'newadmin@example.com',
            'role' => 'admin',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('invitations', [
            'email' => 'newadmin@example.com',
            'role' => 'admin',
            'company_id' => $company->id,
        ]);
    }

    public function test_superadmin_can_invite_a_member_into_a_company(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $company = Company::factory()->create();

        $response = $this->actingAs($superAdmin)->post(route('invitations.store'), [
            'company_id' => $company->id,
            'email' => 'newmember@example.com',
            'role' => 'member',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('invitations', [
            'email' => 'newmember@example.com',
            'role' => 'member',
            'company_id' => $company->id,
        ]);
    }

    public function test_admin_can_invite_another_admin_in_their_own_company(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->admin()->for($company)->create();

        $response = $this->actingAs($admin)->post(route('invitations.store'), [
            'email' => 'anotheradmin@example.com',
            'role' => 'admin',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('invitations', [
            'email' => 'anotheradmin@example.com',
            'role' => 'admin',
            'company_id' => $company->id,
        ]);
    }

    public function test_admin_can_invite_a_member_in_their_own_company(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->admin()->for($company)->create();

        $response = $this->actingAs($admin)->post(route('invitations.store'), [
            'email' => 'newmember@example.com',
            'role' => 'member',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('invitations', [
            'email' => 'newmember@example.com',
            'role' => 'member',
            'company_id' => $company->id,
        ]);
    }
}
