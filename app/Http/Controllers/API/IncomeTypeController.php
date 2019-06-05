<?php

namespace App\Http\Controllers\API;

use App\IncomeType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IncomeTypeController extends Controller
{

    public function create(Request $request)
    {
        $name = strtolower(preg_replace('/\s+/', ' ', $request->name));
        $group = substr_count($name, '__#__');

        $incomeType = new IncomeType;
        $incomeType->church_id = $request->church->id;
        $incomeType->name = $name;
        $incomeType->format = $request->format;
        $incomeType->currency = $request->currency;
        $incomeType->prize = $request->prize ? $request->prize : null;
        $incomeType->group = $group;
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
        $name = $request->name ? strtolower(preg_replace('/\s+/', ' ', $request->name)) :
        $incomeType->name;
        $group = substr_count($name, '__#__');

        $incomeType->name = $name;
        $incomeType->format = $request->format ? $request->format : $incomeType->format;
        $incomeType->currency = $request->currency ? $request->currency : $incomeType->currency;
        $incomeType->prize = $request->prize ? $request->prize : $incomeType->prize;
        $incomeType->group = $group;
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
        $incomeType = $request->incomeType;

        if($incomeType) {
            return response()->json([
                'incomeType' => $incomeType
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function viewAll(Request $request)
    {
        $incomeTypes = IncomeType::where([
            'church_id' => $request->church->id,
        ])->get();


        if(!$incomeTypes) {
            return response()->json([
                'errorMessage' => 'Income types can not be found'
            ], 404);
        }

        if($incomeTypes) {
            return response()->json([
                'incomeTypes' => $incomeTypes
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function delete(Request $request, $incomeTypeId)
    {
        $incomeType = $request->incomeType;
        $incomeType->update([
            'deleted_by' => $request->user->id
        ]);
        
        if($incomeType) {
            IncomeType::destroy($incomeTypeId);
            return response()->json([
                'successMessage' => 'Income Type deleted successfully'
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }



}
