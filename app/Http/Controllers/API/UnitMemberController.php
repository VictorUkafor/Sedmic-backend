<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UnitMemberController extends Controller
{
    
    // add member to unit
    public function addMember(Request $request)
    {
        $unit = $request->unit;
        $member = $request->member;

        $unit->members()->attach($member);
        $unit->members = $unit->members;

        if($unit){
            return response()->json([
                'successMessage' => 'Member added successfully',
                'unit' => $unit,
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    
    }


    // remove member from unit
    public function removeMember(Request $request)
    {
        $unit = $request->unit;
        $member = $request->member;

        $unit->members()->detach($member);
        $unit->members = $unit->members;

        if($unit){
            return response()->json([
                'successMessage' => 'Member removed successfully',
                'unit' => $unit,
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    
    }


}
