<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;

use App\Dynamics\Interfaces\iModelRepository;

class ProfileCredential extends DynamicsRepository implements iModelRepository
{
    public static $table = 'educ_credentials';

    public static $primary_key = 'educ_credentialid';

    public static $fields = [
        'id'            => 'educ_credentialid',
        'contact_id'    => '_educ_contact_value',
        'credential_id' => '_educ_credential_value',
        'verified'      => 'educ_verifiedcredential',
        'year'          => 'educ_credentialsyear'
    ];

    public static $links = [
        'contact_id'        => Profile::class,
        'credential_id'     => Credential::class
    ];

    public static $status = [
        'Yes'           => 610410000,
        'No'            => 610410001,
        'Unverified'    => 610410002
    ];

}