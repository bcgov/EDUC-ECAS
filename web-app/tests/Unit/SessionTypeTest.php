<?php

namespace Tests\Unit;


use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\SessionType;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\BaseMigrations;

class SessionTypeTest extends BaseMigrations
{


    public $api;
    public $fake;
    public $types;

    public function setUp(): void
    {
        parent::setUp();


        $mock = new MockHandler([
            new Response(200, [], json_encode([ 'value' => [
                (object) [
                    'educ_sessiontypecodeid'    => 'a1376a88-016b-e911-b80a-005056833c5b',
                    'educ_sessiontypecode'      => 'GLA 12 I',
                    'educ_name'                 => 'Graduation Literacy Assessment Immersion, 12'
                ],
                (object) [
                    'educ_sessiontypecodeid'    => 'a1376a88-016b-e911-b80a-005056833c5b',
                    'educ_sessiontypecode'      => 'GLA 11 I',
                    'educ_name'                 => 'Graduation Literacy Assessment Immersion, 11'
                ]
            ]
            ])),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->api = new SessionType($client);


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
