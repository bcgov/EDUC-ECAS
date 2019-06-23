<?php


namespace App\Dynamics\Cache;


use App\Dynamics\Interfaces\iFullCRUD;

class Assignment extends Base implements iFullCRUD
{

    public static $duration = 5; // Cache duration in minutes


}