<?php

namespace App\Http\Controllers\Expense;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    public function create(Request $request)
    {
        try{
            $validateFields=Validator::make(
                $request->all(),
                [
                    'expenseName' => 'required|string',
                    'expensePrice' => 'required|numeric|min:1',
                    'budget_id' => 'required|integer',
                    'date' => 'required|date|date_format:Y-m-d',
                ]
            );

            if($validateFields->fails())
            {
                return response()->json([
                    'status' => false,
                    'message' => "Validation Error",
                    'error' => $validateFields->errors()->first(),
                ],401);
            }

            $expense=Expense::create([
                'expenseName' => $request->expenseName,
                'expensePrice' => $request->expensePrice,
                'budget_id' => $request->budget_id,
                'date' => $request->date,
            ]);

            return response()->json([
                'status' => True,
                'message' => "Expense has been Added.",
                'expense' => $expense,
            ],201);

        }
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went\'s wrong, please try again',
                'errors' => $e->getMessage()
            ],500);
          }
    }

    
}
