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
                ->assertSee('ECAS')
                ->clickLink('Dashboard')
                ->assertUrlIs(env('APP_URL').'/Dashboard');
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
}
