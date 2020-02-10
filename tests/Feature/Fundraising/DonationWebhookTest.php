<?php

namespace App\Tests\Feature\Fundraising;

use Tests\TestCase;

use App\User;
use App\Role;
use App\RolePermission;

use Illuminate\Support\Str;

use Carbon\Carbon;

class DonationWebhookTest extends TestCase
{
    public function testRequireAuthentication()
    {
        $response = $this->post('api/fundraising/donations/raiseNowWebHookListener', []);

        $this->assertGuest();
        $response->assertStatus(401);
    }

    public function testRequireAuthorization()
    {
        $user = factory(User::class)->make();

        $response = $this
            ->actingAs($user)
            ->post('api/fundraising/donations/raiseNowWebHookListener', []);

        $this->assertAuthenticated();
        $response->assertStatus(403);
    }

    public function testValidation()
    {
        $user = $this->makeUserWithPermission('fundraising.donations.accept_webhooks');

        $response = $this
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->actingAs($user)
            ->post('api/fundraising/donations/raiseNowWebHookListener', []);

        $this->assertAuthenticated();
        $response->assertStatus(422);
    }

    public function testNewDonorWithDonation()
    {
        $user = $this->makeUserWithPermission('fundraising.donations.accept_webhooks');
        $purpose = Str::random(40);

        $response = $this
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->actingAs($user)
            ->post('api/fundraising/donations/raiseNowWebHookListener', [
                'stored_customer_firstname' => 'Hans',
                'stored_customer_lastname' => 'Muster',
                'stored_customer_street' => 'Musterstrasse',
                'stored_customer_city' => 'Musterort',
                'amount' => '1000',
                'currency' => 'CHF',
                'payment_method' => 'ECA',
                'stored_customer_message' => $purpose,
            ]);

        $this->assertAuthenticated();
        $response->assertStatus(200);

        $this->assertDatabaseHas('donors', [
            'first_name' => 'Hans',
            'last_name' => 'Muster',
            'street' => 'Musterstrasse',
            'city' => 'Musterort',
        ]);

        $this->assertDatabaseHas('donations', [
            'date' => Carbon::today()->toDateString(),
            'amount' => 10,
            'currency' => 'CHF',
            'exchange_amount' => 10,
            'channel' => 'RaiseNow (MasterCard)',
            'purpose' => $purpose,
        ]);
    }

    protected function makeUserWithPermission($permission)
    {
        // $user = $this->createMock(User::class);
        // $user->expects($this->any())
        //     ->method('hasPermission')
        //     ->with('fundraising.donations.accept_webhooks')
        //     ->willReturn(true);

        $user = factory(User::class)->make();
        $role = factory(Role::class)->make();
        $user->roles[0] = $role;
        $role->permissions[0] = RolePermission::make([
            'key' => $permission,
        ]);

        return $user;
    }
}