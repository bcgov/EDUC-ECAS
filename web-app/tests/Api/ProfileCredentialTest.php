<?php

namespace Tests\Api;


use App\Dynamics\Credential;
use App\Dynamics\ProfileCredential;
use Illuminate\Support\Collection;
use Tests\BaseMigrations;


class ProfileCredentialTest extends BaseMigrations
{


    /** @test */
    public function a_user_can_get_all_profile_credentials()
    {
        $mock_profile_id    = 'abc';
        $mock_federated_id  = '123';

        $this->mockUserId($mock_federated_id);

        $this->mockGetProfile($mock_profile_id, $this->validProfileData([
            'id'                    => $mock_profile_id,
            'federated_id'          => $mock_federated_id
        ]));

        $this->mockFilterProfileCredential($mock_profile_id, [
            [
                'contact_id'    => 'abc',
                'credential_id' => 'efg',
                'verified'      => 610410002 // not yet verified
            ],
            [
                'contact_id'    => 'abc',
                'credential_id' => 'mmm',
                'verified'      => 610410000 // verified yes
            ]
        ]);

        $response = $this->get('/api/' . $mock_profile_id . '/profile-credentials' );
        $response
            ->assertJsonFragment(['contact_id' => (string) $mock_profile_id])
            ->assertJsonCount(2);
    }


    /** @test */
    public function a_user_does_not_receive_credentials_verified_as_no()
    {
        $mock_profile_id    = 'abc';
        $mock_federated_id  = '123';

        $this->mockUserId($mock_federated_id);

        $this->mockGetProfile($mock_profile_id, $this->validProfileData([
            'id'                    => $mock_profile_id,
            'federated_id'          => $mock_federated_id
        ]));

        $this->mockFilterProfileCredential($mock_profile_id, [
            [
                'contact_id'    => 'abc',
                'credential_id' => 'efg',
                'verified'      => 610410002 // not yet verified
            ],
            [
                'contact_id'    => 'abc',
                'credential_id' => 'mmm',
                'verified'      => 610410001 // verified no
            ]
        ]);

        $response = $this->get('/api/' . $mock_profile_id . '/profile-credentials' );
        $response
            ->assertJsonFragment(['contact_id' => (string) $mock_profile_id])
            ->assertJsonCount(1);
    }


    /** @test */
    public function an_unauthenticated_user_cannot_get_any_profile_credentials()
    {
        $this->withExceptionHandling();

        $this->mockUserId(null);

        $response = $this->get('/api/' . 'an_unknown_profile_id' . '/profile-credentials' );
        $response->assertStatus(401); // unauthorised
    }

    /** @test */
    public function a_user_can_delete_a_profile_credential()
    {
        $mock_profile_id    = 'abc';
        $mock_federated_id  = '123';
        $record_id_to_be_deleted = 'efg';

        $this->mockUserId($mock_federated_id);

        $this->mockGetProfile($mock_profile_id, $this->validProfileData([
            'id'                    => $mock_profile_id,
            'federated_id'          => $mock_federated_id
        ]));

        $this->mockDeleteProfileCredential($record_id_to_be_deleted, [
            'id'            => $record_id_to_be_deleted,
            'contact_id'    => $mock_profile_id,
            'credential_id' => 'efg',
            'verified'      => 610410002 // not yet verified
        ]);

        $response = $this->delete('/api/' . $mock_profile_id . '/profile-credentials/' . $record_id_to_be_deleted  );

        $response->assertStatus(204 ); // success
    }


    /** @test */
    public function a_user_cannot_delete_another_users_profile_credential()
    {
        $mock_profile_id    = 'abc';
        $mock_federated_id  = '123';
        $record_id_to_be_deleted = 'efg';

        $this->mockUserId($mock_federated_id);

        $this->mockGetProfile($mock_profile_id, $this->validProfileData([
            'federated_id'          => $mock_federated_id
        ]));

        $this->mockCannotDeleteProfileCredential($record_id_to_be_deleted, [
            'id'            => $record_id_to_be_deleted,
            'contact_id'    => 'some_other_users_id',
            'credential_id' => 'efg',
            'verified'      => 610410002 // not yet verified
        ]);

        $response = $this->delete('/api/' . $mock_profile_id . '/profile-credentials/' . $record_id_to_be_deleted  );

        $response->assertStatus(401 ); // unauthorized
    }



    /** @test */
    public function a_user_can_create_a_profile_credential()
    {
        $mock_profile_id            = 'abc';
        $mock_federated_id          = '123';
        $mock_credential_id         = 'efg';
        $new_profile_credential     = [
            'contact_id'    => $mock_profile_id,
            'credential_id' => $mock_credential_id,
            'verified'      => 610410002, // all new records are assigned an 'unverified' status
            'year'          => null
        ];
        $new_profile_credential_id  = 'new_id';

        $this->mockUserId($mock_federated_id);

        $this->mockGetProfile($mock_profile_id, $this->validProfileData([
            'id'                    => $mock_profile_id,
            'federated_id'          => $mock_federated_id
        ]));

        $this->mockCreateProfileCredential($new_profile_credential_id, $new_profile_credential);

        $this->mockCreateCredential($mock_credential_id, [
            [ "id" => $mock_credential_id, 'name' => 'Credential One' ],
            [ "id"    => "aft", "name"  => "Some name" ]
        ],
        [ 'id'         => $mock_credential_id, 'name'       => 'Name of Credential'  ]);

        $response = $this->post('/api/' . $mock_profile_id . '/profile-credentials', $new_profile_credential );

        $response
            ->assertJsonFragment(['contact_id'      => (string) $mock_profile_id ])
            ->assertJsonFragment(['credential'      => [
                'id'        => (string) $mock_credential_id,
                'name'      => (string) 'Name of Credential'
            ]])
            ->assertJsonFragment(['verified'        => (boolean) false ]);
    }



