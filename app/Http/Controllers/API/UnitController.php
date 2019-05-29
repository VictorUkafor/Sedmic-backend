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

        $type = strtolower(preg_replace('/\s+/', '_', $request->type));

        $image = $request->image ? 
        $church->username.$type.md5(microtime(true).mt_Rand()) : '';

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
            $unit = $request->unit;
            $unit->members = $unit->members;
            
            if($unit) {
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

            // get type to be updated
            $type = $request->type ? 
            strtolower(preg_replace('/\s+/', '_', $request->type)) :
            $unit->type;

            // get image to be updated
            $image = $request->image ? 
            $church->username.preg_replace('/\s+/', '_', $type).md5(microtime(true).mt_Rand()) :
            $unit->image;


            // update logic
            $unit->name = $request->name ? 
            strtolower($request->name) : $unit->name;

            $unit->type = $type;

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


    // adds an handler
    public function addHandlers(Request $request)
        {

            $church = $request->church;
            $unit = $request->unit;

            $handlers = '';

            foreach(explode(" ", preg_replace('/\s+/', ' ', $request->handlers)) as $handler){
                if(!in_array($handler, explode(" ", trim(
                    preg_replace('/\s+/', ' ', $unit->handlers))))){
                    $handlers .= ' '.$handler;
                }

            }

            $unit->handlers = $request->handlers ? 
            trim(preg_replace('/\s+/', ' ', $unit->handlers.' '.$handlers)) : 
            $unit->handlers;

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


        // removes an handler
        public function removeHandler(Request $request, $unit_id, $handler)
        {
            $unit = $request->unit;
            $handlers = trim(str_replace($handler, '', $unit->handlers)); 
    
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


        // deletes unit
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
