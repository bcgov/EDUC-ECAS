<?php

namespace Tests\Unit;

use App\Dynamics\Assignment;
use App\Dynamics\AssignmentStatus;
use App\Dynamics\Credential;
use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\District;
use App\Dynamics\Profile;
use App\Dynamics\ProfileCredential;
use App\Dynamics\School;
use App\Dynamics\Session;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\BaseMigrations;

class ProfileCredentialTest extends BaseMigrations
{

    public $api;
    public $profile_credentials;

    public function setUp(): void
    {
        parent::setUp();

        $mock = new MockHandler([
            new Response(200, [], json_encode([ 'value' => [
                (object) [
                    'educ_credentialid'         => '9aa6aa7b-91bc-e911-b80d-005056833c5b',
                    '_educ_contact_value'       => 'ea186595-8ebc-e911-b80d-005056833c5b',
                    '_educ_credential_value'    => 'aff23b01-b06a-e911-b80a-005056833c5b',
                    'educ_verifiedcredential'   => 610410000,
                    'educ_credentialsyear'      => 2020,

                ],
                (object) [
                    'educ_credentialid'         => '4aa6aa7b-91bc-e911-b80d-005056833c5b',
                    '_educ_contact_value'       => 'va186595-8ebc-e911-b80d-005056833c5b',
                    '_educ_credential_value'    => 'fff23b01-b06a-e911-b80a-005056833c5b',
                    'educ_verifiedcredential'   => 610410000,
                    'educ_credentialsyear'      => 2020
                ]
            ]
            ])),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->api = new ProfileCredential($client);

    }


    /** @test */
    public function get_all_credentials_from_api()
    {
        $results = $this->api->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    /** @test */
    public function get_filtered_set_of_credentials_via_the_api()
    {

        $record_under_test = 'ea186595-8ebc-e911-b80d-005056833c5b';

        $mock = new MockHandler([
            new Response(200, [], json_encode([ 'value' => [
                (object) [
                    'educ_credentialid'         => '9aa6aa7b-91bc-e911-b80d-005056833c5b',
                    '_educ_contact_value'       => $record_under_test,
                    '_educ_credential_value'    => 'aff23b01-b06a-e911-b80a-005056833c5b',
                    'educ_verifiedcredential'   => 610410000,
                    'educ_credentialsyear'      => 2019
                ],
                (object) [
                    'educ_credentialid'         => '4aa6aa7b-91bc-e911-b80d-005056833c5b',
                    '_educ_contact_value'       => $record_under_test,
                    '_educ_credential_value'    => 'fff23b01-b06a-e911-b80a-005056833c5b',
                    'educ_verifiedcredential'   => 610410000,
                    'educ_credentialsyear'      => 2019
                ]
            ]
            ])),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $credentials = new ProfileCredential($client);

        $filtered = $credentials->filter([ 'contact_id' => $record_under_test]);

        $this->assertInstanceOf('Illuminate\Support\Collection', $filtered);
        $this->verifySingle($filtered->first());

    }


    /** @test */
    public function create_a_new_credential_via_the_api()
    {
        $expected_response = 'b85d7fd4-dcc5-e911-b80d-005056833c5b';

        $mock = new MockHandler([
            new Response(200, [], $expected_response),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $credentials = new ProfileCredential($client);

        $new_record_id = $credentials->create([
            'contact_id'    => 'ea186595-8ebc-e911-b80d-005056833c5b',
            'credential_id' => 'aff23b01-b06a-e911-b80a-005056833c5b',
            'verified'      => 610410002
        ]);

        $this->assertTrue($new_record_id == $expected_response );


    }


    /** @test */
    public function delete_a_credential_via_the_api()
    {
        $mock = new MockHandler([
            new Response(200, []),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $credentials = new ProfileCredential($client);

        $result = $credentials->delete('some_record_id');

        $this->assertTrue($result->getStatusCode() == 200);

    }


    /** @test */
    public function get_all_credentials_from_api_via_the_cache()
    {
        $results = (new CacheDecorator($this->api))->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    private function verifySingle($result)
    {

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('contact_id', $result);
        $this->assertArrayHasKey('credential_id', $result);
        $this->assertArrayHasKey('verified', $result);
        $this->assertArrayHasKey('year', $result);


    }




}
