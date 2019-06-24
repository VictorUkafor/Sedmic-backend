<?php

namespace App\Http\Controllers\API;

use App\Slip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SlipController extends Controller
{
    public function create(Request $request)
    {

        $slip = new Slip;
        $slip->church_id = $request->church->id;
        $slip->campaign = $request->campaign;
        $slip->first_name = $request->first_name;
        $slip->last_name = $request->last_name;
        $slip->sex = $request->sex;
        $slip->email = $request->email;
        $slip->phone = $request->phone;
        $slip->address = $request->address;
        $slip->ministered_by = $request->ministered_by;
        $slip->created_by = $request->user->id;

        if($slip->save()) {
            return response()->json([
                'successMessage' => 'Slip created successfully',
                'slip' => $slip
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function update(Request $request)
    {
        $slip = $request->slip;

        $slip->campaign = $request->campaign  ?
        $request->campaign : $slip->campaign;

        $slip->first_name = $request->first_name ?
        $request->first_name : $slip->first_name;

        $slip->last_name = $request->last_name ?
        $request->last_name : $slip->last_name;

        $slip->sex = $request->sex ?
        $request->sex : $slip->sex;
        
        $slip->email = $request->email ?
        $request->email : $slip->email;  

        $slip->phone = $request->phone ?
        $request->phone : $slip->phone;  

        $slip->address = $request->address ?
        $request->address : $slip->address;  

        $slip->ministered_by = $request->ministered_by ?
        $request->ministered_by : $slip->ministered_by; 

        $slip->moved = (int)$request->moved ?
        (int)$request->moved : $slip->moved; 

        $slip->updated_by = $request->user->id;


        if($slip->save()) {
            return response()->json([
                'successMessage' => 'Slip updated successfully',
                'slip' => $slip
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function show(Request $request)
    {
        $slip = $request->slip;

        if($slip) {
            return response()->json([
                'slip' => $slip
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function viewAll(Request $request)
    {
        $unMove = $request->query('moved') == 'no';

        $moved = $request->query('moved') == 'yes';

        $campaign = $request->query('campaign') ? 
        $request->query('campaign') : '';

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



        $slips = $request->church->slips()
        ->when($search, function ($query) use($search){
            return $query->where('first_name', 'ilike', '%'.$search.'%')
             ->orWhere('last_name', 'ilike', '%'.$search.'%');
        })
        ->when($campaign, function ($query) use($campaign){
            return $query->where('campaign', $campaign);
        })
        ->when($minister, function ($query) use($minister){
            return $query->where('ministered_by', $minister);
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



        if(!count($slips)) {
            return response()->json([
                'errorMessage' => 'Slips can not be found'
            ], 404);
        }

        if(count($slips)) {
            return response()->json([
                'slips' => $slips
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }


    public function delete(Request $request)
    {
        $slip = $request->slip;

        $slip->update([
            'deleted_by' => $request->user->id
        ]);

        $deleteSlip = Slip::destroy($slip->id);
        if($deleteSlip) {
            return response()->json([
                'successMessage' => 'Slip deleted successfully'
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);

    }
}
