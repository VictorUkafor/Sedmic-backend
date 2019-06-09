<?php

namespace App\Http\Controllers\API;

use App\Income;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IncomeController extends Controller
{
    public function create(Request $request)
    {

        $income = new Income;
        $income->church_id = $request->church->id;
        $income->type = $request->type;
        $income->format = $request->format;
        $income->amount = $request->amount;
        $income->member = $request->member;
        $income->default_currency = $request->default_currency;
        $income->paid_currency = $request->paid_currency;
        $income->prize = $request->prize;
        $income->group = $request->group;
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

        $income->type = $request->type ? 
        $request->type : $income->type;

        $income->format = $request->format ? 
        $request->format : $income->format;

        $income->amount = $request->amount ? 
        $request->amount : $income->amount;

        $income->member = $request->member ? 
        $request->member : $income->member;

        $income->default_currency = $request->default_currency ?
        $request->default_currency : $income->default_currency;

        $income->paid_currency = $request->paid_currency ?
        $request->paid_currency : $income->paid_currency;

        $income->prize = $request->prize ? 
        $request->prize : $income->prize;

        $income->group = $request->group ? 
        $request->group : $income->group;

        $income->cash = $request->cash ? 
        $request->cash : $income->cash;

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
        $income = $request->income;

        if($income) {
            return response()->json([
                'income' => $income
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function viewAll(Request $request)
    {
        $incomes = Income::where([
            'church_id' => $request->church->id,
        ])->get();


        if(!$incomes) {
            return response()->json([
                'errorMessage' => 'Income types can not be found'
            ], 404);
        }

        if($incomes) {
            return response()->json([
                'incomes' => $incomes
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function delete(Request $request, $incomeId)
    {
        $income = $request->income;
        $income->update([
            'deleted_by' => $request->user->id
        ]);
        
        if($income) {
            Income::destroy($incomeId);
            return response()->json([
                'successMessage' => 'Income deleted successfully'
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


}
