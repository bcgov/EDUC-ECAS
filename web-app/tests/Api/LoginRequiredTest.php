<?php

namespace Tests\Api;

use App\Dynamics\Assignment;
use Tests\BaseMigrations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;


class LoginRequiredTest extends BaseMigrations
{

    const PROFILE_ID    = '11111111-1111-1111-1111-111111111111';
    const RECORD_ID     = '22222222-2222-2222-2222-222222222222';


    public static function endpoints()
    {
        return [
            'dashboard'             => ['GET',    '/api/dashboard'],
            'get profile'           => ['GET',    '/api/profiles/' . self::PROFILE_ID],
            'create profile'        => ['POST',   '/api/profiles'],
            'update profile'        => ['PATCH',  '/api/profiles/' . self::PROFILE_ID],
            'list credentials'      => ['GET',    '/api/' . self::PROFILE_ID . '/profile-credentials'],
            'add credential'        => ['POST',   '/api/' . self::PROFILE_ID . '/profile-credentials'],
            'delete credential'     => ['DELETE', '/api/' . self::PROFILE_ID . '/profile-credentials/' . self::RECORD_ID],
            'list assignments'      => ['GET',    '/api/' . self::PROFILE_ID . '/assignments'],
            'create assignment'     => ['POST',   '/api/' . self::PROFILE_ID . '/assignments'],
            'update assignment'     => ['PATCH',  '/api/' . self::PROFILE_ID . '/assignments/' . self::RECORD_ID],
        ];
    }


    #[Test]
    #[DataProvider('endpoints')]
    public function an_unauthenticated_user_is_rejected($method, $uri)
    {
        $this->withExceptionHandling();
        $this->mockUnauthenticatedUser();
        $this->mockEcasApi();

        $this->json($method, $uri, $this->validBody())->assertStatus(401);

        $this->assertEmpty($this->ecas_api_requests);
    }


    #[Test]
    #[DataProvider('endpoints')]
    public function a_token_keycloak_rejects_is_rejected($method, $uri)
    {
        $this->withExceptionHandling();
        $this->mockKeycloakRejectingToken();
        $this->mockEcasApi();

        $this->json($method, $uri, $this->validBody())->assertStatus(401);

        $this->assertEmpty($this->ecas_api_requests);
    }


    public static function searches()
    {
        return [
            'districts'     => ['/api/districts?q=Van'],
            'schools'       => ['/api/schools?q=Second'],
        ];
    }


    #[Test]
    #[DataProvider('searches')]
    public function a_search_with_a_token_keycloak_rejects_is_forbidden($uri)
    {
        $this->withExceptionHandling();
        $this->mockKeycloakRejectingToken();
        $this->mockEcasApi();

        $this->json('GET', $uri)->assertStatus(403);

        $this->assertEmpty($this->ecas_api_requests);
    }


    // profile and assignment updates validate the body before the controller runs
    private function validBody()
    {
        return $this->validProfilePostData([
            'country_id'    => 'some_guid',
            'action'        => Assignment::APPLIED_STATUS
        ]);
    }

}
