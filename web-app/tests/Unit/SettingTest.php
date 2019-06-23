<?php

namespace Tests\Feature;


use App\Dynamics\Settings;
use Tests\TestCase;

class SettingTest extends TestCase
{

    /** @test */
    public function all_settings()
    {
        $settings = Settings::all();

        $this->assertIsArray($settings);
    }


}
