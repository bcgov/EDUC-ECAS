<?php

namespace Tests\Unit;

use App\Dynamics\AssignmentStatus;

use App\Dynamics\Decorators\CacheDecorator;
use Faker\Factory;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\BaseMigrations;


class AssignmentStatusTest extends BaseMigrations
{

    public $api;

    public function setUp(): void
    {

        parent::setUp();

        $mock = new MockHandler([
            new Response(200, [], json_encode([ 'Options' => [
                (object) [
                    "Id" => 610410005,
                    "Label" => "Completed"
                ],
                (object) [
                    "Id" => 610410006,
                    "Label" => "Withdrew"
                ]
            ]
            ])),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->api = new AssignmentStatus($client);

    }

    /** @test */
    public function get_all_assignment_statuses_from_api()
    {
        $results = $this->api->all();

        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());
    }

    /** @test */
    public function get_all_assignment_statuses_from_api_cached()
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
