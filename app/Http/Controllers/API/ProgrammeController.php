<?php

namespace App\Http\Controllers\API;

use App;
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

        $programme = new App\Programme;
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

            $organizer = $request->church->users()
            ->where('id', $programme->created_by)->first();

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
                    $user = $request->church->users()
                    ->where('id', $id)->first();

                    $handler = $programme->handlers()
                    ->where('user_id', $id)->first();

                    $message = $request->message ? $request->message : 
                    'Hi There! This is to notify you that you are now an handler to the programme '.
                    $programme->title.'('.$request->church->name_of_church.'). You may reachout to '.
                    $organizer->full_name.', '.$organizer->phone ? $organizer->phone : $organizer->email.
                    ' for more info. God bless you';

                    if($user && !$handler){
                        DB::table('handlers')->insert([
                            'programme_id' => $programme->id, 
                            'user_id' => $id,
                            'created_by' => $request->user->id,
                        ]); 

                        if($user->phone){
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
            }

           
            $contact = $organizer->phone ? 
            $organizer->phone : $organizer->email;

            $message = $request->message ? $request->message : 
            'Hi There! You\'ve been invited to attend '.
            $programme->title.'('.$request->church->name_of_church.') taking place at '.
            $programme->venue.' on '.$programme->date.' by '.$programme->time_starting.
            '. We\'ll be expecting you. You may reachout to '.$organizer->full_name.', '.
            $contact.' for more info.  God bless you!';
            

            foreach($invitees as $key => $value){
                
                $attendees = null;
                switch ($key) {
                    case 'firstTimers':
                    $attendees = $request->church->firstTimers()
                    ->whereIn('id', $value)->get();
                    break;
                    case 'slips':
                    $attendees = $request->church->slips()
                    ->whereIn('id', $value)->get();
                    break;
                    case 'members':
                    $attendees = $request->church->members()
                    ->whereIn('id', $value)->get();
                    break;
                    default:
                    $attendees = null;
                }  

                if(count($attendees)){
                    foreach($attendees as $attendee){
                        DB::table('invitees')->insert([
                            'programme_id' => $programme->id,
                            'member_id' => $key == 'members' ? 
                            $attendee->id : null,
                            'slip_id' => $key == 'slips' ? 
                            $attendee->id : null,
                            'first_timer_id' => $key == 'firstTimers' ?
                            $attendee->id : null,
                            'first_name' => $attendee->first_name,
                            'last_name' => $attendee->last_name,                            
                            'image' => $attendee->image,
                            'created_by' => $request->user->id,
                        ]);

                        if($request->invitation_on_creation){
                            if($programme->email_notification && $attendee->email){
                                $mail['user'] = $attendee;
                                $attendee->notify(new ProgrammeInvitation($mail));
                            }

                            if($programme->sms_notification && $attendee->phone){
                               $sms->fromSender($smsSender)
                               ->composeMessage($message)
                               ->addRecipients($attendee->phone)
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

                $organizer = $request->church->users()
                ->where('id', $programme->created_by)->first();

                $handlers = $programme->handlers()
                ->pluck('user_id')->toArray();

                $invitees = $programme->invitees;

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
    
                        $user = $request->church->users()
                        ->where('id', $id)->first();
                        
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
                        
                        $contact = null;
                        switch (true) {
                            case $invitee->first_timer_id:
                            $contact = $request->church->firstTimers()
                            ->where('id', $invitee->first_timer_id)->first();
                            break;
                            case $invitee->slip_id:
                            $contact = $request->church->slips()
                            ->where('id', $invitee->slip_id)->first();
                            break;
                            case $invitee->member_id:
                            $contact = $request->church->members()
                            ->where('id', $invitee->member_id)->first();
                            break;
                            default:
                            $contact = null;
                        }  
            
                        if($contact){
                            if($programme->email_notification && $contact->email){
                                $mail['user'] = $contact;
                                $contact->notify(new ProgrammeChange($mail));
                            }

                            if($programme->sms_notification && $contact->phone){
                                $sms->fromSender($smsSender)
                                ->composeMessage($message)
                                ->addRecipients($contact->phone)
                                ->send();
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
        
        $organizer = $request->church->users()
        ->where('id', $programme->created_by)->first();

        $handlers = $programme->handlers()->pluck('user_id');
        $handlerIds = $programme->handlers()->pluck('id');
        $invitees = $programme->invitees;
        $inviteesId = $programme->invitees()->pluck('id');

        if($programme->signs){
            return response()->json([
                'errorMessage' => 'Programme can not be deleted'
            ], 401);
        }

        $programme->update([
            'deleted_by' => $request->user->id
        ]);

        $deleteProgramme = App\Programme::destroy($programme->id);

        if($deleteProgramme && count($programme->invitees)){
            $programme->invitees()->update([
                'deleted_by' => $request->user->id,
            ]); 

            App\Invitee::destroy($programme->invitees()
            ->pluck('id')->toArray());
        }


        if($deleteProgramme) {

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

                    $user = $request->church->users()
                    ->where('id', $id)->first();

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

                    $contact = null;
                    switch (true) {
                        case $invitee->first_timer_id:
                        $contact = $request->church->firstTimers()
                        ->where('id', $invitee->first_timer_id)->first();
                        break;
                        case $invitee->slip_id:
                        $contact = $request->church->slips()
                        ->where('id', $invitee->slip_id)->first();
                        break;
                        case $invitee->member_id:
                        $contact = $request->church->members()
                        ->where('id', $invitee->member_id)->first();
                        break;
                        default:
                        $contact = null;
                    }  
    
                    if($contact){
                        if($programme->email_notification && $contact->email){
                            $mail['user'] = $contact;
                            $contact->notify(new ProgrammeCancel($mail));
                        }

                        if($programme->sms_notification && $contact->phone){
                            $sms->fromSender($smsSender)
                            ->composeMessage($message)
                            ->addRecipients($contact->phone)
                            ->send();
                        }
                    }
                }
            }


            App\Handler::destroy($handlerIds->toArray());
            App\Invitee::destroy($inviteesId->toArray());

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

        $organizer = $request->church->users()
        ->where('id', $programme->created_by)->first();

        $handlers = $programme->handlers()->pluck('user_id');

        $invitees = $programme->invitees;
        $inviteesId = $programme->invitees()->pluck('id');

        $programme->update([
            'date' => null,
            'updated_by' => $request->user->id
        ]);

        if(!$programme->date) {

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

                    $user = $request->church->users()
                    ->where('id', $id)->first();

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

                    $contact = null;
                    switch (true) {
                        case $invitee->first_timer_id:
                        $contact = $request->church->firstTimers()
                        ->where('id', $invitee->first_timer_id)->first();
                        break;
                        case $invitee->slip_id:
                        $contact = $request->church->slips()
                        ->where('id', $invitee->slip_id)->first();
                        break;
                        case $invitee->member_id:
                        $contact = $request->church->members()
                        ->where('id', $invitee->member_id)->first();
                        break;
                        default:
                        $contact = null;
                    }  
    
                    if($contact){
                        if($programme->email_notification && $contact->email){
                            $mail['user'] = $contact;
                            $contact->notify(new ProgrammeSuspend($mail));
                        }

                        if($programme->sms_notification && $contact->phone){
                            $sms->fromSender($smsSender)
                            ->composeMessage($message)
                            ->addRecipients($contact->phone)
                            ->send();
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
        
        $organizer = $request->church->users()
        ->where('id', $programme->created_by)->first();

        $handlers = $programme->handlers()->pluck('user_id');
        $invitees = $programme->invitees;
        $inviteesId = $programme->invitees()->pluck('id');

        $programme->update([
            'type_of_meeting' => $request->type_of_meeting,
            'updated_by' => $request->user->id
        ]);

        if($programme->type_of_meeting == $request->type_of_meeting) {

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

            if(count($handlers)){
                foreach($handlers as $id){

                    $user = $request->church->users()
                    ->where('id', $id)->first();

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

                    $contact = null;
                    switch (true) {
                        case $invitee->first_timer_id:
                        $contact = $request->church->firstTimers()
                        ->where('id', $invitee->first_timer_id)->first();
                        break;
                        case $invitee->slip_id:
                        $contact = $request->church->slips()
                        ->where('id', $invitee->slip_id)->first();
                        break;
                        case $invitee->member_id:
                        $contact = $request->church->members()
                        ->where('id', $invitee->member_id)->first();
                        break;
                        default:
                        $contact = null;
                    }  
    
                    if($contact){
                        if($programme->email_notification && $contact->email){
                            $mail['user'] = $contact;
                            $contact->notify(new ProgrammeChange($mail));
                        }

                        if($programme->sms_notification && $contact->phone){
                            $sms->fromSender($smsSender)
                            ->composeMessage($message)
                            ->addRecipients($contact->phone)
                            ->send();
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


    public function addInvitees(Request $request, EbulkSMS $sms)
    {
        $programme = $request->programme;

        $invitees = json_decode(json_encode($request->invitees), true);
        $invitees = json_decode($invitees, true);

        $organizer = $request->church->users()
        ->where('id', $programme->created_by)->first();

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


        foreach($invitees as $key => $value){
                
            $attendees = null;
            switch ($key) {
                case 'firstTimers':
                $attendees = $request->church->firstTimers()
                ->whereIn('id', $value)->get();
                break;
                case 'slips':
                $attendees = $request->church->slips()
                ->whereIn('id', $value)->get();
                break;
                case 'members':
                $attendees = $request->church->members()
                ->whereIn('id', $value)->get();
                break;
                default:
                $attendees = null;
            }  

            if(count($attendees)){
                foreach($attendees as $attendee){
                    DB::table('invitees')->insert([
                        'programme_id' => $programme->id,
                        'member_id' => $key == 'members' ? 
                        $attendee->id : null,
                        'slip_id' => $key == 'slips' ? 
                        $attendee->id : null,
                        'first_timer_id' => $key == 'firstTimers' ?
                        $attendee->id : null,
                        'first_name' => $attendee->first_name,
                        'last_name' => $attendee->last_name,                            
                        'image' => $attendee->image,
                        'created_by' => $request->user->id,
                    ]);

                    if($request->invitation_on_creation){
                        if($programme->email_notification && $attendee->email){
                            $mail['user'] = $attendee;
                            $attendee->notify(new ProgrammeInvitation($mail));
                        }

                        if($programme->sms_notification && $attendee->phone){
                           $sms->fromSender($smsSender)
                           ->composeMessage($message)
                           ->addRecipients($attendee->phone)
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

        $oldHandlers = $programme->handlers()->pluck('user_id')->toArray();

        foreach($handlers as $id){

            $user = $request->church->users()
            ->where('id', $id)->first();

            $handler = $programme->handlers()
            ->where('user_id', $id)->first();

            if($user && !$handler){
                DB::table('handlers')
                ->insert(
                    [
                        'programme_id' => $programme->id, 
                        'user_id' => $id,
                        'created_by' => $request->user->id,
                    ]);                      
            }
        }


        $newHandlers = App\Handler::where([
            'programme_id' => $programme->id,
            'user_id' => $id
        ])->pluck('user_id')->toArray();

        foreach($newHandlers as $newHandler){
            if(!in_array($newHandler, $oldHandlers)){
                $user = $request->church->users()
                ->where('id', $newHandler)->first();

                $organizer = $request->church->users()
                ->where('id', $programme->created_by)->first();

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
        $handlers = $programme->handlers()->pluck('user_id');

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
        $deleteHandler = App\Handler::destroy($request->handlerId);

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
