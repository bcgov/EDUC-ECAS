<?php

namespace App\Http\Controllers\Fictitious;


use App\MockEntities\Payment;


class PaymentTypeController extends BaseController
{

    public function __construct(Payment $model)
    {
        $this->model = $model;
    }



}
