<?php

namespace App\Http\Controllers\API;

use App\Unit;
use App\Http\Controllers\Controller;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    
    // create unit
    public function create(Request $request)
    {
        $user = $request->user;
        $church = $request->church;

        $image = $request->image ? 
        $church->username.str_replace(' ', '', $request->name).md5(microtime(true).mt_Rand()) : '';

        $type = strtolower(str_replace(' ', '_', $request->type));

        $unit = new Unit;
        $unit->name = strtolower($request->name);
        $unit->church_id = $church->id;
        $unit->type = $type;
        $unit->handlers = $request->handlers;
        $unit->image = $image;
        $unit->description = $request->description;
        $unit->created_by = $user->id;


        if($unit->save()) {

            if($request->image){
                Cloudder::upload($request->image->getRealPath(), $image);
            }
            return response()->json([
                'successMessage' => $type.' created successfully',
                'unit' => $unit
            ], 201);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    }
    
    // view all units
    public function viewAll(Request $request)
    {        
        foreach($request->units as $unit){
            $allUnits[$unit->type][] = $unit;
        }
        
        if($allUnits) {
            return response()->json([
                'allUnits' => $allUnits
            ], 200);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    }
    
    
    // view a single unit
    public function show(Request $request)
        {            
            if($request->unit) {
                return response()->json([
                    'unit' => $request->unit
                ], 200);
            }
            
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);
        }


    // update unit
    public function update(Request $request)
        {

            $church = $request->church;
            $unit = $request->unit;

            $name = $request->name ? 
            strtolower($request->name) : $unit->name;

            $image = $request->image ? 
            $church->username.str_replace(' ', '', $name).md5(microtime(true).mt_Rand()) :
            $unit->image;

            $unit->name = $name;

            $unit->type = $request->type ? 
            strtolower(str_replace(' ', '_', $request->type)) :
            $unit->type;

            $unit->description = $request->description ? 
            $request->description : $unit->description;

            $unit->image = $image;
            $unit->updated_by = $request->user->id;
            
            if($unit->save()) {
                return response()->json([
                    'successMessage' => $unit->type.' updated successfully',
                    'unit' => $unit
                ], 200);
            }
            
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);
        }


    // update unit
    public function addHandlers(Request $request)
        {

            $church = $request->church;
            $unit = $request->unit;


            $unit->handlers = $request->handlers ? 
            $unit->handlers.' '.$request->handlers : $unit->handlers;

            $unit->updated_by = $request->user->id;

            if($unit->save()) {
                return response()->json([
                    'successMessage' => $unit->type.' updated successfully',
                    'unit' => $unit
                ], 200);
            }
            
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);
        }


        public function RemoveHandler(Request $request, $handler)
        {
            $unit = $request->unit;
            $handlers = str_replace($unit->handlers, '', $handler); 
    
            $unit->handlers = $handlers;
            $unit->updated_by = $request->user->id;

            if($unit->save()) {
                return response()->json([
                    'successMessage' => $unit->type.' updated successfully',
                    'unit' => $unit
                ], 200);
            }
            
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);
        }


        public function delete(Request $request, $unit_id)
        {
            $image = $request->unit->image; 
            $user = $request->user;

            $unit = Unit::where('id', $unit_id)
            ->update(['deleted_by' => $user->id]);
            
            if($unit) {
                Unit::destroy($unit_id);
                if($image){ Cloudder::delete($image); } 
                return response()->json([
                    'successMessage' => 'Unit deleted successfully',
                ], 200);
            }
    
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);
        }        


}
