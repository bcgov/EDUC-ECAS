<?php

namespace Tests\Api;

use App\Dynamics\Assignment;
use App\Dynamics\AssignmentStatus;
use App\Dynamics\ContractStage;
use App\Dynamics\Role;
use Tests\BaseMigrations;


class AssignmentTest extends BaseMigrations
{


    /** @test */
    public function a_user_can_request_all_assignments()
    {
        $mock_profile_id    = 'abc';
        $mock_federated_id  = '123';

        $this->mockUserId($mock_federated_id);

        $this->mockGetProfile($mock_profile_id, $this->validProfileData([
            'id'                    => $mock_profile_id,
            'federated_id'          => $mock_federated_id
        ]));

        $this->mockFilterAssignments($mock_profile_id, [
            [
                'id'             => 'educ_assignmentid',
                'session_id'     => '_educ_session_value',
                'contact_id'     => $mock_profile_id,
                'role_id'        => '_educ_role_value',
                'contract_stage' => 'educ_contractstage',
                'status'         => 'statuscode',
                'state'          => 'statecode'
            ],
            [
                'id'             => 'educ_assignmentid',
                'session_id'     => '_educ_session_value',
                'contact_id'     => $mock_profile_id,
                'role_id'        => '_educ_role_value',
                'contract_stage' => 'educ_contractstage',
                'status'         => 'statuscode',
                'state'          => 'statecode'
            ]
        ]);


        $response = $this->get('/api/' . $mock_profile_id .'/assignments' );

        $response
            ->assertJsonFragment(['contact_id' => (string) $mock_profile_id])
            ->assertJsonCount(2);
    }


    /** @test */
    public function an_unauthenticated_user_cannot_get_all_assignments()
    {
        $this->withExceptionHandling();
        $mock_profile_id    = 'abc';

        $this->mockUserId(null);

        $response = $this->get('/api/'. $mock_profile_id .'/assignments' );
        $response->assertStatus(401); // unauthorized

    }

    /** @test */
    public function a_user_cannot_delete_an_assignment()
    {

        $this->withExceptionHandling();
        $mock_profile_id            = 'abc';

        $response = $this->delete('/api/' . $mock_profile_id .'/assignments/' . 'any_assignment_id'  );

        $response->assertStatus(405 ); // method not implemented
    }



    /** @test */
    public function a_user_can_create_an_assignment()
    {
        $mock_profile_id            = 'abc';
        $mock_federated_id          = '123';
        $role_id                    = 'nbc';
        $contract_stage             = 'defg';
        $assignment_status          = 'hijk';
        $state                      = 'lmno';

        $new_assignment             = [
            'contact_id'            =>  $mock_profile_id,
            'session_id'            =>  'new_session_id',
            'role_id'               =>  $role_id,
            'stage'                 =>  $contract_stage,
            'assignment_status'     =>  $assignment_status,
            'state'                 =>  $state
        ];

        $this->mockUserId($mock_federated_id);

        $this->mockGetProfile($mock_profile_id, $this->validProfileData([
            'id'                    => $mock_profile_id,
            'federated_id'          => $mock_federated_id
        ]));

        $this->mockCreateAssignment('new_assignment_id', $new_assignment);
        $this->mockGetRole($role_id, 'Some role name');
        $this->mockGetContractStage($contract_stage, 'Some contract_stage');
        $this->mockGetAssignmentStatus($assignment_status, 'Some assignment status');

        $response = $this->post( '/api/' . $mock_profile_id .'/assignments' , $new_assignment);

        $response
            ->assertJsonFragment(['contact_id'   => (string) $mock_profile_id ])
            ->assertJsonFragment(['session_id'   => $new_assignment['session_id']]);
    }

    protected function mockFilterAssignments($profile_id, Array $data = [] )
    {

        // mock the Profile
        $repository = \Mockery::mock(Assignment::class);
        $repository->shouldReceive('filter')
            ->with([
                'contact_id'    => $profile_id
            ])
            ->once()
            ->andReturn($data);

        // load the mock into the IoC container
        $this->app->instance(Assignment::class, $repository);

    }


    protected function mockCreateAssignment($new_assignment_id, Array $data = [] )
    {
        $new_assignment             = $data;
        $new_assignment['id']       = $new_assignment_id;

        // mock the Profile
        $repository = \Mockery::mock(Assignment::class);

        $repository->shouldReceive('create')
            ->with($data)
            ->once()
            ->andReturn($new_assignment_id)
            ->ordered();

        $repository->shouldReceive('get')
            ->with($new_assignment_id)
            ->once()
            ->andReturn($new_assignment)
            ->ordered();

        // load the mock into the IoC container
        $this->app->instance(Assignment::class, $repository);

    }


    protected function mockGetRole($role_id, $role_name )
    {
        $data['id']     = $role_id;
        $data['name']   = $role_name;
        $data['rate']   = 340;

        // mock the Role
        $repository = \Mockery::mock(Role::class);
        $repository->shouldReceive('get')
            ->with($role_id)
            ->once()
            ->andReturn($data);

        // load the mock into the IoC container
        $this->app->instance(Role::class, $repository);

    }


    protected function mockGetContractStage($contact_stage, $name )
    {
        $data['id']     = $contact_stage;
        $data['name']   = $name;

        // mock the Role
        $repository = \Mockery::mock(ContractStage::class);
        $repository->shouldReceive('get')
            ->with($contact_stage)
            ->once()
            ->andReturn($data);

        // load the mock into the IoC container
        $this->app->instance(ContractStage::class, $repository);

    }


    private function mockGetAssignmentStatus($assignment_status, $name )
    {
        $data['id']     = $assignment_status;
        $data['name']   = $name;

        // mock the Role
        $repository = \Mockery::mock(AssignmentStatus::class);
        $repository->shouldReceive('get')
            ->with($assignment_status)
            ->once()
            ->andReturn($data);

        // load the mock into the IoC container
        $this->app->instance(AssignmentStatus::class, $repository);

    }


}
