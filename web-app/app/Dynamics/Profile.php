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
        'first_name'                     => 'firstname',
        'last_name'                      => 'lastname',
        'email'                          => 'emailaddress1',
        'phone'                          => 'address1_telephone1',
        'social_insurance_number'        => 'educ_socialinsurancenumber',
        'address_1'                      => 'address1_line1',
        'address_2'                      => 'address1_line2',
        'city'                           => 'address1_city',
        'region'                         => 'address1_stateorprovince',
        'postal_code'                    => 'address1_postalcode',
        'district_id'                    => '_educ_district_value',
        'school_id'                      => 'educ_currentschool',
        'professional_certificate_bc'    => 'educ_professionalcertificatebc',
        'professional_certificate_yk'    => 'educ_professionalcertificateyk',
        'professional_certificate_other' => 'educ_professionalcertificateother',
    ];

    public static $links = [
        'district_id' => District::class
    ];


    public static $filter_quote = '\'';


    public function firstOrCreate($federated_id, $data)
    {
        $existing = $this->filter([ 'federated_id' => $federated_id ]);

        if (count($existing) == 0) {

            return [
                'federated_id'                   => $federated_id,
                'id'                             => null,
                'preferred_first_name'           => null,
                'first_name'                     => $data['first_name'],
                'last_name'                      => $data['last_name'],
                'email'                          => $data['email'],
                'phone'                          => null,
                'social_insurance_number'        => null,
                'address_1'                      => null,
                'address_2'                      => null,
                'city'                           => null,
                'region'                         => 'BC',
                'postal_code'                    => null,
                'district_id'                    => null,
                'school_id'                      => null,
                'professional_certificate_bc'    => null,
                'professional_certificate_yk'    => null,
                'professional_certificate_other' => null,
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