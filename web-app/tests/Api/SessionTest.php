<?php

namespace Tests\Api;

use Tests\BaseMigrations;


class SessionTest extends BaseMigrations
{

    /** ---------------- TEST DISABLED ----------------------- */
    public function apply_to_session()
    {
        $this->post('/api/sessions', [
            'session_id' => '',
            'action' => 'apply'
        ])->assertOk();
    }


}
