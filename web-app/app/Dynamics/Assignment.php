<?php
/**
 * Created by PhpStorm.
 * User: dirk
 * Date: 2019-04-18
 * Time: 2:50 PM
 */

namespace App\Dynamics;

use App\Dynamics\Interfaces\iModelRepository;

class Assignment extends DynamicsRepository implements iModelRepository
{
    public static $table = 'educ_assignments';

    public static $primary_key = 'educ_assignmentid';

    const OPEN_STATUS       = 'Open';
    const APPLIED_STATUS    = 'Applied';
    const ACCEPTED_STATUS   = 'Accepted';
    const DECLINED_STATUS   = 'Declined';
    const WITHDREW_STATUS   = 'Withdrew';

    const INACTIVE_STATE = 1;

    public static $fields = [
        'id'             => 'educ_assignmentid',
        'session_id'     => '_educ_session_value',
        'contact_id'     => '_educ_contact_value',
        'role_id'        => '_educ_role_value',
        'contract_stage' => 'educ_contractstage',
        'status_id'      => 'statuscode',
        'state'          => 'statecode'
    ];

    public static $links = [
        'session_id'     => Session::class,
        'contact_id'     => Profile::class,
        'role_id'        => Role::class,
        'status'         => AssignmentStatus::class,
        'contract_stage' => ContractStage::class
    ];
}