<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function loadUser()
    {
        // Mock querying Dynamics for a User
        return [
            'id'                          => 1,
            'email'                       => 'jane.smith@example.com',
            'preferred_first_name'        => 'Sal',
            'first_name'                  => 'Sally',
            'last_name'                   => 'Sherwood',
            'social_insurance_no'         => '123456789',
            'professional_certificate_bc' => 'bd-aejrkqwehr',
            //            'professional_certificate_yk'    => 'yk-039290',
            //            'professional_certificate_other' => '',
            'current_school'              => 'South Park Elementary',
            'address_1'                   => '456 Yellow Brick Rd.',
            'address_2'                   => '',
            'city'                        => 'Oz',
            'region'                      => 'BC',
            'postal_code'                 => 'T0B4T5'
        ];
    }

    protected function loadSubjects()
    {
        return [
            '1'  => 'Learning Assistance Teacher',
            '2'  => 'Learning Specialist Teacher',
            '3'  => 'Learning Support Teacher',
            '4'  => 'Learning Support Teacher',
            '5'  => 'Special Education Teacher',
            '6'  => 'Intervention Teacher',
            '7'  => 'Inclusion Support Teacher',
            '8'  => 'Resource Teacher',
            '9'  => 'Teacher-Librarian',
            '10' => 'Counsellor',
            '11' => 'Homeroom Teacher',
            '12' => 'Academic Support Teacher',
            '13' => 'Language and Cultural Teacher',
            '14' => 'Language Coordinator',
            '15' => 'Language Teacher',
            '16' => 'Adult Education Teacher',
            '17' => 'Vice-Principal',
            '18' => 'Principal',
            '19' => 'Teacher on Call',
            '20' => 'French Immersion Teacher',
            '21' => 'Literacy Teacher',
            '22' => 'Numeracy Teacher',
            '23' => 'Middle School Generalist',
            '24' => 'Primary Teacher',
            '25' => 'Intermediate Teacher',
            '26' => 'Elementary Teacher',
            '27' => 'Junior Secondary Teacher',
            '28' => 'Senior Secondary Teacher',
            '29' => 'Secondary Teacher',
            '30' => 'ELL Teacher',
        ];
    }

    protected function loadSchools()
    {
        return [
            '1' => 'South Park Elementary',
            '2' => 'Ridgemont High',
            '3' => 'Northridge Junior High',
            '4' => 'St. Augustine',
            '5' => 'George Jay Elementary'
        ];
    }

    protected function loadCredentials()
    {
        return [
            [
                'id'   => '1',
                'name' => 'Literacy 10 E'
            ],
            [
                'id'   => '2',
                'name' => 'Literacy 10 P'
            ],
            [
                'id'   => '3',
                'name' => 'Numeracy 10'
            ]
        ];
    }

    protected function loadRegions()
    {
        return [
            'BC',
            'YK'
        ];
    }

    protected function loadSessions()
    {
        return [
            [
                'id'       => '1',
                'activity' => 'Exemplar',
                'type'     => 'LIT 10 I',
                'dates'    => 'August 1-2',
                'location' => 'Vancouver',
                'status'   => 'Scheduled'
            ],
            [
                'id'       => '2',
                'activity' => 'Marking',
                'type'     => 'LIT 10 I',
                'dates'    => 'August 3-4',
                'location' => 'Vancouver',
                'status'   => 'Invited'
            ],
            [
                'id'       => '3',
                'activity' => 'Marking',
                'type'     => 'LIT 20 E',
                'dates'    => 'July 3-4',
                'location' => 'Victoria',
                'status'   => 'Open'
            ],
            [
                'id'       => '4',
                'activity' => 'Marking',
                'type'     => 'NUM 10',
                'dates'    => 'July 10-12',
                'location' => 'Kelowna',
                'status'   => 'Open'
            ]
        ];
    }
}
