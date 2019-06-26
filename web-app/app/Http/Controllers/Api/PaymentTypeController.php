<?php

namespace App\Http\Controllers\Api;


use App\Dynamics\Payment;
use App\Http\Controllers\Interfaces\iApiController;

class PaymentTypeController extends BaseController implements iApiController
{

    public function __construct(Payment $model)
    {
        $this->model = $model;
    }


}
