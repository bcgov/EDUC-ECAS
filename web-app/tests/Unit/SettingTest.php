<?php

namespace Tests\Feature;

use App\Dynamics\Assignment;
use App\Dynamics\AssignmentStatus;
use App\Dynamics\ContractStage;
use App\Dynamics\Credential;
use App\Dynamics\District;
use App\Dynamics\Health;
use App\Dynamics\Payment;
use App\Dynamics\Profile;
use App\Dynamics\ProfileCredential;
use App\Dynamics\Region;
use App\Dynamics\Role;
use App\Dynamics\School;
use App\Dynamics\Session;
use App\Dynamics\SessionActivity;
use App\Dynamics\SessionType;
use App\Dynamics\Settings;
use App\Dynamics\Subject;
use Tests\TestCase;

class SettingTest extends TestCase
{

    /** @test */
    public function get_settings()
    {
        $settings = Settings::all();

        $this->assertIsArray($settings);
    }


}
