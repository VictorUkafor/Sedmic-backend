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
use App\Notifications\ProgrammeCancel;
use App\Notifications\ProgrammeSuspend;
use App\Notifications\ProgrammeHandler;
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
        $programme->title = strtolower(preg_replace('/\s+/', ' ', $request->title));
        $programme->type_of_meeting = $request->type_of_meeting;
        $programme->live = $request->live;
        $programme->date = $request->date;

        $programme->venue = $request->venue ? 
        strtolower(preg_replace('/\s+/', ' ', $request->venue)) :
        $request->church->venue;

        $programme->time_starting = $request->time_starting;
        $programme->time_ending = $request->time_ending;
        $programme->email_notification = $request->email_notification;
        $programme->sms_notification = $request->sms_notification;
        $programme->created_by = $request->user->id;

        if($programme->save()) {

            $organizer = User::find($programme->created_by);

            $smsSender = $request->church->sms_sender_name ? 
            $request->church->sms_sender_name : 'Sedmic';

            $programme->message = $request->message;

            $mail = [];
            $mail['programme'] = $programme;
            $mail['church'] = $request->church;
            
            DB::table('handlers')->insert([
                'programme_id' => $programme->id, 
                'user_id' => $request->user->id,
                'created_by' => $request->user->id,
                'created_at' => date('Y-m-d h:m:s')
            ]);
            

            if($handlers && count($handlers)){
                foreach($handlers as $id){
                    $user = User::find($id);

                    $handler = Handler::where([
                        'programme_id' => $programme->id,
                        'user_id' => $id
                    ])->first();

                    $message = $request->message ? $request->message : 
                    'Hi There! This is to notify you that you are now an handler to the programme '.
                    $programme->title.'('.$request->church->name_of_church.'). You may reachout to '.
                    $organizer->full_name.', '.$organizer->phone ? $organizer->phone : $organizer->email.
                    ' for more info. God bless you';

                    if($user && $user->church_id = $programme->church_id && !$handler){
                        DB::table('handlers')->insert([
                            'programme_id' => $programme->id, 
                            'user_id' => $id,
                            'created_by' => $request->user->id,
                        ]);                      
                    }

                    if($user && $user->phone){
                        $sms->fromSender($smsSender)
                        ->composeMessage($message)
                        ->addRecipients($user->phone)
                        ->send();                   
                    }

                    if($user && $user->email){
                        $mail['user'] = $user;
                        $user->notify(new ProgrammeHandler($mail));                  
                    }

                }
            }

           
            $contact = $organizer->phone ? 
            $organizer->phone : $organizer->email;

            $message = $request->message ? $request->message : 
            'Hi There! You\'ve been invited to attend '.
            $programme->title.'('.$request->church->name_of_church.') taking place at '.
            $programme->venue.' on '.$programme->date.' by '.$programme->time_starting.
            '. We\'ll be expecting you. You may reachout to '.$organizer->full_name.', '.
            $contact.' for more info.  God bless you!';


            if($invitees && $invitees['members']){
                    foreach($invitees['members'] as $memberId){
                        $member = Member::find($memberId);

                        if($member){
                            DB::table('invitees')->insert([
                                'programme_id' => $programme->id,
                                'member_id' => $member->id,
                                'first_name' => $member->first_name,
                                'last_name' => $member->last_name,                            
                                'image' => $member->image,
                                'created_by' => $request->user->id,
                            ]);


                            if($request->invitation_on_creation){

                                if($programme->email_notification && $member->email){
                                    $mail['user'] = $member;
                                    $member->notify(new ProgrammeInvitation($mail));
                                }

                                if($programme->sms_notification && $member->phone){
                                   $sms->fromSender($smsSender)
                                   ->composeMessage($message)
                                   ->addRecipients($member->phone)
                                   ->send();
                                }
                        
                        
                        } 
                    
                    }
                
                }
            
            }


            if($invitees && $invitees['slips']){
                foreach($invitees['slips'] as $slipId){
                    $slip = Slip::find($slipId);

                    if($slip){
                        DB::table('invitees')->insert([
                            'programme_id' => $programme->id,
                            'slip_id' => $slip->id,
                            'first_name' => $slip->first_name,
                            'last_name' => $slip->last_name,                            
                            'image' => $slip->image,
                            'created_by' => $request->user->id,
                        ]);


                        if($request->invitation_on_creation){

                            if($programme->email_notification && $slip->email){
                                $mail['user'] = $slip;
                                $slip->notify(new ProgrammeInvitation($mail));
                            }

                            if($programme->sms_notification && $slip->phone){
                               $sms->fromSender($smsSender)
                               ->composeMessage($message)
                               ->addRecipients($slip->phone)
                               ->send();
                            }
                        
                        
                        }
                    
                    }
                
                }
            
            }


            if($invitees && $invitees['firstTimers']){
                foreach($invitees['firstTimers'] as $firstTimerId){
                    $firstTimer = FirstTimer::find($firstTimerId);

                    if($firstTimer){
                        DB::table('invitees')->insert([
                            'programme_id' => $programme->id,
                            'first_timer_id' => $firstTimer->id,
                            'first_name' => $firstTimer->first_name,
                            'last_name' => $firstTimer->last_name,                            
                            'image' => $firstTimer->image,
                            'created_by' => $request->user->id,
                        ]);


                        if($request->invitation_on_creation){

                            if($programme->email_notification && $firstTimer->email){
                                $mail['user'] = $firstTimer;
                                $firstTimer->notify(new ProgrammeInvitation($mail));
                            }

                            if($programme->sms_notification && $firstTimer->phone){
                               $sms->fromSender($smsSender)
                               ->composeMessage($message)
                               ->addRecipients($firstTimer->phone)
                               ->send();
                            }
                        
                        
                        }
                    
                    }
                
                }
            
            }
            
            
            return response()->json([
                'successMessage' => 'Programme created successfully',
            ], 201);  

        }
        
        
        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function update(Request $request, EbulkSMS $sms)
    {
        $programme = $request->programme;

        $programme->title = $request->title ? 
        strtolower(preg_replace('/\s+/', ' ', $request->title)) :
        $programme->title;

        $programme->date = $request->date ?
        $request->date : $programme->date;

        $programme->time_starting = $request->time_starting ? 
        $request->time_starting : $programme->time_starting;

        $programme->time_ending = $request->time_ending ? 
        $request->time_ending : $programme->time_ending;

        $programme->live = $request->live ? 
        $request->live : $programme->live;

        $programme->venue = $request->venue ? 
        strtolower(preg_replace('/\s+/', ' ', $request->venue)) :
        $programme->venue;

        $programme->email_notification = $request->email_notification ? 
        $request->email_notification : $programme->email_notification;

        $programme->sms_notification = $request->sms_notification ? 
        $request->sms_notification : $programme->sms_notification;

        $programme->updated_by = $request->user->id;


        if($programme->save()) {

            if(($request->date && $request->date != $programme->date) || 
            ($request->time_starting && $request->time_starting != $programme->time_starting) ||
            ($request->venue && $request->venue != $programme->venue)){


                $organizer = User::find($programme->created_by);
                $handlers = Handler::where('programme_id', $programme->id)->pluck('user_id');
                $handlers = $handlers->toArray();
                $invitees = Invitee::where('programme_id', $programme->id)->get();

                $smsSender = $request->church->sms_sender_name ? 
                $request->church->sms_sender_name : 'Sedmic';

                $contact = $organizer->phone ? 
                $organizer->phone : $organizer->email; 

                $message = $request->message ? $request->message : 
                'Hi There! This is to notify you of the following changes. '
                .$programme->title.'('.$request->church->name_of_church.') will now hold on '
                .$programme->date.' at '.$programme->venue.' by '.$programme->time_starting.
                'You may reachout to '.$organizer->full_name.', '.$contact.
                ' for more info. Sorry for any inconviences. God bless you';

                $mail = [];
                $programme->message = $request->message;
                $mail['programme'] = $programme;
                $mail['church'] = $request->church;


                if($handlers && count($handlers)){
                    foreach($handlers as $id){
    
                        $user = User::find($id);
                        if($user && $user->phone){
                            $sms->fromSender($smsSender)
                            ->composeMessage($message)
                            ->addRecipients($user->phone)
                            ->send();                   
                        }
    
                        if($user && $user->email){
                            $mail['user'] = $user;
                            $user->notify(new ProgrammeChange($mail));                  
                        }
    
                    }
                }

                
                if(count($invitees)){
                        foreach($invitees as $invitee){
                            if($request->email_notification){
                                
                                if($invitee->member_id && Member::find($invitee->member_id)){
                                    $member = Member::find($invitee->member_id);
                                    $mail['user'] = $member;
                                    $member->notify(new ProgrammeChange($mail));
                                }

                                if($invitee->slip_id && Slip::find($invitee->slip_id)){
                                    $slip = Slip::find($invitee->slip_id);
                                    $mail['user'] = $slip;
                                    $slip->notify(new ProgrammeChange($mail));
                                }

                                if($invitee->first_timer_id && FirstTimer::find($invitee->first_timer_id)){
                                    $firstTimer = FirstTimer::find($invitee->first_timer_id);
                                    $mail['user'] = $firstTimer;
                                    $firstTimer->notify(new ProgrammeChange($mail));
                                }
                            
                            }


                            if($request->sms_notification){

                                if($invitee->slip_id && Slip::find($invitee->slip_id)){
                                    $slip = Slip::find($invitee->slip_id);                                    
                                    if($slip->phone){
                                        $sms->fromSender($smsSender)
                                        ->composeMessage($message)
                                        ->addRecipients($slip->phone)
                                        ->send();
                                    }

                                }

                                if($invitee->member_id && Member::find($invitee->member_id)){
                                    $member = Member::find($invitee->member_id);                                    
                                    if($member->phone){
                                        $sms->fromSender($smsSender)
                                        ->composeMessage($message)
                                        ->addRecipients($member->phone)
                                        ->send();
                                    }

                                }

                                if($invitee->first_timer_id && Member::find($invitee->first_timer_id)){
                                    $firstTimer = FirstTimer::find($invitee->first_timer_id);                                    
                                    if($firstTimer->phone){
                                        $sms->fromSender($smsSender)
                                        ->composeMessage($message)
                                        ->addRecipients($firstTimer->phone)
                                        ->send();
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

        $programmes = $request->church->programmes;

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


    public function cancel(Request $request, EbulkSMS $sms)
    {
        $programme = $request->programme;

        if($programme->signs){
            return response()->json([
                'errorMessage' => 'Programme can not be deleted'
            ], 401);
        }

        $programme->update([
            'deleted_by' => $request->user->id
        ]);

        $deleteProgramme = Programme::destroy($programme->id);

        if($deleteProgramme && count($programme->invitees)){
            $programme->invitees()->update([
                'deleted_by' => $request->user->id,
            ]); 

            Invitee::destroy($programme->invitees()
            ->pluck('id')->toArray());
        }


        if($deleteProgramme) {
            $organizer = User::find($programme->created_by);

            $handlers = Handler::where('programme_id', $programmeId)->pluck('user_id');
            $handlerIds = Handler::where('programme_id', $programmeId)->pluck('id');

            $invitees = Invitee::where('programme_id', $programmeId)->get();
            $inviteesId = Invitee::where('programme_id', $programmeId)->pluck('id');

            $smsSender = $request->church->sms_sender_name ? 
            $request->church->sms_sender_name : 'Sedmic';

            $contact = $organizer->phone ? 
            $organizer->phone : $organizer->email;

            $message = $request->message ? $request->message :
            'Hi there! This is to notify you that the programme '.
            $programme->title.'('.$request->church->name_of_church.') has been cancelled. '.
            'You may reachout to '.$organizer->full_name.', '.$contact.
            ' for more info. Sorry for any inconviences. God bless you';

            $programme->message = $request->message;

            $mail = [];
            $mail['programme'] = $programme;
            $mail['church'] = $request->church;


            if($handlers && count($handlers)){
                foreach($handlers as $id){

                    $user = User::find($id);
                    if($user && $user->phone){
                        $sms->fromSender($smsSender)
                        ->composeMessage($message)
                        ->addRecipients($user->phone)
                        ->send();                   
                    }

                    if($user && $user->email){
                        $mail['user'] = $user;
                        $user->notify(new ProgrammeCancel($mail));                  
                    }

                }
            }


            if(count($invitees)){
                foreach($invitees as $invitee){
                    if($request->email_notification){    
                        if($invitee->member_id && Member::find($invitee->member_id)){
                            $member = Member::find($invitee->member_id);
                            $mail['user'] = $member;
                            $member->notify(new ProgrammeCancel($mail));
                        }

                        if($invitee->slip_id && Slip::find($invitee->slip_id)){
                            $slip = Slip::find($invitee->slip_id);
                            $mail['user'] = $slip;
                            $slip->notify(new ProgrammeCancel($mail));
                        }

                        if($invitee->first_timer_id && FirstTimer::find($invitee->first_timer_id)){
                            $firstTimer = FirstTimer::find($invitee->first_timer_id);
                            $mail['user'] = $firstTimer;
                            $firstTimer->notify(new ProgrammeCancel($mail));
                        }
                    
                    }


                    if($request->sms_notification){

                        if($invitee->slip_id && Slip::find($invitee->slip_id)){
                            $slip = Slip::find($invitee->slip_id);                                    
                            if($slip->phone){
                                $sms->fromSender($smsSender)
                                ->composeMessage($message)
                                ->addRecipients($slip->phone)
                                ->send();
                            }

                        }

                        if($invitee->member_id && Member::find($invitee->member_id)){
                            $member = Member::find($invitee->member_id);                                    
                            if($member->phone){
                                $sms->fromSender($smsSender)
                                ->composeMessage($message)
                                ->addRecipients($member->phone)
                                ->send();
                            }

                        }

                        if($invitee->first_timer_id && Member::find($invitee->first_timer_id)){
                            $firstTimer = FirstTimer::find($invitee->first_timer_id);                                    
                            if($firstTimer->phone){
                                $sms->fromSender($smsSender)
                                ->composeMessage($message)
                                ->addRecipients($firstTimer->phone)
                                ->send();
                            }

                        }

                    }

                }

            }


            Handler::destroy($handlerIds->toArray());
            Invitee::destroy($inviteesId->toArray());


            return response()->json([
                'successMessage' => 'Programme cancelled successfully'
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function suspend(Request $request, EbulkSMS $sms)
    {
        $programme = $request->programme;
        $programme->update([
            'date' => null,
            'updated_by' => $request->user->id
        ]);

        if($programme) {
            $organizer = User::find($programme->created_by);
            $handlers = Handler::where('programme_id', $programme->id)->pluck('user_id');

            $invitees = Invitee::where('programme_id', $programme->id)->get();
            $inviteesId = Invitee::where('programme_id', $programme->id)->pluck('id');

            $smsSender = $request->church->sms_sender_name ? 
            $request->church->sms_sender_name : 'Sedmic';

            $contact = $organizer->phone ? 
            $organizer->phone : $organizer->email;
            
            $message = $request->message ? $request->message :
            'Hi There! This is to notify you that the programme '.
            $programme->title.'('.$request->church->name_of_church.
            ') has been suspended till further notice. You may reachout to '.
            $organizer->full_name.', '.$contact.
            ' for more info. Sorry for any inconviences. God bless you';

            $mail = [];
            $programme->message = $request->message;
            $mail['programme'] = $programme;
            $mail['church'] = $request->church;

            if($handlers && count($handlers)){
                foreach($handlers as $id){
                    $smsSender = $request->church->sms_sender_name ? 
                    $request->church->sms_sender_name : 'Sedmic';

                    $user = User::find($id);
                    if($user && $user->phone){
                        $sms->fromSender($smsSender)
                        ->composeMessage($message)
                        ->addRecipients($user->phone)
                        ->send();                   
                    }

                    if($user && $user->email){
                        $mail['user'] = $user;
                        $user->notify(new ProgrammeSuspend($mail));                  
                    }

                }
            }


            if(count($invitees)){
                foreach($invitees as $invitee){

                    if($request->email_notification){
                        
                        if($invitee->member_id && Member::find($invitee->member_id)){
                            $member = Member::find($invitee->member_id);
                            $mail['user'] = $member;
                            $member->notify(new ProgrammeSuspend($mail));
                        }

                        if($invitee->slip_id && Slip::find($invitee->slip_id)){
                            $slip = Slip::find($invitee->slip_id);
                            $mail['user'] = $slip;
                            $slip->notify(new ProgrammeSuspend($mail));
                        }

                        if($invitee->first_timer_id && FirstTimer::find($invitee->first_timer_id)){
                            $firstTimer = FirstTimer::find($invitee->first_timer_id);
                            $mail['user'] = $firstTimer;
                            $firstTimer->notify(new ProgrammeSuspend($mail));
                        }
                    
                    }


                    if($request->sms_notification){

                        if($invitee->slip_id && Slip::find($invitee->slip_id)){
                            $slip = Slip::find($invitee->slip_id);                                    
                            if($slip->phone){
                                $sms->fromSender($smsSender)
                                ->composeMessage($message)
                                ->addRecipients($slip->phone)
                                ->send();
                            }

                        }

                        if($invitee->member_id && Member::find($invitee->member_id)){
                            $member = Member::find($invitee->member_id);                                    
                            if($member->phone){
                                $sms->fromSender($smsSender)
                                ->composeMessage($message)
                                ->addRecipients($member->phone)
                                ->send();
                            }

                        }

                        if($invitee->first_timer_id && Member::find($invitee->first_timer_id)){
                            $firstTimer = FirstTimer::find($invitee->first_timer_id);                                    
                            if($firstTimer->phone){
                                $sms->fromSender($smsSender)
                                ->composeMessage($message)
                                ->addRecipients($firstTimer->phone)
                                ->send();
                            }

                        }

                    }

                }

            }


            return response()->json([
                'successMessage' => 'Programme suspended successfully'
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function changeType(Request $request, EbulkSMS $sms)
    {
        $programme = $request->programme;
        $programme->update([
            'type_of_meeting' => $request->type_of_meeting,
            'updated_by' => $request->user->id
        ]);

        if($programme) {
            $organizer = User::find($programme->created_by);
            $handlers = Handler::where('programme_id', $programme->id)->pluck('user_id');

            $invitees = Invitee::where('programme_id', $programme->id)->get();
            $inviteesId = Invitee::where('programme_id', $programme->id)->pluck('id');

            $smsSender = $request->church->sms_sender_name ? 
            $request->church->sms_sender_name : 'Sedmic';

            $contact = $organizer->phone ? 
            $organizer->phone : $organizer->email;

            $closedMessage = 'Hi There! a gate pass has been sent to'.
            ' your email. This will required at the entrance to the venue.'. 
            'Thank you';

            $openMessage = 'Hi There! This is to notify you that gate pass'.
            ' will no longer be required. Thank you';

            $meetingMSG = $request->type_of_meeting == 'closed' ?
            $closedMessage : $openMessage;
            
            $message = $request->message ? 
            $request->message : $meetingMSG;

            $mail = [];
            $programme->message = $request->message;
            $mail['programme'] = $programme;
            $mail['church'] = $request->church;

            if($handlers && count($handlers)){
                foreach($handlers as $id){
                    $smsSender = $request->church->sms_sender_name ? 
                    $request->church->sms_sender_name : 'Sedmic';

                    $user = User::find($id);
                    if($user && $user->phone){
                        $sms->fromSender($smsSender)
                        ->composeMessage($message)
                        ->addRecipients($user->phone)
                        ->send();                   
                    }

                    if($user && $user->email){
                        $mail['user'] = $user;
                        $user->notify(new ProgrammeSuspend($mail));                  
                    }

                }
            }


            if(count($invitees)){
                foreach($invitees as $invitee){

                    if($request->email_notification){
                        
                        if($invitee->member_id && Member::find($invitee->member_id)){
                            $member = Member::find($invitee->member_id);
                            $mail['user'] = $member;
                            $member->notify(new ProgrammeSuspend($mail));
                        }

                        if($invitee->slip_id && Slip::find($invitee->slip_id)){
                            $slip = Slip::find($invitee->slip_id);
                            $mail['user'] = $slip;
                            $slip->notify(new ProgrammeSuspend($mail));
                        }

                        if($invitee->first_timer_id && FirstTimer::find($invitee->first_timer_id)){
                            $firstTimer = FirstTimer::find($invitee->first_timer_id);
                            $mail['user'] = $firstTimer;
                            $firstTimer->notify(new ProgrammeSuspend($mail));
                        }
                    
                    }


                    if($request->sms_notification){

                        if($invitee->slip_id && Slip::find($invitee->slip_id)){
                            $slip = Slip::find($invitee->slip_id);                                    
                            if($slip->phone){
                                $sms->fromSender($smsSender)
                                ->composeMessage($message)
                                ->addRecipients($slip->phone)
                                ->send();
                            }

                        }

                        if($invitee->member_id && Member::find($invitee->member_id)){
                            $member = Member::find($invitee->member_id);                                    
                            if($member->phone){
                                $sms->fromSender($smsSender)
                                ->composeMessage($message)
                                ->addRecipients($member->phone)
                                ->send();
                            }

                        }

                        if($invitee->first_timer_id && Member::find($invitee->first_timer_id)){
                            $firstTimer = FirstTimer::find($invitee->first_timer_id);                                    
                            if($firstTimer->phone){
                                $sms->fromSender($smsSender)
                                ->composeMessage($message)
                                ->addRecipients($firstTimer->phone)
                                ->send();
                            }

                        }

                    }

                }

            }


            return response()->json([
                'successMessage' => 'meeting type changed successfully'
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    // add invitees
    public function addInvitees(Request $request, EbulkSMS $sms)
    {
        $programme = $request->programme;

        $invitees = json_decode(json_encode($request->invitees), true);
        $invitees = json_decode($invitees, true);

        $organizer = User::find($programme->created_by);

        $smsSender = $request->church->sms_sender_name ? 
        $request->church->sms_sender_name : 'Sedmic';

        $contact = $organizer->phone ? 
        $organizer->phone : $organizer->email;

        $message = $request->message ? $request->message : 
        'Hi There! You\'ve been invited to attend '.
        $programme->title.'('.$request->church->name_of_church.') taking place at '.
        $programme->venue.' on '.$programme->date.' by '.$programme->time_starting.
        '. We\'ll be expecting you. You may reachout to '.$organizer->full_name.', '.
        $contact.' for more info.  God bless you!';

        $programme->message = $request->message;

        $mail = [];
        $mail['programme'] = $programme;
        $mail['church'] = $request->church;

        if($invitees && $invitees['members']){
            foreach($invitees['members'] as $memberId){
                $member = Member::find($memberId);

                if($member){
                    DB::table('invitees')->insert([
                        'programme_id' => $programme->id,
                        'member_id' => $member->id,
                        'first_name' => $member->first_name,
                        'last_name' => $member->last_name,                            
                        'image' => $member->image,
                        'created_by' => $request->user->id,
                        'created_at' => date('Y-m-d h:m:s')
                    ]);


                    if($request->invitation_on_creation){

                        if($programme->email_notification && $member->email){
                            $mail['user'] = $member;
                            $member->notify(new ProgrammeInvitation($mail));
                        }

                        if($programme->sms_notification && $member->phone){
                           $sms->fromSender($smsSender)
                           ->composeMessage($message)
                           ->addRecipients($member->phone)
                           ->send();
                        }
                    }
                }
            }
        }


        if($invitees && $invitees['slips']){
            foreach($invitees['slips'] as $slipId){
                $slip = Slip::find($slipId);

                if($slip){
                    DB::table('invitees')->insert([
                        'programme_id' => $programme->id,
                        'slip_id' => $slip->id,
                        'first_name' => $slip->first_name,
                        'last_name' => $slip->last_name,                            
                        'image' => $slip->image,
                        'created_by' => $request->user->id,
                        'created_at' => date('Y-m-d h:m:s')
                    ]);


                    if($request->invitation_on_creation){

                        if($programme->email_notification && $slip->email){
                            $mail['user'] = $slip;
                            $slip->notify(new ProgrammeInvitation($mail));
                        }

                        if($programme->sms_notification && $slip->phone){
                           $sms->fromSender($smsSender)
                           ->composeMessage($message)
                           ->addRecipients($slip->phone)
                           ->send();
                        }
                    }
                
                }
            
            }
        
        }


        if($invitees && $invitees['firstTimers']){
            foreach($invitees['firstTimers'] as $firstTimerId){
                $firstTimer = FirstTimer::find($firstTimerId);

                if($firstTimer){
                    DB::table('invitees')->insert([
                        'programme_id' => $programme->id,
                        'first_timer_id' => $firstTimer->id,
                        'first_name' => $firstTimer->first_name,
                        'last_name' => $firstTimer->last_name,                            
                        'image' => $firstTimer->image,
                        'created_by' => $request->user->id,
                        'created_at' => date('Y-m-d h:m:s')
                    ]);


                    if($request->invitation_on_creation){

                        if($programme->email_notification && $firstTimer->email){
                            $mail['user'] = $firstTimer;
                            $firstTimer->notify(new ProgrammeInvitation($mail));
                        }

                        if($programme->sms_notification && $firstTimer->phone){
                           $sms->fromSender($smsSender)
                           ->composeMessage($message)
                           ->addRecipients($firstTimer->phone)
                           ->send();
                        }
                    }
                
                }
            
            }
        
        }
        

        if(!$invitees['members'] && 
        !$invitees['firstTimers'] &&
        !$invitees['slips']) {
            return response()->json([
                'successMessage' => 'No invitee were added',
            ], 201);  
        }


       if($invitees) {
            return response()->json([
                'successMessage' => 'Invitee(s) added successfully',
            ], 201);  

        }        
        

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function addHandlers(Request $request, EbulkSMS $sms)
    {
        $programme = $request->programme;

        $handlers = json_decode(json_encode($request->handlers), true);
        $handlers = json_decode($handlers, true);

        $oldHandlers = Handler::where([
            'programme_id' => $programme->id,
            'user_id' => $id
        ])->pluck('user_id')->toArray();

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


        $newHandlers = Handler::where([
            'programme_id' => $programme->id,
            'user_id' => $id
        ])->pluck('user_id')->toArray();


        foreach($newHandlers as $newHandler){
            if(!in_array($newHandler, $oldHandlers)){
                $user = User::find($newHandler);

                $organizer = User::find($programme->created_by);

                $smsSender = $request->church->sms_sender_name ? 
                $request->church->sms_sender_name : 'Sedmic';

                $contact = $organizer->phone ? 
                $organizer->phone : $organizer->email;

                $message = 'Hi '.$user->first_name.' '.$user->last_name.
                '. This is to notify you that you are now an handler to the programme '.
                $programme->title.'('.$request->church->name_of_church.'). You may reachout to '.
                $organizer->full_name.', '.$contact.' for more info. God bless you';

                $programme->message = $request->message;

                if($user && $user->phone){
                    $sms->fromSender($smsSender)
                    ->composeMessage($message)
                    ->addRecipients($user->phone)
                    ->send();                   
                }

                if($user && $user->email){
                    $mail = [];
                    $mail['user'] = $user;
                    $mail['programme'] = $programme;
                    $mail['church'] = $request->church;
                    $user->notify(new ProgrammeHandler($mail));                  
                }
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
