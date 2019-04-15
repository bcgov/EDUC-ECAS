<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DashboardTest extends DuskTestCase
{
    /** @test */
    public function see_home_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Login');
        });
    }

    /** @test */
    public function see_dashboard()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/Dashboard')
                ->assertSee('Dashboard');
        });
    }

    /** @test */
    public function new_profile()
    {
        $this->browse(function (Browser $browser) {

            // Some fields are required
            $browser->visit('/Dashboard')
                ->assertSee('Dashboard')
                ->type('email', 'taylor@laravel.com')
                ->click('@save')
                ->waitForText('Required');

            // Fill in all required fields
            $browser->visit('/Dashboard')
                ->type('email', 'taylor@laravel.com')
                ->select('region', 'BC')
                ->type('first_name', 'Test')
                ->type('last_name', 'User')
                ->type('phone', '250.812.1234')
                ->type('address_1', '1234 Road')
                ->type('city', 'Vancouver')
                ->type('postal_code', 'V8V1J5')
                ->click('@save')
                // We should now have saved the new Profile and made it available in the vue store
                ->waitUntilVue('getUser.first_name', 'Test', '@dashboard-component');
        });
    }
}
