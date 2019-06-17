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
        $service = new OrderOfService;
        $service->programme_id = $request->programme->id;
        $service->invitee_id = $request->anchor;
        $service->title = $request->title;
        $service->start_time = $request->start_time;
        $service->end_time = $request->end_time;
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


    // view all services
    public function viewAll(Request $request)
    {
        $services = $request->programme->orderOfServices;

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



}
