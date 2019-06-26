<?php

namespace App\Http\Controllers\API;

use App\FirstTimer;
use App\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FirstTimerController extends Controller
{
    public function create(Request $request)
    {
        $image = $request->image ? 
        $request->first_name.$request->last_name.'first-timer'.
        $request->user->church_username : '';

        $firstTimer = new FirstTimer;
        $firstTimer->church_id = $request->church->id;
        $firstTimer->first_name = $request->first_name;
        $firstTimer->last_name = $request->last_name;
        $firstTimer->sex = $request->sex;
        $firstTimer->email = $request->email;
        $firstTimer->phone = $request->phone;
        $firstTimer->image = $image;
        $firstTimer->address = $request->address;
        $firstTimer->invited_by = $request->invited_by;
        $firstTimer->created_by = $request->user->id;

        if($firstTimer->save()) {
            if($request->image){
                Cloudder::upload($request->image->getRealPath(), $image);   
            }
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

        if($firstTimer->created_by != $user->id){
            return response()->json([
                'errorMessage' => 'Unauthorized'
            ], 401);
        }


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

        // set image public_id for cloudinary
        $image = $request->image ? 
        $firstTimer->first_name.$firstTimer->last_name.'first-timer'.
        $request->user->church_username : $firstTimer->image;


        $firstTimer->invited_by = $request->invited_by ?
        $request->invited_by : $firstTimer->invited_by; 

        $firstTimer->moved = $request->moved ?
        $request->moved : $firstTimer->moved; 

        $firstTimer->updated_by = $request->user->id;


        if($firstTimer->save()) {

            if($request->image){
                if(Cloudder::delete($firstTimer->image)){
                    Cloudder::upload($request->image->getRealPath(), $image);
                }     
            }

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


    public function move(Request $request)
    {
        $firstTimer = $request->firstTimer;

        $member = new Member;
        $member->first_name = $firstTimer->first_name;
        $member->last_name = $firstTimer->last_name;
        $member->sex = $firstTimer->sex;
        $member->phone = $firstTimer->phone;
        $member->email = $firstTimer->email;
        $member->address = $firstTimer->address;
        $member->created_by = $request->user->id;
        $member->saved();

        $firstTimer->block = $member->id;

        if($firstTimer->saved()) {
            return response()->json([
                'successMessage' => 'First timer moved to member successfully'
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function viewAll(Request $request)
    {
        $unMove = $request->query('moved') == 'no';

        $moved = $request->query('moved') == 'yes';

        $minister = (int)$request->query('minister') ? 
        (int)$request->query('minister') : '';

        $search = $request->query('search') ? 
        $request->query('search') : '';
         
        $paginate = $request->query('paginate') ? 
        $request->query('paginate') : 20; 

        $sort = $request->query('sort') ? 
        $request->query('sort') : 'id';

        $order = ($request->query('order') === 'asc' ||
        $request->query('order') === 'desc') ?
        $request->query('order') : 'desc';

        $sex = $request->query('sex') ? 
        $request->query('sex') : '';

        $email = $request->query('email') ? 
        $request->query('email') : '';

        $phone = $request->query('phone') ? 
        $request->query('phone') : '';

        $created_by = (int)$request->query('created_by') ? 
        (int)$request->query('created_by') : '';

        $updated_by = (int)$request->query('updated_by') ? 
        (int)$request->query('updated_by') : '';

        $first_name = $request->query('first_name') ? 
        $request->query('first_name') : '';

        $last_name = $request->query('last_name') ? 
        $request->query('last_name') : '';



        $firstTimers = $request->church->firstTimers()
        ->when($search, function ($query) use($search){
            return $query->where('first_name', 'ilike', '%'.$search.'%')
             ->orWhere('last_name', 'ilike', '%'.$search.'%');
        })
        ->when($minister, function ($query) use($minister){
            return $query->where('invited_by', $minister);
        })        
        ->when($sex, function ($query) use($sex){
            return $query->where('sex', $sex);
        })
        ->when($email, function ($query) use($email){
            return $query->where('email', 'ilike', '%'.$email.'%');
        })
        ->when($phone, function ($query) use($phone){
            return $query->where('phone', 'ilike', '%'.$phone.'%');
        })
        ->when($first_name, function ($query) use($first_name){
            return $query->where('first_name', 'ilike', '%'.$first_name.'%');
        })
        ->when($last_name, function ($query) use($last_name){
            return $query->where('last_name', 'ilike', '%'.$last_name.'%');
        })
        ->when($created_by, function ($query) use($created_by){
            return $query->where('created_by', $created_by);
        })
        ->when($updated_by, function ($query) use($updated_by){
            return $query->where('updated_by', $updated_by);
        })
        ->when($unMove, function ($query) use($unMove){
            return $query->where('moved', 0);
        })
        ->when($moved, function ($query) use($moved){
            return $query->where('moved', '>', 0);
        })
        ->orderBy($sort, $order)->paginate($paginate);
        

        if(!count($firstTimers)) {
            return response()->json([
                'errorMessage' => 'First timers can not be found'
            ], 404);
        }

        if(count($firstTimers)) {
            return response()->json([
                'firstTimers' => $firstTimers
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function delete(Request $request)
    {
        $firstTimer = $request->firstTimer;
        $user = $request->user;

        if($firstTimer->created_by != $user->id &&
        $request->programme->created_by != $user->id){
            return response()->json([
                'errorMessage' => 'Unauthorized'
            ], 401);
        }

        $firstTimer->update([
            'deleted_by' => $request->user->id
        ]);

        $delete = FirstTimer::destroy($firstTimer->id);
        if($delete) {
            return response()->json([
                'successMessage' => 'First timer deleted successfully'
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }
    
}
