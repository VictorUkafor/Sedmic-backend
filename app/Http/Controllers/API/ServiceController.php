<?php

namespace App\Http\Controllers\API;

use App\OrderOfService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    // create service
    public function create(Request $request)
    {
        $order = $request->programme->orderOfServices;

        $service = new OrderOfService;
        $service->programme_id = $request->programme->id;
        $service->invitee_id = $request->anchor;
        $service->title = $request->title;
        $service->start_time = $request->start_time;
        $service->end_time = $request->end_time;
        $service->duration = $request->duration;
        $service->order = count($order) + 1;
        $service->instruction = $request->instruction;        
        $service->created_by = $request->user->id;

        if($service->save()) {
            return response()->json([
                'successMessage' => 'Service created successfully',
                'service' => $service
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    // create service
    public function update(Request $request)
    {

        $service = $request->service;
        $service->invitee_id = $request->anchor ? 
        $request->anchor : $service->anchor;

        $service->invitee_id = $request->title ? 
        $request->title : $service->title;

        $service->invitee_id = $request->instruction ? 
        $request->instruction : $service->instruction;

        $service->updated_by = $request->user->id;

        if($service->save()) {
            return response()->json([
                'successMessage' => 'Service updated successfully',
                'service' => $service
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    }
    


    // view all services
    public function viewAll(Request $request)
    {
        $services = $request->programme->orderOfServices()
        ->orderBy('order', 'asc')->get();

        if(!count($services)) {
            return response()->json([
                'errorMessage' => 'Services could not be found',
            ], 401);
        }

        if(count($services)) {
            return response()->json([
                'services' => $services
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    // view a service
    public function show(Request $request)
    {
        if($request->service) {
            return response()->json([
                'service' => $request->service
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    // delete a service
    public function delete(Request $request)
    {
        $service = $request->service;
        $updateService = $service->update([
            'deleted_by' => $request->user->id
        ]);

        $deleteService = OrderOfService::destroy($service->id);
        
        if($updateService && $deleteService) {
            return response()->json([
                'successMessage' => 'Service deleted successfully'
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    // view all service gaps
    public function gaps(Request $request)
    {
        if(count($request->gaps)) {
            return response()->json([
                'gaps' => $request->gaps
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    // view a single service gaps
    public function gap(Request $request,$programmeId, $gapId)
    {
        $gapId = (int)$gapId - 1;

        if(array_key_exists($gapId, $request->gaps) == false) {
            return response()->json([
                'gap' => 'Gap not found'
            ], 404);
        }

        if($request->gaps[$gapId]) {
            return response()->json([
                'gap' => $request->gaps[$gapId]
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function fixGap(Request $request)
    {
        $oldServices = $request->programme->orderOfServices;

        foreach($request->gaps as $gap){
            $gapstart = strtotime($gap['start_time']);
            $gapend = strtotime($gap['end_time']);

            $duration = date('i', ($gapend - $gapstart));

            $service = new OrderOfService;
            $service->programme_id = $request->programme->id;
            $service->invitee_id = $request->user->id;
            $service->title = 'Fixing gaps';
            $service->start_time = $gap['start_time'];
            $service->end_time = $gap['end_time'];
            $service->duration = $duration;
            $service->order = $gap['order'];
            $service->instruction = 'No instruct';        
            $service->created_by = $request->user->id; 
            $service->save();           
        }

        $newServices = OrderOfService::where('programme_id', $request->programme)
        ->get();

        if(count($newServices) - count($oldServices) == count($request->gaps)) {
            return response()->json([
                'successMessage' => 'Gap(s) fix successfully',
                'service' => $services
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function squash(Request $request)
    {
        $services = $request->programme->orderOfServices()
        ->orderBy('order', 'asc')->get();


        for($x = 0; $x < count($services); $x++){
            $time = isset($time) ? $time + $services[$x]->duration : 
            $services[$x]->duration;

            $initialTime = $time - $services[$x]->duration;

            $startTime = strtotime("+$initialTime minutes", 
            strtotime($request->programme->time_starting));
            $startTime = date('H:i:s', $startTime);

            $duration = $services[$x]->duration;

            $endTime = strtotime("+$duration minutes", strtotime($startTime));
            $endTime = date('H:i:s', $endTime);
            
            $services[$x]->start_time = $startTime;
            $services[$x]->end_time = $endTime;
            $services[$x]->order = $x+1;
            $services[$x]->updated_by = $request->user->id; 
            $services[$x]->save();           
        }


        if($services) {
            return response()->json([
                'successMessage' => 'Gap(s) squash successfully',
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }



}
