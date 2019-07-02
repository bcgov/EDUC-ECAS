<?php

namespace Tests\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SessionTest extends TestCase
{

    /** @test */
    public function apply_to_session()
    {
        $this->post('/api/sessions', [
            'session_id' => '',
            'action' => 'apply'
        ])->assertOk();
    }


}
