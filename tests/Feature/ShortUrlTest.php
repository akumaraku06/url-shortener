<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_short_urls(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->admin()->for($company)->create();

        $response = $this->actingAs($admin)->post(route('short-urls.store'), [
            'original_url' => 'https://example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('short_urls', [
            'user_id' => $admin->id,
            'company_id' => $company->id,
            'original_url' => 'https://example.com',
        ]);
    }

    public function test_member_can_create_short_urls(): void
    {
        $company = Company::factory()->create();
        $member = User::factory()->member()->for($company)->create();

        $response = $this->actingAs($member)->post(route('short-urls.store'), [
            'original_url' => 'https://example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('short_urls', [
            'user_id' => $member->id,
            'company_id' => $company->id,
            'original_url' => 'https://example.com',
        ]);
    }

    public function test_superadmin_cannot_create_short_urls(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)->get(route('short-urls.create'));
        $response->assertForbidden();

        $response = $this->actingAs($superAdmin)->post(route('short-urls.store'), [
            'original_url' => 'https://example.com',
        ]);
        $response->assertForbidden();

        $this->assertDatabaseCount('short_urls', 0);
    }

    public function test_superadmin_can_view_short_url_list_for_every_company(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $adminA = User::factory()->admin()->for($companyA)->create();
        $adminB = User::factory()->admin()->for($companyB)->create();

        $urlA = ShortUrl::factory()->create(['user_id' => $adminA->id, 'company_id' => $companyA->id]);
        $urlB = ShortUrl::factory()->create(['user_id' => $adminB->id, 'company_id' => $companyB->id]);

        $response = $this->actingAs($superAdmin)->get(route('short-urls.index'));

        $response->assertOk();
        $response->assertSee($urlA->code);
        $response->assertSee($urlB->code);
    }

    public function test_admin_can_only_see_short_urls_created_in_their_own_company(): void
    {
        $ownCompany = Company::factory()->create();
        $otherCompany = Company::factory()->create();

        $admin = User::factory()->admin()->for($ownCompany)->create();
        $memberInOwnCompany = User::factory()->member()->for($ownCompany)->create();
        $adminInOtherCompany = User::factory()->admin()->for($otherCompany)->create();

        $ownCompanyUrl = ShortUrl::factory()->create([
            'user_id' => $memberInOwnCompany->id,
            'company_id' => $ownCompany->id,
        ]);

        $otherCompanyUrl = ShortUrl::factory()->create([
            'user_id' => $adminInOtherCompany->id,
            'company_id' => $otherCompany->id,
        ]);

        $response = $this->actingAs($admin)->get(route('short-urls.index'));

        $response->assertOk();
        $response->assertSee($ownCompanyUrl->code);
        $response->assertDontSee($otherCompanyUrl->code);
    }

    public function test_member_can_only_see_short_urls_created_by_themselves(): void
    {
        $company = Company::factory()->create();

        $member = User::factory()->member()->for($company)->create();
        $otherAdmin = User::factory()->admin()->for($company)->create();

        $ownUrl = ShortUrl::factory()->create([
            'user_id' => $member->id,
            'company_id' => $company->id,
        ]);

        $othersUrl = ShortUrl::factory()->create([
            'user_id' => $otherAdmin->id,
            'company_id' => $company->id,
        ]);

        $response = $this->actingAs($member)->get(route('short-urls.index'));

        $response->assertOk();
        $response->assertSee($ownUrl->code);
        $response->assertDontSee($othersUrl->code);
    }

    public function test_short_urls_are_publicly_resolvable_and_redirect_to_original_url(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->admin()->for($company)->create();

        $shortUrl = ShortUrl::factory()->create([
            'user_id' => $admin->id,
            'company_id' => $company->id,
            'original_url' => 'https://example.com/target-page',
        ]);

        // Guest (unauthenticated) hit must resolve and redirect publicly.
        $guestResponse = $this->get(route('short-urls.redirect', $shortUrl->code));
        $guestResponse->assertRedirect('https://example.com/target-page');
    }
}
