<?php

namespace Tests\Feature;

use App\Dynamics\Payment;

use Tests\TestCase;

class PaymentOptionListTest extends TestCase
{

    /** @test */
    public function get_payment_option_list()
    {
        $result = Payment::all();

        $this->assertIsArray($result);
        $this->assertIsArray($result[0]);
        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('name', $result[0]);
    }


}
