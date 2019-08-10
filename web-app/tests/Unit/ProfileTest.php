<?php

namespace Tests\Unit;

use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\District;
use App\Dynamics\Profile;
use App\Dynamics\School;
use Tests\BaseMigrations;
use Faker\Generator as Faker;

class ProfileTest extends BaseMigrations
{

    public $api;
    public $fake;
    public $profile;
    public $schools;
    public $districts;

    public function setUp(): void
    {
        parent::setUp();
        $this->api = new Profile();
        $this->fake = new \App\MockEntities\Repository\Profile(new \App\MockEntities\Profile());
        factory(\App\MockEntities\School::class, 1)->create();
        factory(\App\MockEntities\District::class, 1)->create();
        $this->profile = Factory(\App\MockEntities\Profile::class)->create();

        $this->districts = (new District())->all()->pluck('id')->toArray();
        $this->schools = (new School())->all()->pluck('id')->toArray();

    }


    /** @test */
    public function filter_for_a_single_profile_via_the_api()
    {

        $new_data = factory(\App\MockEntities\Profile::class)->make([
            'district_id'   => $this->randomElement($this->districts),
            'school_id'     => $this->randomElement($this->schools)
        ])->toArray();


        $new_record_id = $this->api->create($new_data);

        $singleProfile = $this->api->filter(['federated_id' => $new_data['federated_id']]);

        $this->verifySingle($singleProfile[0]);

    }


    /** @test */
    public function get_a_single_profile_via_the_api()
    {

        $result = $this->api->all()->first();

        $singleProfile = $this->api->get($result['id']);

        $this->assertTrue($result == $singleProfile);

    }



    /** @test */
    public function return_blank_profile_when_none_exists_via_the_api()
    {

        $new_federated_id   = 'aaaa-bbbb-cccc-dddd';
        $data               = [
            'first_name'    => 'Bob',
            'last_name'     => 'Smith',
            'email'         => 'bsmith@example.com',
        ];

        $singleProfile = $this->api->firstOrCreate($new_federated_id, $data);

        $this->assertTrue($singleProfile['federated_id']    == $new_federated_id );
        $this->assertTrue($singleProfile['first_name']      == $data['first_name'] );
        $this->assertTrue($singleProfile['last_name']       == $data['last_name'] );
        $this->assertTrue($singleProfile['email']           == $data['email'] );
    }


    /** @test */
    public function delete_a_single_profile_via_the_api()
    {

        $new_data = factory(\App\MockEntities\Profile::class)->make([
            'district_id'   => $this->randomElement($this->districts),
            'school_id'     => $this->randomElement($this->schools)
        ])->toArray();


        $new_record_id = $this->api->create($new_data);

        $result = $this->api->delete($new_record_id);

        $this->assertTrue($result);

    }



    /** @test */
    public function get_all_profiles_from_api()
    {
        $results = $this->api->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    /** @test */
    public function get_all_profiles_from_api_via_the_cache()
    {
        $results = (new CacheDecorator($this->api))->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }

    /** @test */
    public function get_all_fake_profiles_from_api()
    {
        $results = $this->fake->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }


    /** @test */
    public function get_all_fake_profiles_from_api_via_the_cache()
    {
        $results = (new CacheDecorator($this->fake))->all();
        $this->assertInstanceOf('Illuminate\Support\Collection', $results);
        $this->verifySingle($results->first());

    }



    private function verifySingle($result)
    {
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('first_name', $result);
        $this->assertArrayHasKey('preferred_first_name', $result);
        $this->assertArrayHasKey('last_name', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('phone', $result);
        $this->assertArrayHasKey('social_insurance_number', $result);
        $this->assertArrayHasKey('address_1', $result);
        $this->assertArrayHasKey('address_2', $result);
        $this->assertArrayHasKey('city', $result);
        $this->assertArrayHasKey('region', $result);
        $this->assertArrayHasKey('postal_code', $result);
        $this->assertArrayHasKey('district_id', $result);
        $this->assertArrayHasKey('school_id', $result);
        $this->assertArrayHasKey('professional_certificate_bc', $result);
        $this->assertArrayHasKey('professional_certificate_yk', $result);
        $this->assertArrayHasKey('professional_certificate_other', $result);

    }

    private function randomElement($array)
    {

        $index = array_rand($array);
        return $array[$index];

    }

}
