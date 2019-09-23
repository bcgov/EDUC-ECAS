<?php

namespace Tests\Unit;

use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\Session;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\BaseMigrations;

class SessionTest extends BaseMigrations
{
    
    public $api;
    public $sessions;

    public function setUp(): void
    {
        parent::setUp();

        $mock = new MockHandler([
            new Response(200, [], json_encode([ 'value' => [
                (object) [
                    'educ_sessionid'                => '74462934-ac72-e911-b80a-005056833c5b',
                    '_educ_sessionactivity_value'   => '3816434a-b26a-e911-b80a-005056833c5b',
                    '_educ_sessiontype_value'       => 'a7376a88-016b-e911-b80a-005056833c5b',
                    'educ_startdate'                => '2019-05-09T07:00:00Z',
                    'educ_enddate'                  => '2019-05-11T07:00:00Z',
                    'educ_locationname'             => 'Langley Secondary School',
                    'educ_locationaddress'          => '1234 Walnut Street',
                    'educ_locationcity'             => 'Langley',
                    'statecode'                     => 1,
                    'statuscode'                    => 1,
                    'educ_publishsession'           => True
                ],
                (object) [
                    'educ_sessionid'                => '84462934-ac72-e911-b80a-005056833c5b',
                    '_educ_sessionactivity_value'   => '4816434a-b26a-e911-b80a-005056833c5b',
                    '_educ_sessiontype_value'       => 'a8376a88-016b-e911-b80a-005056833c5b',
                    'educ_startdate'                => '2019-05-09T07:00:00Z',
                    'educ_enddate'                  => '2019-05-11T07:00:00Z',
                    'educ_locationname'             => 'Burnaby Secondary School',
                    'educ_locationaddress'          => '1234 Burnaby Street',
                    'educ_locationcity'             => 'Burnaby',
                    'statecode'                     => 1,
                    'statuscode'                    => 1,
                    'educ_publishsession'           => True
                ]
            ]
            ])),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->api = new Session($client);

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
        $this->assertArrayHasKey('activity_id', $result);
        $this->assertArrayHasKey('type_id', $result);
        $this->assertArrayHasKey('start_date', $result);
        $this->assertArrayHasKey('end_date', $result);
        $this->assertArrayHasKey('location', $result);
        $this->assertArrayHasKey('address', $result);
        $this->assertArrayHasKey('city', $result);

    }


}
