<?php

namespace App\Http\Controllers\API;

use App\Member;
use App\Http\Controllers\Controller;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Http\Request;

class MemberController extends Controller
{

    public function create(Request $request)
    {
        $user = $request->user;
        $church = $request->church;
        $image = $request->image ? 
        $request->first_name.$request->last_name : '';

        $member = new Member;

        $member->church_id = $church->id;
        $member->first_name = $request->first_name;
        $member->middle_name = $request->middle_name;
        $member->last_name = $request->last_name;
        $member->sex = $request->sex;
        $member->marital_status = $request->marital_status;
        $member->phone = $request->phone;
        $member->email = $request->email;
        $member->address = $request->address;
        $member->image = $image;
        $member->date_of_birth = $request->date_of_birth;
        $member->occupation = $request->occupation;
        $member->created_by = $user->id;


        if($member->save()) {         
            if($request->image){
                Cloudder::upload($request->image->getRealPath(), $image);   
            }
            return response()->json([
                'successMessage' => 'Member created successfully',
                'member' => $member
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    }



    public function viewAll(Request $request)
    {
        $search = $request->query('search') ? 
        $request->query('search') : 'ale';
         
        $paginate = $request->query('paginate') ? 
        $request->query('paginate') : 20; 

        $sort = $request->query('sort') ? 
        $request->query('sort') : 'id';

        $order = $request->query('order') ? 
        $request->query('order') : 'asc';

        $church = $request->church;

        $members = Member::where('church_id', $church->id)
        ->where('first_name', 'LIKE', '%'.$search.'%')
        ->orWhere('last_name', 'LIKE', '%'.$search.'%')
        ->orWhere('middle_name', 'LIKE', '%'.$search.'%')
        ->orderBy($sort, $order)
        ->paginate($paginate);

        if($members) {         
            return response()->json([
                'members' => $members
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'member can not be found'
        ], 404);
    }
  
}
