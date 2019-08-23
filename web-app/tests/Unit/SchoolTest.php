<?php

namespace Tests\Unit;

use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\School;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\BaseMigrations;

class SchoolTest extends BaseMigrations
{



    public $api;
    public $schools;

    public function setUp(): void
    {
        parent::setUp();

        $mock = new MockHandler([
            new Response(200, [], json_encode([ 'value' => [
                (object) [
                        'educ_schoolcode'   => '2345678',
                        'educ_name'         => 'Collingwood School',
                        'educ_schoolcity'   => 'West Vancouver',
                    ],
                (object) [
                        'educ_schoolcode'   => '2345698',
                        'educ_name'         => 'Collingwood School',
                        'educ_schoolcity'   => 'West Vancouver',
                    ]
                ]
            ]))
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->api = new School($client);

    }


    /** @test */
    public function get_all_schools_from_api()
    {

        $results = $this->api->all();

        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    /** @test */
    public function the_api_can_filter_by_school_name()
    {
        $results = $this->api->filterContains([ 'name' => 'Collingwood']);

        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->assertTrue(count($results) == 2 );
        $this->assertTrue($results[0]['name'] == 'Collingwood School');

    }


    /** @test */
    public function get_all_schools_from_api_via_the_cache()
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
        $this->assertArrayHasKey('city', $result);

    }

 }
