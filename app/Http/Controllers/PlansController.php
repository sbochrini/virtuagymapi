<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exercise;
use App\Plan;
use App\DifficultyLevel;
use App\ExerciseInstance;

class PlansController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plans = Plan::orderBy('id','desc')->get();
        return response()->json($plans);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
          'text' => 'required'
        ]);

        if($validator->fails()){
          $response = array('response' => $validator->messages(), 'success' => false);
          return $response;
        } else {
          // Create plan
          $plan = new Plan();
          $plan->text = $request->input('plan_name');
          $plan->body = $request->input('plan_difficulty');
          $plan->body = $request->input('plan_description');
          $plan->save();
          // foreach ($request->input('day') as $d):
          //     $day= new PlanDay();
          //     $day->plan_id=$plan->id;
          //     $day->day_name=$d['day_name'];
          //     $day->order=$d['day_order'];
          //     $day->save();
          //     foreach ($d['exercises'] as $e):
          //         $exercise= new ExerciseInstance();
          //         $exercise->exercise_id= $e['exercise_id'];
          //         $exercise->day_id=$day->id;
          //         $exercise->order=$e['exercise_order'];
          //         $exercise->exercise_duration=$e['exercise_duration'];
          //         $exercise->save();
          //     endforeach;
          // endforeach;

          //return response()->json($plan);
        }
        return 123;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $plan = Plan::find($id);
        return response()->json($plan);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $validator = Validator::make($request->all(),[
        'text' => 'required'
      ]);

      if($validator->fails()){
        $response = array('response' => $validator->messages(), 'success' => false);
        return $response;
      } else {
        // Find an plan
        $plan = Plan::find($id);
        $plan->plan_name = $request->input('plan_name');
        $plan->plan_description = $request->input('plan_description');
        $plan->save();

        return response()->json($plan);
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      // Find an plan
      $plan = Plan::find($id);
      $days=$plan->days;
       foreach ($days as $day):
           $exercises=$day->exerciseInstances;
           foreach ($exercises as $exercise):
               $exercise->delete();
           endforeach;
           $day->delete();
       endforeach;
      $plan->delete();

      $response = array('response' => 'Plan deleted', 'success' => true);
      return $response;
    }

    public function addplanform()
    {
        $difficulty_levels=DifficultyLevel::orderBy('id', 'asc')->get();
        $exercises=Exercise::all();
        $array["difficulty_levels"]=$difficulty_levels;
        $array["exercises"]=$exercises;

        return response()->json($array);
    }
}
