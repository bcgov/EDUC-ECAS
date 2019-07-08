<?php

namespace Tests\Api;

use App\MockEntities\Assignment;
use Tests\BaseMigrations;


class AssignmentTest extends BaseMigrations
{

    private $assignments;
    private $user;

    public function setUp() : void
    {

        parent::setUp();
        $this->user = Factory(\App\User::class)->create();
        Factory(\App\MockEntities\ContractStage::class, 5)->create();
        Factory(\App\MockEntities\AssignmentStatus::class, 5)->create();
        Factory(\App\MockEntities\Session::class, 5)->create([
            'type_id'       => 1,
            'activity_id'   => 2,
        ]);
        Factory(\App\MockEntities\Role::class, 5)->create();

        $this->assignments = Factory(Assignment::class,2)->create([
            'user_id'       => $this->user->id
        ]);

    }


    /** @test */
    public function a_user_can_get_all_assignments()
    {
        $this->actingAs($this->user, 'api');

        $response = $this->get('/api/assignments' );
        $response
            ->assertJsonFragment(['user_id' => (string) $this->user->id])
            ->assertJsonCount(2);
    }


    /** @test */
    public function an_unauthenticated_user_cannot_get_all_assignments()
    {
        $this->withExceptionHandling();

        $response = $this->get('/api/assignments' );
        $response->assertStatus(302); // unauthorised
    }

    /** @test */
    public function a_user_can_delete_an_assignment()
    {
        $this->actingAs($this->user, 'api');

        $record_2b_deleted = $this->assignments->last();

        $response = $this->delete('/api/assignments/' . $record_2b_deleted->id  );

        $response->assertStatus(405 ); // method not implemented
    }


    /** @test */
    public function a_user_cannot_delete_another_users_assignments()
    {
        $another_user = Factory(\App\User::class)->create();
        $this->actingAs($another_user, 'api');

        $record_2b_deleted = $this->assignments->last();

        $response = $this->delete('/api/assignments/' . $record_2b_deleted->id  );

        $response->assertStatus(405 ); // method not implemented
    }



    /** @test */
    public function a_user_can_create_an_assignment()
    {
        $this->actingAs($this->user, 'api');

        $response = $this->post('/api/assignments', [
            'session_id'       => 2,
            'role_id'          => 1
        ] );

        $response
            ->assertJsonFragment(['user_id'         => (string) $this->user->id])
            ->assertJsonFragment(['session_id'   => '2']);
    }




}
