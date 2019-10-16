<?php

namespace Tests\Unit;


use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\SessionActivity;
use Tests\BaseMigrations;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class SessionActivitiesTest extends BaseMigrations
{

    public $api;
    public $sessionActivities;

    public function setUp(): void
    {
        parent::setUp();
        $mock = new MockHandler([
            new Response(200, [], json_encode([ 'value' => [
                (object) [
                    "educ_sessionactivitycodeid"    => "3e16434a-b26a-e911-b80a-005056833c5b",
                    "educ_sessionactivitycode"      => "PRP",
                    "educ_name"                     => "Prep marking site"
                ],
                (object) [
                    "educ_sessionactivitycodeid"    => "3c16434a-b26a-e911-b80a-005056833c5b",
                    "educ_sessionactivitycode"      => "MON",
                    "educ_name"                     => "Monitoring"
                ]
            ]
            ])),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->api = new SessionActivity($client);

    }

    /** @test */
    public function get_all_records_from_api()
    {
        $results = $this->api->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    /** @test */
    public function get_all_records_from_api_via_the_cache()
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
        $this->assertArrayHasKey('code', $result);

    }

}
