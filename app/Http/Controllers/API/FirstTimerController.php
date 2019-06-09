<?php

namespace App\Http\Controllers\API;

use App\FirstTimer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FirstTimerController extends Controller
{
    public function create(Request $request)
    {

        $firstTimer = new FirstTimer;
        $firstTimer->church_id = $request->church->id;
        $firstTimer->first_name = $request->first_name;
        $firstTimer->last_name = $request->last_name;
        $firstTimer->sex = $request->sex;
        $firstTimer->email = $request->email;
        $firstTimer->phone = $request->phone;
        $firstTimer->address = $request->address;
        $firstTimer->invited_by = $request->invited_by;
        $firstTimer->created_by = $request->user->id;

        if($firstTimer->save()) {
            return response()->json([
                'successMessage' => 'First timer created successfully',
                'firstTimer' => $firstTimer
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function update(Request $request)
    {
        $firstTimer = $request->firstTimer;

        $firstTimer->first_name = $request->first_name ?
        $request->first_name : $firstTimer->first_name;

        $firstTimer->last_name = $request->last_name ?
        $request->last_name : $firstTimer->last_name;

        $firstTimer->sex = $request->sex ?
        $request->sex : $firstTimer->sex;
        
        $firstTimer->email = $request->email ?
        $request->email : $firstTimer->email;  

        $firstTimer->phone = $request->phone ?
        $request->phone : $firstTimer->phone;  

        $firstTimer->address = $request->address ?
        $request->address : $firstTimer->address;  

        $firstTimer->invited_by = $request->invited_by ?
        $request->invited_by : $firstTimer->invited_by; 

        $firstTimer->updated_by = $request->user->id;


        if($firstTimer->save()) {
            return response()->json([
                'successMessage' => 'First updated successfully',
                'firstTimer' => $firstTimer
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function show(Request $request)
    {
        $firstTimer = $request->firstTimer;

        if($firstTimer) {
            return response()->json([
                'firstTimer' => $firstTimer
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function viewAll(Request $request)
    {
        $firstTimers = FirstTimer::where([
            'church_id' => $request->church->id,
        ])->get();


        if(!count($firstTimers)) {
            return response()->json([
                'errorMessage' => 'First timers can not be found'
            ], 404);
        }

        if($firstTimers) {
            return response()->json([
                'firstTimers' => $firstTimers
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function delete(Request $request, $firstTimerId)
    {
        $firstTimer = $request->firstTimer;

        $firstTimer->update([
            'deleted_by' => $request->user->id
        ]);
        
        if($firstTimer) {
            FirstTimer::destroy($firstTimerId);
            return response()->json([
                'successMessage' => 'First timer deleted successfully'
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }
    
}
