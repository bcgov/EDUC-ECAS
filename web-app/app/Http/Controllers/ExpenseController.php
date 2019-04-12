<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request, $session_id)
    {
        // TODO: Get list of existing expenses for this session
        $expenses = [
            ['id' => 1, 'type' => 'Fee', 'description' => 'Marking Fees', 'amount' => 199.77, 'status' => 'Paid'],
            ['id' => 2, 'type' => 'Hotel', 'description' => '2 Night Hotel', 'amount' => 251.23, 'status' => 'Submitted']
        ];

        return view('expenses', [
            'expenses' => $expenses
        ]);
    }
}
