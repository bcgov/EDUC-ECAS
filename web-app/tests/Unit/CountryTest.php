<?php

namespace Tests\Unit;

use App\Dynamics\Country;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\BaseMigrations;

class CountryTest extends BaseMigrations
{

    public $api;

    public function setUp(): void
    {
        parent::setUp();

        $mock = new MockHandler([
            new Response(200, [], json_encode([ 'value' => [
                (object) [
                    'educ_countrycodeid'    => '2345678',
                    'educ_name'             => 'Canada'
                ]
            ]
            ])),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->api = new Country($client);
    }


    /** @test */
    public function get_a_single_country_via_the_api()
    {
        $result = $this->api->get('2345678');
        $this->assertSame(['id' => '2345678', 'name' => 'Canada'], $result);
    }

}
