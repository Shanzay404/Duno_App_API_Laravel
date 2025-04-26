<?php

namespace App\Http\Controllers\Budget;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Expense;
use Illuminate\Support\Facades\Validator;

class BudgetController extends Controller
{
    public function create(Request $request)
    {
        try{
            $validateFields=Validator::make(
                $request->all(),
                [
                    'name' => 'required|string',
                    'amount' => 'required|numeric|min:1',
                    'icon' => 'required|file|mimes:png,jpg,jpeg,gif|max:2048',
                    'duration' => 'required|date|date_format:Y-m-d',
                ],
                [
                   'duration.date_format' => "Enter a date in y-m-d format",
                ]
            );
            if($validateFields->fails())
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation Error',
                    'errors' => $validateFields->errors()->first()
                ], 401);
            }
    
            $icon = $request->icon;
            $extension=$icon->getClientOriginalExtension();
            $icon_name=time().".".$extension;
            $icon->move(public_path().'/iconUploads', $icon_name);
            $budget=Budget::create([
                'name' => $request->name,
                'amount' => $request->amount,
                'icon' => $icon_name,
                'duration' => $request->duration,
            ]);
            return response()->json([
                'status' => true,
                'message' => "Budget Has been Created...",
                'budget'=> $budget,
            ],201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went\'s wrong, please try again',
                'errors' => $e->getMessage()
            ],500);
        }
    }


    public function viewReport(Request $request, $id)
    {
        try{
            
            $validateInputs=Validator::make(
                $request->all(),
                [
                    "budget_id" => 'required|numeric',
                ]
            );
            if($validateInputs->fails())
            {
                return response()->json([
                    'status' => false,
                    'message' => "validation Error",
                    'error' => $validateInputs->errors()->first(),
                ],401);
            }

            $budget=Budget::where('id',$id)->select('id','name','amount')->first();
            $budgetTotalAmount = $budget->amount;


            $totalExpensesAmount = Expense::where('budget_id', $id)->sum('expensePrice');
            return response()->json([
                'status' => true,
                'budget' => $budget,
                'total budget' => $budgetTotalAmount,
                'total Expenses' => $totalExpensesAmount,
            ],201); 

        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went\'s wrong, please try again',
                'errors' => $e->getMessage()
            ],500);
        }

    }
}
