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
        $request->first_name.$request->last_name.$user->church_username : '';

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
        $member->birthday = $request->birthday;
        $member->age_category = $request->age_category;
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
        $request->query('search') : '';
         
        $paginate = $request->query('paginate') ? 
        $request->query('paginate') : 20; 

        $sort = $request->query('sort') ? 
        $request->query('sort') : 'id';

        $order = $request->query('order') ? 
        $request->query('order') : 'desc';

        $sex = $request->query('sex') ? 
        $request->query('sex') : '';

        $marital_status = $request->query('marital_status') ? 
        $request->query('marital_status') : '';

        $occupation = $request->query('occupation') ? 
        $request->query('occupation') : '';

        $email = $request->query('email') ? 
        $request->query('email') : '';

        $phone = $request->query('phone') ? 
        $request->query('phone') : '';

        $created_by = $request->query('created_by') ? 
        $request->query('created_by') : '';

        $updated_by = $request->query('updated_by') ? 
        $request->query('updated_by') : '';

        $age_category = $request->query('age_category') ? 
        $request->query('age_category') : '';

        $first_name = $request->query('first_name') ? 
        $request->query('first_name') : '';

        $middle_name = $request->query('middle_name') ? 
        $request->query('middle_name') : '';

        $last_name = $request->query('last_name') ? 
        $request->query('last_name') : '';

        $time_exact = $request->query('time_exact') ?
        $request->query('time_exact') : '';

        $time_from = $request->query('time_from') ?
        $request->query('time_from') : '';

        $time_untill = $request->query('time_untill') ?
        $request->query('time_untill') : '';

        $birth_month = $request->query('birth_month') ?
        $request->query('birth_month') : '';


        $church = $request->church;

        $members = Member::where('church_id', $church->id)
        ->when($search, function ($query) use($search){
            return $query->where('first_name', 'ilike', '%'.$search.'%')
             ->orWhere('last_name', 'ilike', '%'.$search.'%')
             ->orWhere('middle_name', 'ilike', '%'.$search.'%');
        })
        ->when($sex, function ($query) use($sex){
            return $query->where('sex', $sex);
        })
        ->when($marital_status, function ($query) use($marital_status){
            return $query->where('marital_status', $marital_status);
        })
        ->when($occupation, function ($query) use($occupation){
            return $query->where('occupation', $occupation);
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
        ->when($middle_name, function ($query) use($middle_name){
            return $query->where('middle_name', 'ilike', '%'.$middle_name.'%');
        })
        ->when($created_by, function ($query) use($created_by){
            return $query->where('created_by', $created_by);
        })
        ->when($updated_by, function ($query) use($updated_by){
            return $query->where('updated_by', $updated_by);
        })
        ->when($age_category, function ($query) use($age_category){
            return $query->where('age_category', $age_category);
        })
        ->when($time_exact, function ($query) use($time_exact){
            return $query->where('date', $time_exact);
        })
        ->when($time_from, function ($query) use($time_from){
            return $query->where('date', '>=', $time_from);
        })
        ->when($time_untill, function ($query) use($time_untill){
            return $query->where('date', '<=', $time_untill);
        })
        ->when($birth_month, function ($query) use($birth_month){
            return $query->whereMonth('date_of_birth', $birth_month)
            ->orWhereMonth('birthday', $birth_month);
        })
        ->orderBy($sort, $order)
        ->paginate($paginate);

        if($members) {         
            return response()->json([
                'members' => $members
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Member can not be found'
        ], 404);
    }


    public function show(Request $request)
    {

        $member = $request->member;

        if($member) { 
            return response()->json([
                'member' => $member
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Member can not be found'
        ], 404);
    }


    public function update(Request $request)
    {

        $member = $request->member;
        $user = $request->user;

        // get firstname
        $first_name = $request->first_name ? 
        $request->first_name : $member->first_name;

        // get lastname 
        $last_name = $request->last_name ? 
        $request->last_name : $member->last_name;

        // set image public_id for cloudinary
        $image = $request->image ? 
        $first_name.$last_name.$user->church_username :
        $member->image;


        // update member
        $member->first_name = $first_name; 

        $member->middle_name = $request->middle_name ? 
        $request->middle_name : $member->middle_name;;

        $member->last_name = $last_name;

        $member->sex = $request->sex ? 
        $request->sex: $member->sex;

        $member->marital_status = $request->marital_status ?
        $request->marital_status: $member->marital_status;

        $member->phone = $request->phone ? 
        $request->phone: $member->phone;

        $member->email = $request->email ?
        $request->email : $member->email;

        $member->address = $request->address ? 
        $request->address: $member->address;

        $member->image = $image;

        $member->date_of_birth = $request->date_of_birth ? 
        $request->date_of_birth: $member->date_of_birth;

        $member->occupation = $request->occupation ?
        $request->occupation: $member->occupation;

        $member->birthday = $request->birthday ?
        $request->birthday: $member->birthday;

        $member->age_category = $request->age_category ?
        $request->age_category : $member->age_category;

        $member->updated_by = $user->id;


        if($member->save()) { 
                    
            if($request->image){
                if(Cloudder::delete($member->image)){
                    Cloudder::upload($request->image->getRealPath(), $image);
                }     
            }

            return response()->json([
                'successMessage' => 'Member updated successfully',
                'member' => $member
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    }


    public function delete(Request $request, $member_id)
    {
        
        $image = $request->member->image; 
        $user = $request->user;
        $member = Member::where('id', $member_id)
        ->update(['deleted_by' => $user->id]);
        
        if($member) {
            Member::destroy($member_id);
            if($image){ Cloudder::delete($image); } 
            return response()->json([
                'successMessage' => 'Member deleted successfully',
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    }
  
}
