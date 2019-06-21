<?php

namespace App\Http\Controllers\API;

use App\IncomeType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IncomeTypeController extends Controller
{

    public function create(Request $request)
    {
        $name = preg_replace('/\s+/', '__#__', $request->name);
        $group = substr_count($name, '__#__');

        $incomeType = new IncomeType;
        $incomeType->church_id = $request->church->id;
        $incomeType->name = $name;
        $incomeType->format = $request->format;
        $incomeType->currency = $request->currency;
        $incomeType->prize = $request->prize ? $request->prize : null;
        $incomeType->group = $group ? 1 : 0;
        $incomeType->created_by = $request->user->id;


        if($incomeType->save()) {
            return response()->json([
                'successMessage' => 'Income Type created successfully',
                'incomeType' => $incomeType
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function update(Request $request)
    {
        $incomeType = $request->incomeType;
        $name = $request->name ? preg_replace('/\s+/', '__#__', $request->name) :
        $incomeType->name;
        $group = substr_count($name, '__#__');

        $incomeType->name = $name;
        $incomeType->format = $request->format ? $request->format : $incomeType->format;
        $incomeType->currency = $request->currency ? $request->currency : $incomeType->currency;
        $incomeType->prize = $request->prize ? $request->prize : $incomeType->prize;
        $incomeType->group = $group ? 1 : 0;
        $incomeType->updated_by = $request->user->id;


        if($incomeType->save()) {
            return response()->json([
                'successMessage' => 'Income Type updated successfully',
                'incomeType' => $incomeType
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function show(Request $request)
    {
        if($request->incomeType) {
            return response()->json([
                'incomeType' => $request->incomeType
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function viewAll(Request $request)
    {
        $incomeTypes = $request->church->incomeTypes;

        if(!count($incomeTypes)) {
            return response()->json([
                'errorMessage' => 'Income types can not be found'
            ], 404);
        }

        if(count($incomeTypes)) {
            return response()->json([
                'incomeTypes' => $incomeTypes
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function typeIncomes(Request $request)
    {
        $typeIncomes = $request->incomeType->incomes;

        if(!count($typeIncomes)) {
            return response()->json([
                'errorMessage' => 'Incomes could not be found'
            ], 404);
        }

        if(count($typeIncomes)) {
            return response()->json([
                'typeIncomes' => $typeIncomes
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function delete(Request $request)
    {
        $incomeType = $request->incomeType;
        $incomeType->update([
            'deleted_by' => $request->user->id
        ]);

        $deleteIncome = IncomeType::destroy($incomeType->id);
        if($deleteIncome) {
            return response()->json([
                'successMessage' => 'Income Type deleted successfully'
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }



}
