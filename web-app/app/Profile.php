<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App;

class Profile extends DynamicsRepository
{
    public static $table = 'contacts';

    // TODO: this field isn't populated yet, use the contact id for testing
//    public $primary_key = 'educ_federatedid';
    public static $primary_key = 'contactid';

    public static $data_bind = 'educ_Contact';

    public static $fields = [
        'id'                             => 'contactid',
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
        //        'district_id'                    => 'educ_districtcode_contact',
        'school_id'                      => 'educ_currentschool',
        'professional_certificate_bc'    => 'educ_professionalcertificatebc',
        'professional_certificate_yk'    => 'educ_professionalcertificateyk',
        'professional_certificate_other' => 'educ_professionalcertificateother',
    ];

    public static $links = [
        'district_id' => District::class
    ];
}