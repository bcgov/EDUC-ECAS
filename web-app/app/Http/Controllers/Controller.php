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
            'phone'                       => '2508123353',
            'social_insurance_no'         => '123456789',
            'professional_certificate_bc' => 'bd-aejrkqwehr',
            //            'professional_certificate_yk'    => 'yk-039290',
            //            'professional_certificate_other' => '',
            'school'                      => '1',
            'district'                    => '1',
            'address_1'                   => '456 Yellow Brick Rd.',
            'address_2'                   => '',
            'city'                        => 'Oz',
            'region'                      => 'BC',
            'postal_code'                 => 'T0B4T5',
        ];
    }

    protected function loadSubjects()
    {
        return [
            ['id' => '1', 'name' => 'Learning Assistance Teacher'],
            ['id' => '2', 'name' => 'Learning Specialist Teacher'],
            ['id' => '3', 'name' => 'Learning Support Teacher'],
            ['id' => '4', 'name' => 'Learning Support Teacher'],
            ['id' => '5', 'name' => 'Special Education Teacher'],
            ['id' => '6', 'name' => 'Intervention Teacher'],
            ['id' => '7', 'name' => 'Inclusion Support Teacher'],
            ['id' => '8', 'name' => 'Resource Teacher'],
            ['id' => '9', 'name' => 'Teacher-Librarian'],
            ['id' => '10', 'name' => 'Counsellor'],
            ['id' => '11', 'name' => 'Homeroom Teacher'],
            ['id' => '12', 'name' => 'Academic Support Teacher'],
            ['id' => '13', 'name' => 'Language and Cultural Teacher'],
            ['id' => '14', 'name' => 'Language Coordinator'],
            ['id' => '15', 'name' => 'Language Teacher'],
            ['id' => '16', 'name' => 'Adult Education Teacher'],
            ['id' => '17', 'name' => 'Vice-Principal'],
            ['id' => '18', 'name' => 'Principal'],
            ['id' => '19', 'name' => 'Teacher on Call'],
            ['id' => '20', 'name' => 'French Immersion Teacher'],
            ['id' => '21', 'name' => 'Literacy Teacher'],
            ['id' => '22', 'name' => 'Numeracy Teacher'],
            ['id' => '23', 'name' => 'Middle School Generalist'],
            ['id' => '24', 'name' => 'Primary Teacher'],
            ['id' => '25', 'name' => 'Intermediate Teacher'],
            ['id' => '26', 'name' => 'Elementary Teacher'],
            ['id' => '27', 'name' => 'Junior Secondary Teacher'],
            ['id' => '28', 'name' => 'Senior Secondary Teacher'],
            ['id' => '29', 'name' => 'Secondary Teacher'],
            ['id' => '30', 'name' => 'ELL Teacher'],
        ];
    }

    protected function loadSchools()
    {
        return [
            ['id' => '1', 'name' => 'South Park Elementary'],
            ['id' => '2', 'name' => 'Ridgemont High'],
            ['id' => '3', 'name' => 'Northridge Junior High'],
            ['id' => '4', 'name' => 'St. Augustine'],
            ['id' => '5', 'name' => 'George Jay Elementary']
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
            ['code' => 'BC', 'name' => 'British Columbia'],
            ['code' => 'YK', 'name' => 'Yukon']
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
