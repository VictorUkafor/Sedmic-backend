<?php

namespace App\Http\Controllers\API;

use App\Slip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SlipController extends Controller
{
    public function create(Request $request)
    {

        $slip = new Slip;
        $slip->church_id = $request->church->id;
        $slip->campaign = $request->campaign;
        $slip->first_name = $request->first_name;
        $slip->last_name = $request->last_name;
        $slip->sex = $request->sex;
        $slip->email = $request->email;
        $slip->phone = $request->phone;
        $slip->address = $request->address;
        $slip->ministered_by = $request->ministered_by;
        $slip->created_by = $request->user->id;

        if($slip->save()) {
            return response()->json([
                'successMessage' => 'Slip created successfully',
                'slip' => $slip
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function update(Request $request)
    {
        $slip = $request->slip;

        $slip->campaign = $request->campaign  ?
        $request->campaign : $slip->campaign;

        $slip->first_name = $request->first_name ?
        $request->first_name : $slip->first_name;

        $slip->last_name = $request->last_name ?
        $request->last_name : $slip->last_name;

        $slip->sex = $request->sex ?
        $request->sex : $slip->sex;
        
        $slip->email = $request->email ?
        $request->email : $slip->email;  

        $slip->phone = $request->phone ?
        $request->phone : $slip->phone;  

        $slip->address = $request->address ?
        $request->address : $slip->address;  

        $slip->ministered_by = $request->ministered_by ?
        $request->ministered_by : $slip->ministered_by; 

        $slip->updated_by = $request->user->id;


        if($slip->save()) {
            return response()->json([
                'successMessage' => 'Slip updated successfully',
                'slip' => $slip
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function show(Request $request)
    {
        $slip = $request->slip;

        if($slip) {
            return response()->json([
                'slip' => $slip
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function viewAll(Request $request)
    {
        $slips = Slip::where([
            'church_id' => $request->church->id,
        ])->get();


        if(!count($slips)) {
            return response()->json([
                'errorMessage' => 'Slips can not be found'
            ], 404);
        }

        if($slips) {
            return response()->json([
                'slips' => $slips
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function delete(Request $request, $slipId)
    {
        $slip = $request->slip;

        $slip->update([
            'deleted_by' => $request->user->id
        ]);
        
        if($slip) {
            Slip::destroy($slipId);
            return response()->json([
                'successMessage' => 'Slip deleted successfully'
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }
}
