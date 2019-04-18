<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-16
 * Time: 12:33 PM
 */

namespace App;

use App\Http\Resources\DistrictCollection;
use App\Http\Resources\DistrictResource;
use GuzzleHttp\Client;

class DynamicsRepository
{
    public $api;

    public $base_url = 'https://ecaswebapi.azurewebsites.net/api/operations';

    public function __construct()
    {
        $this->api = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->base_url,
            // You can set any number of default request options.
            'timeout'  => 5.0,
        ]);
    }

    public function getDistricts()
    {
        $table = 'educ_districtcodes';

        $fields = [
            'id'   => 'educ_districtcodeid',
            'name' => 'educ_name'
        ];

        return $this->queryAPI($table, $fields);
    }

    public function getProfile($id)
    {
        $table = 'contacts';

        $foreign_key = 'educ_federatedid'; // TODO: this field isn't populated yet, use the contact id for testing
        $foreign_key = 'contactid';

        $fields = [
            'preferred_first_name'           => 'educ_preferredfirstname',
            'first_name'                     => 'firstname',
            'last_name'                      => 'lastname',
            'email'                          => 'emailaddress1',
            'phone'                          => 'address1_telephone1',
            'sin'                            => 'educ_socialinsurancenumber',
            'address_1'                      => 'address1_line1',
            'address_2'                      => 'address1_line2',
            'city'                           => 'address1_city',
            'region'                         => 'address1_stateorprovince',
            'postal_code'                    => 'address1_postalcode',
            'district_id'                    => '_educ_district_value',
            'school'                         => 'educ_currentschool',
            'professional_certificate_bc'    => 'educ_professionalcertificatebc',
            'professional_certificate_yk'    => 'educ_professionalcertificateyk',
            'professional_certificate_other' => 'educ_professionalcertificateother',
            'payment'                        => 'educ_methodofpayment'
        ];

        $collection = $this->queryAPI($table, $fields, $foreign_key, $id);

        return current($collection);
    }

    public function createProfile()
    {
        $table = 'contacts';

        $data = [
            'firstname'                => 'Test',
            'lastname'                 => 'User',
            'emailaddress1'            => 'test2@example.com',
            'address1_telephone1'      => '2508123352',
            'address1_line1'           => 'test address',
            'address1_city'            => 'Victoria',
            'address1_stateorprovince' => 'BC',
            'address1_postalcode'      => 'V8V1J5',
        ];

        $query = $this->base_url . '?statement=' . $table;

        $response = $this->api->request('POST', $query, [
            'json' => $data
        ]);
        echo 'status: ' . $response->getStatusCode();
        dd($response->getBody()->getContents());
        // "contacts - https://ecasbc.api.crm3.dynamics.com/api/data/v9.1/contacts(cf7837ae-0862-e911-a983-000d3af42a5a) added"
        $data = json_decode($response->getBody()->getContents())->value;
        dd($data);
    }

    /**
     * @param $table
     * @param $fields
     * @return array
     */
    private function queryAPI($table, $fields, $foreign_key = null, $id = null): array
    {
        $query = 'statement=' . $table . '&$select=' . implode(',', $fields);

        if ($id) {
            $query .= '&$filter=' . $foreign_key . ' eq \'' . $id . '\'';
        }

        $response = $this->api->request('GET', $this->base_url, [
            'query' => $query
        ]);

        $data = json_decode($response->getBody()->getContents())->value;

        $collection = [];

        foreach ($data as $index => $row) {
            foreach ($fields as $local_field_name => $data_source_field_name) {
                $collection[$index][$local_field_name] = $row->$data_source_field_name;
            }
        }

        return $collection;
    }
}