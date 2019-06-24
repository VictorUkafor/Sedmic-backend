<?php

namespace App\Http\Controllers\API;

use App;
use Illuminate\Http\Request;
use App\Notifications\ServiceCreate;
use therealsmat\Ebulksms\EbulkSMS;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    // create service
    public function create(Request $request, EbulkSMS $sms)
    {
        $order = $request->programme->orderOfServices;

        $service = new App\OrderOfService;
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
            $findAnchor = App\Invitee::find($service->invitee_id);

            $anchor = null;

            switch (true) {
                case $findAnchor->member_id:
                $anchor = App\Member::find($findAnchor->member_id);
                break;
                case $findAnchor->firstTimer_id:
                $anchor = App\FirstTimer::find($findAnchor->firstTimer_id);
                break;
                case $findAnchor->slip_id:
                $anchor = App\Slip::find($findAnchor->slip_id);
                break;
                default:
                $anchor = null;
            }


            $smsSender = $request->church->sms_sender_name ? 
            $request->church->sms_sender_name : 'Sedmic';

            $message = "Hi $anchor->first_name! This is to notify you that you\'ll 
            be handling $service->title of the $request->programme->title programme
             from $service->start_time to $service->end_time. Thank you";

            $mail = [];
            $mail['programme'] = $request->programme;
            $mail['service'] = $service;
            $mail['anchor'] = $anchor;

            if($anchor){
                if($anchor->phone && $request->programme->sms_notification){
                    $sms->fromSender($smsSender)
                    ->composeMessage($message)
                    ->addRecipients($anchor->phone)
                    ->send();
                }

                if($anchor->email && $request->programme->email_notification){
                    $anchor->notify(new ServiceCreate($mail));                  
                } 
            }

            return response()->json([
                'successMessage' => 'Service created successfully',
                'service' => $service
            ], 201);

        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    // update service
    public function update(Request $request)
    {
        $service = $request->service;

        // last service
        $lastService = $request->programme->orderOfServices()
        ->orderBy('order', 'desc')->first();

        // before
        $firstService = $request->programme->orderOfServices()
        ->orderBy('order', 'asc')->first();

        // get the start_time(mins) of the old service
        $service_start = explode(':', $service->start_time);
        $service_start_min = ($service_start[0]*60) + 
        $service_start[1] + ($service_start[2]>30?1:0);

        // get the end_time(mins) of the old service
        $service_end = explode(':', $service->end_time);
        $service_end_min = ($service_end[0]*60) + 
        $service_end[1] + ($service_end[2]>30?1:0);        

        // get the start_time(mins) of the new service
        $start_time = explode(':', $request->start_time);
        $start_time_min = $request->start_time ? ($start_time[0]*60) + 
        $start_time[1] + ($start_time[2]>30?1:0) : $service_start_min;

        // get the end_time(mins) of the new service
        $end_time = explode(':', $request->end_time);
        $end_time_min = $request->end_time ? ($end_time[0]*60) + 
        $end_time[1] + ($end_time[2]>30?1:0) : $service_end_min;

        // get the end_time(mins) of the last service 
        $last_service = explode(':', $lastService->end_time);
        $last_service_end = ($last_service[0]*60) + 
        $last_service[1] + ($last_service[2]>30?1:0);

        $start_diff = 0;
        $beforeValue = null;
        if($start_time_min > $service_start_min){
            $start_diff = $start_time_min - $service_start_min;
            $beforeValue = true;
        }

        if($start_time_min < $service_start_min){
            $start_diff = $service_start_min - $start_time_min;
            $beforeValue = false;
        }

        $end_diff = 0;
        $afterValue = null;
        if($end_time_min > $service_end_min){
            $end_diff = $end_time_min - $service_end_min;
            $afterValue = true;
        }

        if($end_time_min < $service_end_min){
            $end_diff = $service_end_min - $end_time_min;
            $afterValue = false;
        }


        if((!$beforeValue && (($firstService->duration - $start_diff) < 5)) || 
        ($request->service->order == 1 && 
        ($request->start_time != $request->programme->time_starting))
        ){
            return response()->json([
                'errorMessage' => 'Start time is the beyond the starting time of the programme',
            ], 401);
        }


        if(($afterValue && (date('H:i:s', 
        strtotime("+$end_diff minutes", 
        strtotime($lastService->end_time))) > 
        $request->programme->time_ending)) || 
        
        ($beforeValue && (date('H:i:s', 
        strtotime("+$start_diff minutes", 
        strtotime($lastService->end_time))) > 
        $request->programme->time_ending))){
            return response()->json([
                'errorMessage' => 'End time is beyond the time ending for the programme',
            ], 401);
        }



        $beforeServices = $request->programme->orderOfServices()
        ->where('order', '<', $service->order)->get();

        $afterServices = $request->programme->orderOfServices()
        ->where('order', '>', $service->order)->get();

            if($beforeValue && $beforeServices){
                foreach($beforeServices as $before){
                    $startTime = strtotime("+$start_diff minutes", strtotime($before->start_time));
                    $endTime = strtotime("+$start_diff minutes", strtotime($before->end_time));
                
                    $before_start_time = $before->order == 1 ?
                    $request->programme->time_starting : date('H:i:s', $startTime);

                    $before->start_time = $before_start_time;
                    $before->end_time = date('H:i:s', $endTime);

                    $splitStart = explode(':', $before_start_time);
                    $start_mins = ($splitStart[0]*60)+($splitStart[1])+($splitStart[2]>30?1:0);

                    $splitEnd = explode(':', date('H:i:s', $endTime));
                    $end_mins = ($splitEnd[0]*60)+($splitEnd[1])+($splitEnd[2]>30?1:0);

                    $before->duration = $end_mins - $start_mins;
                    $before->save();
                }
            }  
                
            if(!$beforeValue && $beforeServices){
                foreach($beforeServices as $before){
                    $startTime = strtotime("-$start_diff minutes", strtotime($before->start_time));
                    $endTime = strtotime("-$start_diff minutes", strtotime($before->end_time));
                    
                    $before_start_time = $before->order == 1 ?
                    $request->programme->time_starting : date('H:i:s', $startTime);
    
                    $before->start_time = $before_start_time;
                    $before->end_time = date('H:i:s', $endTime);
    
                    $splitStart = explode(':', $before_start_time);
                    $start_mins = ($splitStart[0]*60)+($splitStart[1])+($splitStart[2]>30?1:0);
    
                    $splitEnd = explode(':', date('H:i:s', $endTime));
                    $end_mins = ($splitEnd[0]*60)+($splitEnd[1])+($splitEnd[2]>30?1:0);
    
                    $before->duration = $end_mins - $start_mins;
                    $before->save();
                }
            }            

            if($afterValue && $afterServices){
                foreach($afterServices as $before){
                    $startTime = strtotime("+$end_diff minutes", strtotime($before->start_time));
                    $endTime = strtotime("+$end_diff minutes", strtotime($before->end_time));
                
                    $before_start_time = $before->order == 1 ?
                    $request->programme->time_starting : date('H:i:s', $startTime);

                    $before->start_time = $before_start_time;
                    $before->end_time = date('H:i:s', $endTime);

                    $splitStart = explode(':', $before_start_time);
                    $start_mins = ($splitStart[0]*60)+($splitStart[1])+($splitStart[2]>30?1:0);

                    $splitEnd = explode(':', date('H:i:s', $endTime));
                    $end_mins = ($splitEnd[0]*60)+($splitEnd[1])+($splitEnd[2]>30?1:0);

                    $before->duration = $end_mins - $start_mins;
                    $before->save();
                }
            }  
                
            if(!$afterValue && $afterServices){
                foreach($afterServices as $before){
                    $startTime = strtotime("-$end_diff minutes", strtotime($before->start_time));
                    $endTime = strtotime("-$end_diff minutes", strtotime($before->end_time));
                    
                    $before_start_time = $before->order == 1 ?
                    $request->programme->time_starting : date('H:i:s', $startTime);
    
                    $before->start_time = $before_start_time;
                    $before->end_time = date('H:i:s', $endTime);
    
                    $splitStart = explode(':', $before_start_time);
                    $start_mins = ($splitStart[0]*60)+($splitStart[1])+($splitStart[2]>30?1:0);
    
                    $splitEnd = explode(':', date('H:i:s', $endTime));
                    $end_mins = ($splitEnd[0]*60)+($splitEnd[1])+($splitEnd[2]>30?1:0);
    
                    $before->duration = $end_mins - $start_mins;
                    $before->save();
                }
            } 

        
        // update service
        $updateService = $request->service;

        $updateService->invitee_id = $request->anchor ? 
        $request->anchor : $updateService->invitee_id;

        $updateService->title = $request->title ? 
        $request->title : $updateService->title;

        $new_start_time = $request->start_time ? 
        $request->start_time : $updateService->start_time;

        $accepted_start_time = $updateService->order == 1 ?
        $request->programme->time_starting : $new_start_time;
        
        $updateService->start_time = $accepted_start_time; 

        $timesplit = explode(':', $accepted_start_time);
        $min = ($timesplit[0]*60)+($timesplit[1])+($timesplit[2]>30?1:0);

        $updateService->end_time = $request->end_time ? 
        $request->end_time : $updateService->end_time;

        $updateService->duration = $end_time_min - $min;

        $updateService->instruction = $request->instruction ? 
        $request->instruction : $updateService->instruction;

        $updateService->updated_by = $request->user->id;
        

        if($updateService->save()) {
            return response()->json([
                'successMessage' => 'Service updated successfully',
                'service' => $updateService
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

        $deleteService = App\OrderOfService::destroy($service->id);
        
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


    public function restoreServices(Request $request)
    {
        $oldServices = $request->programme->orderOfServices;

        foreach($request->gaps as $gap){
            $gapstart = strtotime($gap['start_time']);
            $gapend = strtotime($gap['end_time']);

            $duration = date('i', ($gapend - $gapstart));

            $service = new App\OrderOfService;
            $service->programme_id = $request->programme->id;
            $service->invitee_id = $request->user->id;
            $service->title = 'temp-service-'.mt_rand(0, 9999999);;
            $service->start_time = $gap['start_time'];
            $service->end_time = $gap['end_time'];
            $service->duration = $duration;
            $service->order = $gap['order'];
            $service->instruction = 'No instruction';        
            $service->created_by = $request->user->id; 
            $service->save();           
        }

        $newServices = App\OrderOfService::where('programme_id', $request->programme->id)
        ->get();

        if(count($newServices) - count($oldServices) == count($request->gaps)) {
            return response()->json([
                'successMessage' => 'Services restored successfully',
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
                'successMessage' => 'Services squashed successfully',
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }



}
