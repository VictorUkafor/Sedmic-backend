<?php

namespace App\Http\Controllers\API;

use App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    
    // show all invitees
    public function invitees(Request $request)
    {
        
        $invitees = $request->programme->invitees;
        
        if(count($invitees)) {
            return response()->json([
                'invitees' => $invitees
            ], 200);
        }

        if(!count($invitees)) {
            return response()->json([
                'errorMessage' => 'Invitees could not be found'
            ], 404);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    
    }


    // get invitee
    public function invitee(Request $request)
    {        
        if($request->invitee) {
            return response()->json([
                'invitee' => $request->invitee
            ], 200);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    // remove invitee
    public function removeInvitee(Request $request)
    {
        $invitee = $request->invitee;

        if($invitee->signs){
            return response()->json([
                'errorMessage' => 'Attendee can not be deleted'
            ], 401);
        }
        
        $invitee->update(['deleted_by' => $request->user->id]);
        $deleteInvitee = App\Invitee::destroy($invitee->id);

        if($deleteInvitee) {
            return response()->json([
                'successMessage' => 'Invitee removed successfully'
            ], 200);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    // search for invitee
    public function search(Request $request)
    {
        $programme = $request->programme;
        $search = $request->search;

        $invitees = $programme->invitees()
        ->where('first_name', 'Like', '%'.$search.'%')
        ->OrWhere('last_name', 'Like', '%'.$search.'%')
        ->get();

        
        if(!count($invitees)) {
            return response()->json([
                'errorMessage' => 'Invitees could not be found'
            ], 404);
        }

        if(count($invitees)) {
            return response()->json([
                'invitees' => $programme->invitees
            ], 200);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    }


    // get attendance
    public function attendees(Request $request)
    {
        $programme = $request->programme;

        $attendees =  $programme->invitees()
        ->where('present', 1)->get();
        
        if(!count($attendees)) {
            return response()->json([
                'errorMessage' => 'Attendees could not be found'
            ], 404);
        }

        if(count($attendees)) {
            return response()->json([
                'attendance' => $attendees
            ], 200);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    }


    // add attendees
    public function addAttendees(Request $request)
    {
        $invitees = $request->invitees;

        foreach($invitees as $key => $type){

            $attendees = null;
            switch ($key) {
                case 'firstTimers':
                $attendees = $request->church->firstTimers()
                ->whereIn('id', $type)->get();
                break;
                case 'slips':
                $attendees = $request->church->slips()
                ->where('id', $type)->get();
                break;
                case 'members':
                $attendees = $request->church->members()
                ->where('id', $type)->get();
                break;
                default:
                $attendees = null;
            }  
            
            if(count($attendees)){
                foreach($attendees as $attendee){
                    DB::table('invitees')->insert([
                        'programme_id' => $request->programme->id,
                        'member_id' => $key == 'members' ? 
                        $attendee->id : null,
                        'slip_id' => $key == 'slips' ? 
                        $attendee->id : null,
                        'first_timer_id' => $key == 'firstTimers' ?
                        $attendee->id : null,
                        'first_name' => $attendee->first_name,
                        'last_name' => $attendee->last_name, 
                        'present' => 1,                           
                        'image' => $attendee->image,
                        'created_by' => $request->user->id,
                        'created_at' => date('Y-m-d H:m:s')
                    ]);                    
                }
            }
        }


        if(!count($invitees)){
            return response()->json([
                'successMessage' => 'Attendees added successfully'
            ], 201);             
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    // get attendance
    public function absentees(Request $request)
    {
        $programme = $request->programme;

        $absentees = $programme->invitees()
        ->where('present', 0)->get();
        
        if(!count($absentees)) {
            return response()->json([
                'errorMessage' => 'Absentees could not be found'
            ], 404);
        }

        if(count($absentees)) {
            return response()->json([
                'absentees' => $absentees
            ], 200);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    }


    // add sign in and sign out
    public function addSign(Request $request)
    {
        $invitee = $request->invitee;

        if(!$invitee->present){
            $invitee->update([
                'present' => 1,
                'updated_by' => $request->user->id,
                'updated_at' => date('Y-m-d H:m:s')
            ]);
        }

        $sign = DB::table('signs')->insert([
            'programme_id' => $request->programme->id, 
            'invitee_id' => $request->invitee->id,
            'value' => $request->value,
            'created_by' => $request->user->id,
            'created_at' => date('Y-m-d H:m:s')
        ]);

        if($sign) {
            return response()->json([
                'successMessage' => 'Action performed successfully'
            ], 201);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    // add new attendee
    public function newContact(Request $request)
    {
        $newAttendee = null;

        if($request->type == 'firstTimer' ||
        $request->type == 'slip' ||
        $request->type == 'new'){
            $newAttendee = new App\Member;
        }

        if($request->type == 'slip' || $request->type == 'new'){
            $newAttendee = new App\FirstTimer;
            $newAttendee->programme_id = $request->programme->id;
        }
        
        $newAttendee->church_id = $request->church->id;
        $newAttendee->first_name = $request->first_name;
        $newAttendee->last_name = $request->last_name;
        $newAttendee->created_by = $request->user->id;

        if($newAttendee->save()) {
            if($request->type == 'new'){

                if(!$newAttendee->campaign && $request->unit){
                    $request->unit->members()->attach($newAttendee);
                }

                $invitee = DB::table('invitees')->insert([
                    'programme_id' => $request->programme->id,
                    'member_id' => !$newAttendee->campaign ? null : $newAttendee->id,
                    'slip_id' => $newAttendee->campaign ? $newAttendee->id : null,
                    'first_name' => $newAttendee->first_name,
                    'last_name' => $newAttendee->last_name, 
                    'present' => 1,                           
                    'created_by' => $request->user->id,
                    'created_at' => date('Y-m-d H:m:s')
                ]);
                
                if($invitee){
                    DB::table('signs')->insert([
                        'programme_id' => $request->programme->id, 
                        'invitee_id' => $invitee->id,
                        'value' => 1,
                        'created_by' => $request->user->id,
                        'created_at' => date('Y-m-d H:m:s')
                    ]); 

                }

            } 
            
            return response()->json([
                'successMessage' => 'Action performed successfully'
            ], 201); 

        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function getFirstTimers(Request $request)
    {        
        $firstTimers = $request->programme->firstTimers;
        
        if(count($firstTimers)) {
            return response()->json([
                'firstTimers' => $firstTimers
            ], 200);   
        }

        if(!count($firstTimers)) {
            return response()->json([
                'errorMessage' => 'First timers could not be found'
            ], 200);   
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    // get sign in or sign out
    public function getSign(Request $request)
    {        
        if($request->sign) {
            return response()->json([
                'sign' => $request->sign
            ], 200);
        }
        
        return response()->json([
            'errorMessage' => 'Sign could not be found'
        ], 404);

    }


    // edit sign in and sign out
    public function editSign(Request $request)
    {
        $sign = $request->sign; 
        
        $sign->update([
            'value' => $request->value,
            'updated_by' => $request->user->id
        ]);

        if($sign) {
            return response()->json([
                'successMessage' => 'Sign updated successfully'
            ], 201);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    // remove sign in and sign out
    public function removeSign(Request $request)
    {
        $invitee = $request->invitee;
        $sign = $request->sign; 

        $attendeeSigns = $request->invitee->signs;

        if(count($attendeeSigns) === 1){
            $invitee->present = 0;
            $invitee->updated_by = $request->user->id;
            $invitee->saved();
        }
        
        $sign->update([
            'deleted_by' => $request->user->id
        ]);

        $deleteSign = Sign::destroy($sign->id);

        if($deleteSign) {
            return response()->json([
                'successMessage' => 'Sign removed successfully'
            ], 200);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    // Attendee sign times
    public function attendeeSigns(Request $request)
    {
        $signs = $request->invitee->signs;

        if(!count($signs)) {
            return response()->json([
                'errorMessage' => 'Signs could not be found'
            ], 404);
        }

        if(count($signs)) {
            return response()->json([
                'signs' => $signs
            ], 200);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    // sign in and out
    public function signs(Request $request)
    {
        $signs = $request->programme->signs;

        if(!count($signs)) {
            return response()->json([
                'errorMessage' => 'Signs could not be found'
            ], 404);
        }

        if(count($signs)) {
            return response()->json([
                'signs' => $signs
            ], 200);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }

}
