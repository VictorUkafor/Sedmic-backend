<?php

namespace App\Http\Controllers\API;

use App\Income;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IncomeController extends Controller
{
    public function create(Request $request)
    {

        $incomeType = $request->incomeType;

        $income = new Income;
        $income->church_id = $request->church->id;
        $income->income_type_id = $incomeType->id;
        
        $income->programme_id = $request->programme ? 
        $request->programme->id : null;

        $income->title = $request->programme ? 
        $request->programme->title.' - income' :
        $request->church->name_of_church.' - income';

        $income->type = $incomeType->name;
        $income->format = $incomeType->format;
        $income->default_currency = $incomeType->currency;
        $income->prize = $incomeType->prize;
        $income->group = $incomeType->group; 

        $income->paid_currency = $request->currency;
        $income->amount = $request->amount;
        $income->member_id = $request->member;
        $income->first_timer_id = $request->first_timer;
        $income->slip_id = $request->slip;
        $income->cash = $request->cash;
        $income->created_by = $request->user->id;


        if($income->save()) {
            return response()->json([
                'successMessage' => 'Income created successfully',
                'income' => $income
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function update(Request $request)
    {
        $income = $request->income;

        if($income->cash) {
            return response()->json([
                'errorMessage' => 'Income can not be deleted'
            ], 401);
        }

        $amount = $request->amount ?
        $request->amount : $income->amount;

        $income->amount = $income->cash ?
        $income->amount : $amount;

        $income->member_id = $request->member ? 
        $request->member : $income->member_id;

        $income->first_timer_id = $request->first_timer ? 
        $request->first_timer : $income->first_timer_id;

        $income->slip_id = $request->slip ? 
        $request->slip : $income->slip_id;        

        $income->paid_currency = $request->currency ?
        $request->currency : $income->currency;

        $income->updated_by = $request->user->id;

        if($income->save()) {
            return response()->json([
                'successMessage' => 'Income updated successfully',
                'income' => $income
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function show(Request $request)
    {
        if($request->income) {
            return response()->json([
                'income' => $request->income
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function viewAll(Request $request)
    {
        $incomes = $request->church->incomes;

        if(!count($incomes)) {
            return response()->json([
                'errorMessage' => 'Incomes can not be found'
            ], 404);
        }

        if(count($incomes)) {
            return response()->json([
                'incomes' => $incomes
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function createdIncomes(Request $request)
    {
        $incomes = $request->church->incomes()
        ->where('created_by', $request->user->id)->get();

        if(!count($incomes)) {
            return response()->json([
                'errorMessage' => 'Incomes can not be found'
            ], 404);
        }

        if(count($incomes)) {
            return response()->json([
                'incomes' => $incomes
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function delete(Request $request)
    {
        $income = $request->income;

        if($income->cash) {
            return response()->json([
                'errorMessage' => 'Income can not be deleted'
            ], 401);
        }

        $income->update([
            'deleted_by' => $request->user->id
        ]);

        $deleteIncome = Income::destroy($income->id);
        if($deleteIncome) {
            return response()->json([
                'successMessage' => 'Income deleted successfully'
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


}
