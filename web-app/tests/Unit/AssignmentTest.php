<?php

namespace Tests\Unit;

use App\Dynamics\Assignment;
use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\Profile;
use App\Dynamics\Session;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\BaseMigrations;

class AssignmentTest extends BaseMigrations
{

    public $api;
    public $assignments;

    public function setUp(): void
    {
        parent::setUp();

        $mock = new MockHandler([
            new Response(200, [], json_encode([ 'value' => [
                (object) [
                    "educ_assignmentid"     => "225762e7-b0c2-e911-b80d-005056833c5b",
                    "_educ_session_value"   => "668def59-6b68-e911-b80a-005056833c5b",
                    "_educ_contact_value"   => "6b3566c2-3ba3-e911-b80c-005056833c5b",
                    "_educ_role_value"      => null,
                    "educ_contractstage"    => 610410000,
                    "statuscode"            => 1,
                    "statecode"             => 0
                ],
                (object) [
                    "educ_assignmentid"     => "225762e7-b0c2-e911-b80d-005056833c5b",
                    "_educ_session_value"   => "668def59-6b68-e911-b80a-005056833c5b",
                    "_educ_contact_value"   => "6b3566c2-3ba3-e911-b80c-005056833c5b",
                    "_educ_role_value"      => null,
                    "educ_contractstage"    => 610410000,
                    "statuscode"            => 1,
                    "statecode"             => 0
                ]
            ]
            ])),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->api = new Assignment($client);


    }


    /** @test */
    public function get_all_assignments_from_api()
    {
        $results = $this->api->all();

        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());
    }


    /** @test */
    public function create_a_new_assignment_via_the_api()
    {
        $expected_response = 'b85d7fd4-dcc5-e911-b80d-005056833c5b';

        $mock = new MockHandler([
            new Response(200, [], $expected_response),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $api = new Assignment($client);


        $new_record_id = $api->create([
            'contact_id'    => '6b3566c2-3ba3-e911-b80c-005056833c5b',
            'session_id'    => '668def59-6b68-e911-b80a-005056833c5b'
        ]);

        $this->assertTrue($new_record_id == $expected_response );

    }


    /** @test */
    public function update_an_assignment_via_the_api()
    {
        $record_under_test = "225762e7-b0c2-e911-b80d-005056833c5b";
        $session_id         = "668def59-6b68-e911-b80a-005056833c5b";
        $contact_id         = "6b3566c2-3ba3-e911-b80c-005056833c5b";


        $mock = new MockHandler([
            new Response( 200, []),
            new Response(200, [], json_encode([ 'value' => [
                (object) [
                    "educ_assignmentid"     => $record_under_test,
                    "_educ_session_value"   => $session_id,
                    "_educ_contact_value"   => $contact_id,
                    "_educ_role_value"      => null,
                    "educ_contractstage"    => 610410000,
                    "statuscode"            => 1,
                    "statecode"             => 0
                ]
            ]
            ])),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $api = new Assignment($client);


        $updated_assignment = $api->update($record_under_test, [
            'contact_id'    => $contact_id,
            'session_id'    => $session_id
        ]);
        
        $this->verifySingle($updated_assignment);

    }

    /** @test */
    public function get_filtered_set_of_assignments_via_the_api()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([ 'value' => [
                (object) [
                    "educ_assignmentid"     => "225762e7-b0c2-e911-b80d-005056833c5b",
                    "_educ_session_value"   => "668def59-6b68-e911-b80a-005056833c5b",
                    "_educ_contact_value"   => "6b3566c2-3ba3-e911-b80c-005056833c5b",
                    "_educ_role_value"      => null,
                    "educ_contractstage"    => 610410000,
                    "statuscode"            => 1,
                    "statecode"             => 0
                ]
            ]
            ])),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $api = new Assignment($client);

        $result = $api->filter([ 'contact_id' => '6b3566c2-3ba3-e911-b80c-005056833c5b' ]);

        $this->assertInstanceOf('Illuminate\Support\Collection', $result);
        $this->verifySingle($result->first());

    }



    /** @test */
    public function get_all_assignments_from_api_cached()
    {
        $results = (new CacheDecorator($this->api))->all();

        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());
    }


    private function verifySingle($result)
    {

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('session_id', $result);
        $this->assertArrayHasKey('contact_id', $result);
        $this->assertArrayHasKey('role_id', $result);
        $this->assertArrayHasKey('contract_stage', $result);
        $this->assertArrayHasKey('status_id', $result);
        $this->assertArrayHasKey('state', $result);

    }




}
