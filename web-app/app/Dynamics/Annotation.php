<?php

namespace App\Dynamics;

use App\Dynamics\Interfaces\iModelRepository;

/*
 * A file (note) attached to a Dynamics record, read to find the record it belongs to
 */
class Annotation extends DynamicsRepository implements iModelRepository
{
    public static $table = 'annotations';

    public static $primary_key = 'annotationid';

    public static $fields = [
        'id'        => 'annotationid',
        'object_id' => '_objectid_value'
    ];
}
