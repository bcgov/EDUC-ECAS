<?php

namespace Tests\Unit;

use App\Dynamics\Credential;
use App\Dynamics\Decorators\CacheDecorator;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\BaseMigrations;

class CredentialTest extends BaseMigrations
{

    public $api;
    public $credentials;

    public function setUp(): void
    {
        parent::setUp();

        $mock = new MockHandler([
            new Response(200, [], json_encode([ 'value' => [
                (object) [
                    "educ_credentialcodeid"     => "668def59-6b68-e911-b80a-005056833c5b",
                    "educ_name"                 => "Credentialed Literacy 12 I"
                ],
                (object) [
                    "educ_credentialcodeid"    => "668def59-6b68-e911-b80a-005056833c5i",
                    "educ_name"                => "Credentialed Literacy 11 I"
                ]
            ]
            ])),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->api = new Credential($client);

    }

    /** @test */
    public function get_credentials()
    {
        $credentials = $this->api->all();

        $this->assertInstanceOf('Illuminate\Support\Collection', $credentials);
        $this->verifySingle($credentials->first());
        
    }


    /** @test */
    public function get_cache_credentials()
    {
        $credentials = (new CacheDecorator($this->api))->all();

        $this->assertInstanceOf('Illuminate\Support\Collection', $credentials);
        $this->verifySingle($credentials->first());
    }



    
    private function verifySingle($result)
    {

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
    }


}
