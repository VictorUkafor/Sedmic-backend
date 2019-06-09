<?php

namespace App\Http\Controllers\API;

use App\Programme;
use App\Member;
use App\FirstTimer;
use App\Slip;
use App\Handler;
use App\Invitee;
use App\User;
use App\Notifications\ProgrammeInvitation;
use App\Notifications\ProgrammeChange;
use therealsmat\Ebulksms\EbulkSMS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProgrammeController extends Controller
{
    // create programme
    public function create(Request $request, EbulkSMS $sms)
    {
        $invitees = json_decode(json_encode($request->invitees), true);
        $invitees = json_decode($invitees, true);

        $handlers = json_decode(json_encode($request->handlers), true);
        $handlers = json_decode($handlers, true);

        $programme = new Programme;
        $programme->church_id = $request->church->id;
        $programme->title = $request->title;
        $programme->type_of_meeting = $request->type_of_meeting;
        $programme->live = $request->live;
        $programme->date = $request->date;

        $programme->venue = $request->venue ? 
        $request->venue : $request->church->venue;

        $programme->time_starting = $request->time_starting;
        $programme->time_ending = $request->time_ending;
        $programme->email_notification = $request->email_notification;
        $programme->sms_notification = $request->sms_notification;
        $programme->invitation_message = $request->invitation_message;
        $programme->created_by = $request->user->id;

        if($programme->save()) {
            if($request->invitation_on_creation && $request->email_notification){

                if($invitees && $invitees['members']){
                    foreach($invitees['members'] as $memberId){
                        $member = Member::find($memberId);
                        if($member && $member->email){
                            $mail = [];
                            $mail['user'] = $member;
                            $mail['programme'] = $programme;
                            $mail['church'] = $request->church;
                            //$member->notify(new ProgrammeInvitation($mail));
                        }

                        if($member){
                            DB::table('invitees')->insert([
                                'programme_id' => $programme->id,
                                'member_id' => $member->id,
                                'first_name' => $member->first_name,
                                'last_name' => $member->last_name,                            
                                'image' => $member->image,
                                'created_by' => $request->user->id,
                            ]);
                        }   
                    }
                }

                if($invitees && $invitees['firstTimers']){
                    foreach($invitees['firstTimers'] as $firstTimerId){
                        $firstTimer = FirstTimer::find($firstTimerId);
                        if($firstTimer && $firstTimer->email){
                            $mail = [];
                            $mail['user'] = $firstTimer;
                            $mail['programme'] = $programme;
                            $mail['church'] = $request->church;
                            //$firstTimer->notify(new ProgrammeInvitation($mail));
                        } 

                        if($firstTimer){
                            DB::table('invitees')->insert([
                                'programme_id' => $programme->id,
                                'first_timer_id' => $firstTimer->id,
                                'first_name' => $firstTimer->first_name,
                                'last_name' => $firstTimer->last_name,                            
                                'image' => $firstTimer->image,
                                'created_by' => $request->user->id,
                            ]);
                        } 
                    }
                }

                if($invitees && $invitees['slips']){
                    foreach($invitees['slips'] as $slipId){
                        $slip = Slip::find($slipId);
                        if($slip && $slip->email){
                            $mail = [];
                            $mail['user'] = $slip;
                            $mail['programme'] = $programme;
                            $mail['church'] = $request->church;
                            //$slip->notify(new ProgrammeInvitation($mail));
                        } 
                        
                        if($slip){
                            DB::table('invitees')->insert([
                            'programme_id' => $programme->id,
                            'slip_id' => $slip->id,
                            'first_name' => $slip->first_name,
                            'last_name' => $slip->last_name,                            
                            'image' => $slip->image,
                            'created_by' => $request->user->id,
                            ]);

                        }
                    }
                } 


            }


            if($request->invitation_on_creation && $request->sms_notification){

                $smsSender = $request->church->sms_sender_name ? 
                $request->church->sms_sender_name : 'Sedmic';

                $invitationMessage = $programme->invitation_message ? $programme->invitation_message : 
                'Hi '.$request->user->first_name.' '.$request->user->last_name.
                ' You\'ve been invited to attend '.$programme->title.'('.$requrst->church->name_of_church.
                ') taking place at '.$programme->venue.' on '.$programme->date.' by '.$programme->time_starting.
                ' We\'ll be expecting you. God bless you!';

                if($invitees && $invitees['members']){
                    foreach($invitees['members'] as $memberId){
                        $member = Member::find($memberId);
                        if($member && $member->phone){
                            // $sms->fromSender($smsSender)
                            // ->composeMessage($invitationMessage)
                            // ->addRecipients($member->phone)
                            // ->send();
                        }     
                    }
                }

                if($invitees && $invitees['firstTimers']){
                    foreach($invitees['firstTimers'] as $firstTimerId){
                        $firstTimer = FirstTimer::find($firstTimerId);
                        if($firstTimer && $firstTimer->phone){
                            // $sms->fromSender($smsSender)
                            // ->composeMessage($invitationMessage)
                            // ->addRecipients($firstTimer->phone)
                            // ->send();
                        }     
                    }
                }

                if($invitees && $invitees['slips']){
                    foreach($invitees['slips'] as $slipId){
                        $slip = Slip::find($slipId);
                        if($slip && $slip->phone){
                            // $sms->fromSender($smsSender)
                            // ->composeMessage($invitationMessage)
                            // ->addRecipients($slip->phone)
                            // ->send();
                        }     
                    }
                }

            }
            
            DB::table('handlers')->insert([
                'programme_id' => $programme->id, 
                'user_id' => $request->user->id,
                'created_by' => $request->user->id,
            ]);
            
            if($handlers && count($handlers)){
                foreach($handlers as $id){

                    $user = User::where($id);

                    $handler = Handler::where([
                        'programme_id' => $programme->id,
                        'user_id' => $id
                    ])->first();

                    if($user && $user->church_id = $programme->church_id && !$handler){
                        DB::table('handlers')->insert([
                            'programme_id' => $programme->id, 
                            'user_id' => $id,
                            'created_by' => $request->user->id,
                        ]);                      
                    }

                }
            }
            
            return response()->json([
                'successMessage' => 'Programme created successfully',
                'programme' => $programme
            ], 201);

        } 
        
        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    
    }



    public function update(Request $request)
    {
        $programme = $request->programme;

        $programme->title = $request->title ? 
        $request->title : $programme->title;

        $programme->type_of_meeting = $request->type_of_meeting ? 
        $request->type_of_meeting : $programme->type_of_meeting;

        $programme->date = $request->date ?
        $request->date : $programme->date;

        $programme->time_starting = $request->time_starting ? 
        $request->time_starting : $programme->time_starting;

        $programme->time_ending = $request->time_ending ? 
        $request->time_ending : $programme->time_ending;

        $programme->live = $request->live ? 
        $request->live : $programme->live;

        $programme->email_notification = $request->email_notification ? 
        $request->email_notification : $programme->email_notification;

        $programme->sms_notification = $request->sms_notification ? 
        $request->sms_notification : $programme->sms_notification;

        $programme->invitation_message = $request->invitation_message ?
        $request->invitation_message : $programme->invitation_message;

        $programme->updated_by = $request->user->id;


        if($programme->save()) {

            if(($request->date && $request->date != $programme->date) || 
            ($request->time_starting && $request->time_starting != $programme->time_starting) ||
            ($request->venue && $request->venue != $programme->venue)){
                
                $invitees = Invitee::where('programme_id', $programme->id)->get();

                    if(count($invitees)){
                        foreach($invitees as $invitee){

                            if($request->email_notification){
                                
                                if($invitee->member_id && Member::find($invitee->member_id)){
                                    $member = Member::find($invitee->member_id);
                                    $mail = [];
                                    $mail['user'] = $member;
                                    $mail['programme'] = $programme;
                                    $mail['church'] = $request->church;
                                    //$member->notify(new ProgrammeChange($mail));
                                }

                                if($invitee->slip_id && Slip::find($invitee->slip_id)){
                                    $slip = Slip::find($invitee->slip_id);
                                    $mail = [];
                                    $mail['user'] = $slip;
                                    $mail['programme'] = $programme;
                                    $mail['church'] = $request->church;
                                    //$slip->notify(new ProgrammeChange($mail));
                                }

                                if($invitee->first_timer_id && FirstTimer::find($invitee->first_timer_id)){
                                    $firstTimer = FirstTimer::find($invitee->first_timer_id);
                                    $mail = [];
                                    $mail['user'] = $firstTimer;
                                    $mail['programme'] = $programme;
                                    $mail['church'] = $request->church;
                                    //$firstTimer->notify(new ProgrammeChange($mail));
                            }
                            
                            if($request->sms_notification){

                                $smsSender = $request->church->sms_sender_name ? 
                                $request->church->sms_sender_name : 'Sedmic';

                                $message = 'Hi there! This is to notify you of the following changes. '
                                .$programme->title.'('.$request->church->name_of_church.') will now hold on '
                                .$programme->date.' at '.$programme->venue.' by '.$programme->time_starting.
                                ' Sorry for any inconviences. God bless you';

                                if($invitee->slip_id && Slip::find($invitee->slip_id)){
                                    $slip = Slip::find($invitee->slip_id);                                    
                                    if($slip->phone){
                                        // $sms->fromSender($smsSender)
                                        // ->composeMessage($message)
                                        // ->addRecipients($slip->phone)
                                        // ->send();
                                    }

                                }

                                if($invitee->member_id && Member::find($invitee->member_id)){
                                    $member = Member::find($invitee->member_id);                                    
                                    if($member->phone){
                                        // $sms->fromSender($smsSender)
                                        // ->composeMessage($message)
                                        // ->addRecipients($member->phone)
                                        // ->send();
                                    }

                                }

                                if($invitee->first_timer_id && Member::find($invitee->first_timer_id)){
                                    $firstTimer = FirstTimer::find($invitee->first_timer_id);                                    
                                    if($firstTimer->phone){
                                        // $sms->fromSender($smsSender)
                                        // ->composeMessage($message)
                                        // ->addRecipients($firstTimer->phone)
                                        // ->send();
                                    }

                                }

                            }

                        }
                        
                    }

    
                }
    
    

            }

            return response()->json([
                'successMessage' => 'Programme updated successfully',
                'programme' => $programme
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function show(Request $request)
    {
        $programme = $request->programme;

        if($programme) {
            return response()->json([
                'programme' => $programme
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function viewAll(Request $request)
    {
        $programmes = Programme::where([
            'church_id' => $request->church->id,
        ])->get();


        if(!count($programmes)) {
            return response()->json([
                'errorMessage' => 'Programmes can not be found'
            ], 404);
        }

        if($programmes) {
            return response()->json([
                'programmes' => $programmes
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function delete(Request $request, $programmeId)
    {
        $programme = $request->programme;
        $programme->update([
            'deleted_by' => $request->user->id
        ]);

        $handlers = Handler::where('programme_id', $programmeId)->pluck(id);
        $deleteProgramme = Programme::destroy($programmeId);
        $deleteHandlers = Handler::destroy($handlers->toArray());  

        if($programme && $deleteProgramme && $deleteHandlers) {
            return response()->json([
                'successMessage' => 'Programme deleted successfully'
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function addHandlers(Request $request)
    {
        $programme = $request->programme;

        $handlers = json_decode(json_encode($request->handlers), true);
        $handlers = json_decode($handlers, true);

        foreach($handlers as $id){

            $user = User::find($id);

            $handler = Handler::where([
                'programme_id' => $programme->id,
                'user_id' => $id
            ])->first();

            if($user && $user->church_id = $programme->church_id && !$handler){
                DB::table('handlers')
                ->insert(
                    [
                        'programme_id' => $programme->id, 
                        'user_id' => $id,
                        'created_by' => $request->user->id,
                    ]);                      
            }
        }

        if($handlers) {
            return response()->json([
                'successMessage' => 'Action performed successfully'
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    
    }


    public function getHandlers(Request $request)
    {
        $programme = $request->programme;
        $handlers = Handler::where('programme_id', $programme->id)->pluck('user_id');

        if($handlers) {
            return response()->json([
                'handlers' => $handlers->toArray()
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function removeHandler(Request $request)
    {
        $deleteHandler = Handler::destroy($request->handlerId);

        if($deleteHandler) {
            return response()->json([
                'successMessage' => 'Handler removed successfully'
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


}
