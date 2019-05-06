<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;

class ProfileCredential extends DynamicsRepository
{
    public static $table = 'educ_credentials';

    public static $primary_key = 'educ_credentialid';

    public static $cache = 0; // Do Not Cache

    public static $fields = [
        'id'            => 'educ_credentialid',
        'user_id'       => '_educ_contact_value',
        'credential_id' => '_educ_credential_value',
        'verified'      => 'educ_verified'
    ];

    public static $links = [
        'user_id'       => Profile::class,
        'credential_id' => Credential::class
    ];
}