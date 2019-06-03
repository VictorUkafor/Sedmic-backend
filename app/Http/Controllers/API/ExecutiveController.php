<?php

namespace App\Http\Controllers\API;

use App\UnitExecutive;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExecutiveController extends Controller
{
    // add unit exco
    public function addUnitExco(Request $request)
    {
        $position = strtolower(preg_replace('/\s+/', ' ', $request->position));

        $unitExco = new UnitExecutive;
        $unitExco->unit_id = $request->unit->id;
        $unitExco->member_id = $request->excoId;
        $unitExco->position = $position;
        $unitExco->created_by = $request->user->id;

        if($unitExco->save()){
            return response()->json([
                'successMessage' => 'Exco added successfully',
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    // show unit excos
    public function unitExcos(Request $request)
    {
        $positions = $request->unit->executives;

        if(!count($positions)){
            return response()->json([
                'errorMessage' => 'No position',
            ], 404); 
        }

        if($positions){
            return response()->json([
                'positions' => $positions,
            ], 200); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    // remove unit exco
    public function removeUnitExco(Request $request)
    {
        $findPosition = $request->findPosition;
        $deleted = $findPosition->update([
            'deleted_by' => $request->user->id
        ]);

        if($deleted){
            UnitExecutive::destroy($findPosition->id);
            return response()->json([
                'successMessage' => 'Exco removed successfully',
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }
}
