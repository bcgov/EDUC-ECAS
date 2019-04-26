<?php

namespace App\Http\Controllers;

use App\Assignment;
use App\Credential;
use App\District;
use App\School;
use App\Session;
use App\SessionActivity;
use App\SessionType;
use App\Subject;
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

    protected function loadDistricts()
    {
        $districts = District::get();
        usort($districts, function($a, $b) {
            return $a['name'] <=> $b['name'];
        });
        return $districts;
    }
    protected function loadSubjects()
    {
        $subjects = Subject::get();
        usort($subjects, function($a, $b) {
            return $a['name'] <=> $b['name'];
        });
        return $subjects;
    }

    protected function loadSchools()
    {
        $schools = School::get();
        usort($schools, function($a, $b) {
            return $a['name'] <=> $b['name'];
        });
        return $schools;
    }

    protected function loadActivities()
    {
        return SessionActivity::get();
    }

    protected function loadAssignments()
    {
        return Assignment::get();
    }

    protected function loadTypes()
    {
        return SessionType::get();
    }

    protected function loadCredentials()
    {
        $credentials = Credential::get();
        usort($credentials, function($a, $b) {
            return $a['name'] <=> $b['name'];
        });
        return $credentials;
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
        $sessions = Session::get();
        usort($sessions, function($a, $b) {
            return $a['start_date'] <=> $b['start_date'];
        });
        return $sessions;

//        return [
//            [
//                'id'       => '1',
//                'activity' => 'Exemplar',
//                'type'     => 'LIT 10 I',
//                'dates'    => 'August 1-2',
//                'location' => 'Vancouver',
//                'status'   => 'Scheduled'
//            ],
//            [
//                'id'       => '2',
//                'activity' => 'Marking',
//                'type'     => 'LIT 10 I',
//                'dates'    => 'August 3-4',
//                'location' => 'Vancouver',
//                'status'   => 'Invited'
//            ],
//            [
//                'id'       => '3',
//                'activity' => 'Marking',
//                'type'     => 'LIT 20 E',
//                'dates'    => 'July 3-4',
//                'location' => 'Victoria',
//                'status'   => 'Open'
//            ],
//            [
//                'id'       => '4',
//                'activity' => 'Marking',
//                'type'     => 'NUM 10',
//                'dates'    => 'July 10-12',
//                'location' => 'Kelowna',
//                'status'   => 'Open'
//            ]
//        ];
    }
}
