<?php

namespace Tests\Feature;

use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\Payment;

use Tests\TestCase;

class PaymentOptionListTest extends TestCase
{

    public $api;
    public $fake;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = new Payment();
        $this->fake = new \App\MockEntities\Repository\Payment(new \App\MockEntities\Payment());
    }



    /** @test */
    public function get_all_records_from_api()
    {
        $results = $this->api->all();
        $this->verifyCollection($results);
        $this->verifySingle($results[0]);

    }


    /** @test */
    public function get_all_records_from_api_via_the_cache()
    {
        $results = (new CacheDecorator($this->api))->all();
        $this->verifyCollection($results);
        $this->verifySingle($results[0]);

    }


    /** @test */
    public function get_all_fake_records_from_api()
    {
        // TODO - remove toArray() below - collection
        $results = $this->fake->all()->toArray();
        $this->verifyCollection($results);
        $this->verifySingle($results[0]);

    }


    /** @test */
    public function get_all_fake_records_from_api_via_the_cache()
    {
        // TODO - remove toArray() below - collection
        $results = (new CacheDecorator($this->fake))->all()->toArray();
        $this->verifyCollection($results);
        $this->verifySingle($results[0]);

    }



    private function verifyCollection($results)
    {
        $this->assertIsArray($results);

    }

    private function verifySingle($result)
    {
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);

    }



}
