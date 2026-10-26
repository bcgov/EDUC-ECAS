<?php

namespace Tests\Unit;

use App\Keycloak\KeycloakProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Laravel\Socialite\Facades\Socialite;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class KeycloakProviderTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $_SERVER['KEYCLOAK_AUTHSERVERURL']  = 'https://keycloak.test/auth';
        $_SERVER['KEYCLOAK_REALM']          = 'ecas';
    }

    public function tearDown(): void
    {
        unset($_SERVER['KEYCLOAK_AUTHSERVERURL'], $_SERVER['KEYCLOAK_REALM']);

        parent::tearDown();
    }


    #[Test]
    public function socialite_builds_the_keycloak_driver()
    {
        $this->assertInstanceOf(KeycloakProvider::class, Socialite::driver('keycloak'));
    }

    #[Test]
    public function the_bearer_token_is_sent_to_the_keycloak_userinfo_endpoint()
    {
        $requests = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200, [], json_encode(['sub' => 'abc-123'])),
        ]));
        $stack->push(Middleware::history($requests));

        $provider = Socialite::driver('keycloak');
        $provider->setHttpClient(new Client(['handler' => $stack]));

        $this->assertSame(['sub' => 'abc-123'], $provider->getUserByToken('some-token'));
        $this->assertSame(
            'https://keycloak.test/auth/realms/ecas/protocol/openid-connect/userinfo?prettyPrint=false',
            (string) $requests[0]['request']->getUri()
        );
        $this->assertSame('Bearer some-token', $requests[0]['request']->getHeaderLine('Authorization'));
    }

}
