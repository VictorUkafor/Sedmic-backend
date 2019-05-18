<?php

namespace App\Http\Controllers\API;

use App\Church;
use Illuminate\Http\Request;

class ChurchController extends Controller
{

    public function show(Request $request)
    {
        $church = $request->church;

        if($church) {
            return response()->json([
                'church' => $church
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    }

  
    public function create(Request $request)
    {
        $user = auth()->user();
        $images = '';

        if($request->images){
            foreach ($request->images as $key => $image) {
                Cloudder::upload($image->getRealPath(), 
                'church'.$user->church_username.$key);

                $images .= 'church'.$user->church_username.$key.' ';
            }
        }


        $church = new Church;
        $church->name_of_church = $request->name_of_church;
        $church->username = $user->username;
        $church->official_email = $request->official_email;
        $church->venue = $request->venue;
        $church->images = $images;
        $church->minister_in_charge = $request->minister_in_charge;
        $church->contact_numbers = $request->contact_numbers;

        if($church->save()) {
            return response()->json([
                'successMessage' => 'Church created successfully',
                'church' => $church
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    }


    public function update(Request $request)
    {
        $church = $request->church;

        $church->name_of_church = $request->name_of_church ? 
        $request->name_of_church : $church->name_of_church;

        $church->username = $user->username ? 
        $user->username : $church->username;

        $church->official_email = $request->official_email ?
        $request->official_email : $church->official_email;

        $church->venue = $request->venue ? 
        $request->venue : $church->venue;

        $church->minister_in_charge = $request->minister_in_charge ?
        $request->minister_in_charge : $church->minister_in_charge;

        $church->contact_numbers = $request->contact_numbers ? 
        $request->contact_numbers : $church->contact_numbers;

        if($church->save()) {
            return response()->json([
                'successMessage' => 'Church created successfully',
                'church' => $church
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    }


    public function uploadImage(Request $request)
    {
        $church = $request->church;
        $images = $church->images;
        $count = count(explode(" ", $images));

        foreach ($request->images as $key => $image) {
            $number = $key + $count;

            Cloudder::upload($image->getRealPath(), 
            'church'.$church->username.$number);

            $images .= 'church'.$church->username.$number.' ';
        }
        
        $church->images = $images;

        if($church->save()) {
            return response()->json([
                'successMessage' => 'Image uploaded successfully',
                'church' => $church
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    }



    public function deleteImage(Request $request, $image)
    {
        $church = $request->church;

        $images = $church->images;

        $delete = Cloudder::delete($image); 

        if($delete){
          $images = str_replace($image, '', $images);  
        }

        $church->images = $images;

        if($church->save()) {
            return response()->json([
                'successMessage' => 'Image deleted successfully',
                'church' => $church
            ], 200);
        }

        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    }


}

