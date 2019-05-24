<?php

namespace App\Http\Controllers\API;

use App\Aggregate;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AggregateController extends Controller
{
    // create aggregate
    public function create(Request $request)
    {
        $user = $request->user;
        $church = $request->church;
    
        $sub_unit_type = strtolower(preg_replace('/\s+/', '_', $request->sub_unit_type));
    
        $image = $request->image ? 
        $church->username.$sub_unit_type.md5(microtime(true).mt_Rand()) : '';
    
        $aggregate = new Aggregate;
        $aggregate->church_id = $church->id;
        $aggregate->name = strtolower($request->name);
        $aggregate->sub_unit_type = $sub_unit_type;
        $aggregate->level = $request->level;
        $aggregate->handlers = $request->handlers;
        $aggregate->image = $image;
        $aggregate->description = $request->description;
        $aggregate->created_by = $user->id;
        
        
        if($aggregate->save()) {

            if($request->image){
                Cloudder::upload($request->image->getRealPath(), $image);
            }
            
            return response()->json([
                'successMessage' => $aggregate->name.' created successfully',
                'aggregate' => $aggregate
            ], 201);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    }
    
    
    // view all aggregates
    public function viewAll(Request $request)
    {
        foreach($request->aggregates as $aggregate){
            $allAggregates[$aggregate->sub_unit_type][] = $aggregate;
        }
        
        if(count($allAggregates)) {
            return response()->json([
                'allAggregates' => $allAggregates
            ], 200);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    
    }


    // view a single aggregate
    public function show(Request $request)
    {
        if($request->aggregate) {
            return response()->json([
                    'aggregate' => $request->aggregate
                ], 200);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error'
        ], 500);
    }
        
        
    // updates aggregate
    public function update(Request $request)
    {
        $church = $request->church;
        $aggregate = $request->aggregate;

        // get sub_unit_type to be updated
        $sub_unit_type = $request->sub_unit_type ?
        strtolower(preg_replace('/\s+/', '_', $request->sub_unit_type)) :
        $aggregate->sub_unit_type;

        // get image to be updated
        $image = $request->image ? 
        $church->username.preg_replace('/\s+/', '_', $sub_unit_type).md5(microtime(true).mt_Rand()) :
        $aggregate->image;


        // update logic
        $aggregate->name = $request->name ? 
        strtolower($request->name) : $aggregate->name;

        $aggregate->sub_unit_type = $sub_unit_type;

        $aggregate->description = $request->description ? 
        $request->description : $aggregate->description;

        $aggregate->image = $image;
        $aggregate->updated_by = $request->user->id;
            
        if($aggregate->save()) {
            return response()->json([
                'successMessage' => $aggregate->sub_unit_type.' updated successfully',
                'aggregate' => $aggregate
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
            $aggregate = $request->aggregate;

            $handlers = '';

            foreach(explode(" ", preg_replace('/\s+/', ' ', $request->handlers)) as $handler){
                if(!in_array($handler, explode(" ", trim(
                    preg_replace('/\s+/', ' ', $aggregate->handlers))))){
                    $handlers .= ' '.$handler;
                }

            }

            $aggregate->handlers = $request->handlers ? 
            trim(preg_replace('/\s+/', ' ', $aggregate->handlers.' '.$handlers)) : 
            $aggregate->handlers;

            $aggregate->updated_by = $request->user->id;

            if($aggregate->save()) {
                return response()->json([
                    'successMessage' => $aggregate->sub_unit_type.' updated successfully',
                    'aggregate' => $aggregate
                ], 200);
            }
            
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);
        }


        // removes an handler
        public function removeHandler(Request $request, $aggregate_id, $handler)
        {
            $aggregate = $request->aggregate;
            $handlers = trim(str_replace($handler, '', $aggregate->handlers)); 
    
            $aggregate->handlers = $handlers;
            $aggregate->updated_by = $request->user->id;

            if($aggregate->save()) {
                return response()->json([
                    'successMessage' => $aggregate->sub_unit_type.' updated successfully',
                    'aggregate' => $aggregate
                ], 200);
            }
            
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);
        }
        
        // deletes unit
        public function delete(Request $request, $aggregate_id)
        {
            $image = $request->aggregate->image; 
            $user = $request->user;

            $aggregate = Aggregate::where('id', $aggregate_id)
            ->update(['deleted_by' => $user->id]);
            
            if($aggregate) {
                Aggregate::destroy($aggregate_id);
                if($image){ Cloudder::delete($image); } 
                return response()->json([
                    'successMessage' => 'Aggregate deleted successfully',
                ], 200);
            }
    
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);
        } 
    
}
