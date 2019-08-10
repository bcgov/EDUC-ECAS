<?php

namespace Tests\Unit;

use App\Dynamics\Assignment;
use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\Profile;
use App\Dynamics\Session;
use Tests\BaseMigrations;

class AssignmentTest extends BaseMigrations
{

    public $api;
    public $fake;
    public $assignments;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = new Assignment();
        $this->fake = new \App\MockEntities\Repository\Assignment(new \App\MockEntities\Assignment());

        $this->assignments = factory(\App\MockEntities\Assignment::class, 5)->create([
            'contact_id'        => 1,
            'role_id'           => 1,
            'session_id'        => 1,
            'contract_stage'    => 1,
            'status'            => 1
        ]);
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
        $profile = (new Profile())->all()->first();
        $session = (new Session())->all()->first();

        $new_record_id = $this->createAnAssignment($profile['id'],$session['id']);

        $results = $this->api->get($new_record_id);

        $this->verifySingle($results);

    }

    /** @test */
    public function delete_an_assignment_via_the_api()
    {
        $profile = (new Profile())->all()->first();
        $session = (new Session())->all()->first();

        $new_record_id = $this->createAnAssignment($profile['id'],$session['id']);

        $result = $this->api->delete($new_record_id);

        $this->assertTrue($result);

    }


//    /** @test */
//    public function delete_jonathans_duplicate_assignments()
//    {
//        $assignments = $this->api->filter(['contact_id' => "a9dbd5d8-3db3-e911-b80d-005056833c5b" ]);
//
//        $result = $this->api->delete($assignments->first()['id']);
//
//        dd($result, $assignments->count());
//
//        $this->assertTrue($result);
//
//    }


    /** @test */
    public function update_an_assignment_via_the_api()
    {
        $profile = (new Profile())->all()->first();
        $session = (new Session())->all()->first();

        $new_record_id = $this->createAnAssignment($profile['id'],$session['id']);

        $new_assignment = $this->api->update($new_record_id, [
            'contact_id'    => $profile['id'],
            'session_id'    => $session['id']
        ]);

        // clean up as this test will have likely messed up an existing assignment record in Dynamics
        $this->api->delete($new_record_id);

        $this->verifySingle($new_assignment);

    }

    /** @test */
    public function get_filtered_set_of_assignments_via_the_api()
    {
        
        $expected = $this->api->all()->last();
        
        $result = $this->api->filter([ 'contact_id' => $expected['contact_id'] ]);

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

    /** @test */
    public function get_all_assignments_from_fake_local_database()
    {

        $results = $this->fake->all();


        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());
    }


    /** @test */
    public function get_all_assignments_from_fake_local_database_then_cached()
    {

        $results = (new CacheDecorator($this->fake))->all();

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
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('state', $result);

    }

    private function createAnAssignment($contact_id, $session_id)
    {
        return $this->api->create([
            'contact_id'    => $contact_id,
            'session_id'    => $session_id
        ]);

    }


}
