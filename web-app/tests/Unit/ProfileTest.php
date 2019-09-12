<?php

namespace Tests\Unit;

use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\District;
use App\Dynamics\Profile;
use App\Dynamics\School;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\BaseMigrations;
use Faker\Generator as Faker;

class ProfileTest extends BaseMigrations
{

    public $api;
    public $profile;
    public $schools;
    public $districts;

    public function setUp(): void
    {
        parent::setUp();

        $mock = new MockHandler([
            new Response(200, [], json_encode(['value' => [
                (object)[
                    'contactid' => '6b3566c2-3ba3-e911-b80c-005056833c5b',
                    'educ_federatedid' => '6b3566c2-3ba3-e911-b80c',
                    'educ_preferredfirstname' => 'Vic',
                    'firstname' => 'Victor',
                    'lastname' => 'Dantas',
                    'emailaddress1' => 'dantas_victor@hotmail.com',
                    'address1_telephone1' => '4035551212',
                    'educ_socialinsurancenumber' => '999999111',
                    'address1_line1' => '675 ARBOUR LAKE DR NW',
                    'address1_line2' => '',
                    'address1_city' => 'Calgary',
                    'address1_stateorprovince' => 'AB',
                    'address1_postalcode' => 'V8R4N4',
                    '_educ_district_value' => '8ca7af66-b06a-e911-b80a-005056833c5b',
                    'educ_currentschool' => '8585001',
                    'educ_professionalcertificatebc' => 'AB1235',
                    'educ_professionalcertificateyk' => 'XX2345',
                    'educ_professionalcertificateother' => 'BC5556678',
                ]
            ]
            ])),
            new Response(200, [], json_encode(['value' => [
                (object)[
                    'contactid' => '6b3566c2-3ba3-e911-b80c-005056833c5b',
                    'educ_federatedid' => '6b3566c2-3ba3-e911-b80c',
                    'educ_preferredfirstname' => 'Vic',
                    'firstname' => 'Victor',
                    'lastname' => 'Dantas',
                    'emailaddress1' => 'dantas_victor@hotmail.com',
                    'address1_telephone1' => '4035551212',
                    'educ_socialinsurancenumber' => '999999111',
                    'address1_line1' => '675 ARBOUR LAKE DR NW',
                    'address1_line2' => '',
                    'address1_city' => 'Calgary',
                    'address1_stateorprovince' => 'AB',
                    'address1_postalcode' => 'V8R4N4',
                    '_educ_district_value' => '8ca7af66-b06a-e911-b80a-005056833c5b',
                    'educ_currentschool' => '8585001',
                    'educ_professionalcertificatebc' => 'AB1235',
                    'educ_professionalcertificateyk' => 'XX2345',
                    'educ_professionalcertificateother' => 'BC5556678',
                ]
            ]
            ])),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->api = new Profile($client);

    }

    public function filter_for_a_single_profile_via_the_api()
    {

        $singleProfile = $this->api->filter(['federated_id' => 'some_federated_id']);

        $this->verifySingle($singleProfile[0]);

    }


    /** @test */
    public function get_a_single_profile_via_the_api()
    {

        $singleProfile = $this->api->get('some_profile_id');

        $this->verifySingle($singleProfile);

    }



    /** @test */
    public function return_blank_profile_when_none_exists_via_the_api()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['value' =>[] ]))

        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->api = new Profile($client);


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

    }

    private function randomElement($array)
    {

        $index = array_rand($array);
        return $array[$index];

    }

}
