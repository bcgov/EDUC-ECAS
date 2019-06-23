<?php


namespace App\Dynamics\Cache;


use App\Dynamics\Interfaces\iFullCRUD;

class SessionType extends Base implements iFullCRUD
{

    public static $duration = 480; // Cache duration in minutes



}