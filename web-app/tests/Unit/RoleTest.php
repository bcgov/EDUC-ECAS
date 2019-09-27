<?php

namespace Tests\Unit;

use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\Role;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\BaseMigrations;

class RoleTest extends BaseMigrations
{


    public $api;
    public $roles;

    public function setUp(): void
    {
        parent::setUp();
        $mock = new MockHandler([
            new Response(200, [], json_encode([ 'value' => [
                (object) [
                    "educ_rolecodeid"    => "3d13348b-b16a-e911-b80a-005056833c5b",
                    "educ_rolerate"      => "250",
                    "educ_name"          => "Team Member"
                ],
                (object) [
                    "educ_rolecodeid"   => "3c16434a-b26a-e911-b80a-005056833c5b",
                    "educ_rolerate"     => "325",
                    "educ_name"         => "Team Lead"
                ]
            ]
            ])),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->api = new Role($client);

    }


    /** @test */
    public function get_all_role_records_from_api()
    {
        $results = $this->api->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    /** @test */
    public function get_all_role_records_from_api_via_the_cache()
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
        $this->assertArrayHasKey('rate', $result);

    }


}
