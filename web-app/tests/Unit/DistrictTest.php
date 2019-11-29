<?php

namespace Tests\Unit;

use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\District;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\BaseMigrations;

class DistrictTest extends BaseMigrations
{


    public $api;
    public $districts;

    public function setUp(): void
    {
        parent::setUp();

        $mock = new MockHandler([
            new Response(200, [], json_encode([ 'value' => [
                (object) [
                    'educ_districtcodeid'       => '2345678',
                    'educ_districtnamenumber'   => 'Victoria(73)'
                ],
                (object) [
                    'educ_districtcodeid'       => '2345679',
                    'educ_districtnamenumber'   => 'Campbell River(72)',
                ]
            ]
            ])),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->api = new District($client);

    }


    /** @test */
    public function get_all_districts_from_api()
    {
        $results = $this->api->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }

    /** @test */
    public function the_api_can_filter_by_district_name()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([ 'value' => [
                (object) [
                    'educ_districtcodeid'       => '2345679',
                    'educ_districtnamenumber'   => 'Campbell River(72)',
                ]
            ]
            ])),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $districts = new District($client);

        $results = $districts->filterContains([ 'name' => 'Camp']);

        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->assertTrue(count($results) == 1 );
        $this->assertTrue($results[0]['name'] == 'Campbell River(72)');

    }


    /** @test */
    public function get_all_districts_from_api_via_the_cache()
    {
        $results = (new CacheDecorator($this->api))->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }

    private function verifySingle($result)
    {
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);

    }

}