    /** @test */
    public function a_user_cannot_create_a_profile_credential_with_a_verified_status()
    {

        $this->withoutMiddleware();

        $mock_profile_id            = 'abc';
        $mock_federated_id          = '123';
        $mock_credential_id         = 'b5f23b01-b06a-e911-b80a-005056833c5b';
        $new_profile_credential     = [
            'contact_id'    => $mock_profile_id,
            'credential_id' => $mock_credential_id,
            'verified'      =>  610410002, // all new records are assigned an 'unverified' status
            'year'          => null
        ];
        $new_profile_credential_id  = 'new_id';

        $this->mockUserId($mock_federated_id);

        $this->mockGetProfile($mock_profile_id, $this->validProfileData([
            'id'                    => $mock_profile_id,
            'federated_id'          => $mock_federated_id
        ]));

        $this->mockCreateProfileCredential($new_profile_credential_id, $new_profile_credential);

        $this->mockCreateCredential($mock_credential_id, [
            [ "id" => $mock_credential_id, 'name' => 'Credential One' ],
            [ "id"    => "aft", "name"  => "Some name" ]
        ],
            [ 'id'         => $mock_credential_id, 'name'       => 'Name of Credential'  ]);

        // Attempt to submit a `verified` credential
        $modified_profile_credential = $new_profile_credential;
        $modified_profile_credential['verified'] = ProfileCredential::$status['Yes'];

        $response = $this->post('/api/' . $mock_profile_id . '/profile-credentials', $new_profile_credential );

        $response
            ->assertJsonFragment(['verified'        => (boolean) false ]);
    }


    /** @test */
    public function a_user_cannot_update_a_profile_credential()
    {
        $this->withExceptionHandling();
        $mock_profile_id            = 'abc';

        $response = $this->patch('/api/'. $mock_profile_id .'/profile-credentials/' . 'any_profile_credential_id', []);
        $response->assertStatus(405); // route not available


    }

    /** @test */
    public function a_user_cannot_get_a_profile_credential()
    {
        $this->withExceptionHandling();
        $mock_profile_id            = 'abc';

        $response = $this->get('/api/'. $mock_profile_id .'/profile-credentials/' . 'any_profile_credential_id', []);
        $response->assertStatus(405); // route not available


    }

    private function mockFilterProfileCredential($profile_id, Array $data)
    {

        // mock the Profile Credential
        $repository = \Mockery::mock(ProfileCredential::class);
        $repository->shouldReceive('filter')
            ->with(['contact_id' => $profile_id])
            ->once()
            ->andReturn(collect($data));

        // load the mock into the IoC container
        $this->app->instance(ProfileCredential::class, $repository);

    }




    private function mockDeleteProfileCredential($profile_credential_id, Array $data)
    {

        // mock the Profile Credential
        $repository = \Mockery::mock(ProfileCredential::class);

        $repository->shouldReceive('get')
            ->with($profile_credential_id)
            ->once()
            ->andReturn($data)
            ->ordered();

        $repository->shouldReceive('delete')
            ->with($profile_credential_id)
            ->once()
            ->andReturn(TRUE )
            ->ordered();

        // load the mock into the IoC container
        $this->app->instance(ProfileCredential::class, $repository);

    }

    private function mockCannotDeleteProfileCredential($profile_credential_id, Array $data)
    {

        // mock the Profile Credential
        $repository = \Mockery::mock(ProfileCredential::class);

        $repository->shouldReceive('get')
            ->with($profile_credential_id)
            ->once()
            ->andReturn($data);

        // load the mock into the IoC container
        $this->app->instance(ProfileCredential::class, $repository);

    }


    private function mockCreateProfileCredential($new_record_id, Array $data)
    {

        $new_record                 = $data;
        $new_record['id']           = $new_record_id;

        // mock the Profile Credential
        $repository = \Mockery::mock(ProfileCredential::class);
        $repository->shouldReceive('create')
            ->with($data)
            ->once()
            ->andReturn($new_record_id)
            ->ordered();

        $repository->shouldReceive('get')
            ->with($new_record_id)
            ->once()
            ->andReturn($new_record)
            ->ordered();

        // load the mock into the IoC container
        $this->app->instance(ProfileCredential::class, $repository);

    }


    private function mockCreateCredential($credential_id, Array $all_data, Array $get_data )
    {

        // mock the Credential
        $repository = \Mockery::mock(Credential::class);
        $repository->shouldReceive('all')
            ->withNoArgs()
            ->once()
            ->andReturn(collect($all_data));

        $repository->shouldReceive('get')
            ->with($credential_id)
            ->once()
            ->andReturn($get_data);

        // load the mock into the IoC container
        $this->app->instance(Credential::class, $repository);

    }


    private function mockGetAllCredentials($return_data)
    {

        // mock the Credential
        $repository = \Mockery::mock(Credential::class);
        $repository->shouldReceive('all')
            ->once()
            ->andReturn($return_data);


        // load the mock into the IoC container
        $this->app->instance(Credential::class, $repository);

    }


}
