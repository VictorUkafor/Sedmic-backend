<?php

namespace App\Http\Controllers\API;

use App\Unit;
use App\Aggregate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubController extends Controller
{
    
    // add sub to aggregate
    public function addSub(Request $request)
    {
        $aggregate = $request->aggregate;

        $sub = $aggregate->level == 1 ? 
        Unit::find($request->subId) : 
        Aggregate::find($request->subId);

        $sub->update([
            'aggregate_id' => $aggregate->id,
            'updated_by' => $request->user->id
        ]);
    

        if($sub){
            return response()->json([
                'successMessage' => 'Sub added successfully',
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    
    }


    // remove sub from aggregate
    public function removeSub(Request $request)
    {
        $aggregate = $request->aggregate;

        $sub = $aggregate->level == 1 ? 
        Unit::find($request->subId) : 
        Aggregate::find($request->subId);

        $sub->update([
        'aggregate_id' => NULL,
        'updated_by' => $request->user->id
        ]);

        if($sub){
            return response()->json([
                'successMessage' => 'Sub removed successfully',
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    
    }


}
