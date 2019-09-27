<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;

use App\Dynamics\Interfaces\iModelRepository;


class Profile extends DynamicsRepository implements iModelRepository
{
    public static $table = 'contacts';

    public static $primary_key = 'contactid';

    public static $data_bind = 'educ_Contact';

    public static $fields = [

        'id'                             => 'contactid',
        'federated_id'                   => 'educ_federatedid',
        'preferred_first_name'           => 'educ_preferredfirstname',
        'username'                       => 'educ_federatedusername',
        'first_name'                     => 'firstname',
        'last_name'                      => 'lastname',
        'email'                          => 'emailaddress1',
        'phone'                          => 'address1_telephone1',
        'social_insurance_number'        => 'educ_socialinsurancenumber',
        'address_1'                      => 'address1_line1',
        'address_2'                      => 'address1_line2',
        'city'                           => 'address1_city',
        'region'                         => 'address1_stateorprovince',
        'country_id'                     => '_educ_countryid_value',
        'postal_code'                    => 'address1_postalcode',
        'district_id'                    => '_educ_district_value',
        'school_id'                      => '_educ_currentschoold_value',  // note '..schoold..' is not a typo
        'professional_certificate_bc'    => 'educ_professionalcertificatebc',
        'professional_certificate_yk'    => 'educ_professionalcertificateyk',
    ];

    public static $links = [
        'district_id'   => District::class,
        'school_id'     => School::class,
        'country_id'    => Country::class
    ];


    public static $filter_quote = '\'';

    public function firstOrCreate($federated_id, $data)
    {
        $existing = $this->filter([ 'federated_id' => $federated_id ]);

        if (count($existing) == 0) {

            return [
                'id'                             => null,
                'federated_id'                   => $federated_id,
                'first_name'                     => $data['first_name'],
                'last_name'                      => $data['last_name'],
                'email'                          => $data['email'],
            ];

        }

        return $existing[0];

    }

    /*
     * Read data from Dynamics
     */
    public function get($id)
    {

        $query = env('DYNAMICSBASEURL') . '/' . static::$api_verb . '?statement=' . static::$table . '&$select=' . implode(',', static::$fields);

        $query .= '&$filter=' . static::$primary_key . " eq " .  $id;

        $collection = $this->retrieveData($query);

        return current($collection)[0];

    }


}