<?php

namespace Tests\Api;

use App\MockEntities\Assignment;
use Faker\Factory;
use Tests\BaseMigrations;


class AssignmentTest extends BaseMigrations
{

    private $assignments;
    private $user;
    private $profile;

    public function setUp() : void
    {

        parent::setUp();


        $this->user = Factory(\App\User::class)->create();
        $this->profile = Factory(\App\MockEntities\Profile::class)->create([
            'federated_id'  => $this->user->id
        ]);
        Factory(\App\MockEntities\Session::class, 5)->create([
            'type_id'       => 1,
            'activity_id'   => 2,
        ]);
        $this->assignments = Factory(Assignment::class,2)->create([
            'contact_id'    => $this->profile->id
        ]);

    }


    /** @test */
    public function a_user_can_get_all_assignments()
    {
        $this->actingAs($this->user, 'api');

        $response = $this->get('/api/' . $this->profile->id .'/assignments' );

        $response
            ->assertJsonFragment(['contact_id' => (string) $this->profile->id])
            ->assertJsonCount(2);
    }


    /** @test */
    public function an_unauthenticated_user_cannot_get_all_assignments()
    {
        $this->withExceptionHandling();

        $response = $this->get( '/api/' . $this->profile->id .'/assignments' );
        $response->assertStatus(302); // unauthorised
    }

    /** @test */
    public function a_user_can_delete_an_assignment()
    {
        $this->actingAs($this->user, 'api');

        $record_2b_deleted = $this->assignments->last();

        $response = $this->delete('/api/' . $this->profile->id .'/assignments/' . $record_2b_deleted->id  );

        $response->assertStatus(405 ); // method not implemented
    }


    /** @test */
    public function a_user_cannot_delete_another_users_assignments()
    {
        $another_user = Factory(\App\User::class)->create();
        $this->actingAs($another_user, 'api');

        $record_2b_deleted = $this->assignments->last();

        $response = $this->delete( '/api/' . $this->profile->id .'/assignments/' . $record_2b_deleted->id  );

        $response->assertStatus(405 ); // method not implemented
    }



    /** @test */
    public function a_user_can_create_an_assignment()
    {
        $this->actingAs($this->user, 'api');

        $response = $this->post( '/api/' . $this->profile->id .'/assignments' , [
            'session_id'       => 2,
            'role_id'          => 1
        ] );

        $response
            ->assertJsonFragment(['contact_id'   => (string) $this->profile->id])
            ->assertJsonFragment(['session_id'   => '2']);
    }




}
