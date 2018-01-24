<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exercise;
use App\Plan;
use App\DifficultyLevel;
use App\ExerciseInstance;
use Validator;

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
        $difficulty_levels=DifficultyLevel::orderBy('id','asc')->get();
        $exercises=Exercise::all();
        return response()->json(array('plans'=>$plans , 'difficulty_levels'=>$difficulty_levels, 'exercises'=>$exercises));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->get('data'); 
        $validator = Validator::make($request->all(),[
          'plan_name' => 'required',
          'plan_difficulty' => 'required'
        ]);

        if($validator->fails()){
          $response = array('response' => $validator->messages(), 'success' => false);
          return $response;
        } else {
          // Create plan
          $plan = new Plan();
          $plan->plan_name = $request->input('plan_name');
          $plan->plan_difficulty = $request->input('plan_difficulty');
          $plan->plan_description = $request->input('plan_description');
          $plan->save();
          foreach ($request->input('day') as $d):
              $day= new PlanDay();
              $day->plan_id=$plan->id;
              $day->day_name=$d['day_name'];
              $day->order=$d['day_order'];
              $day->save();
              foreach ($d['exercises'] as $e):
                  $exercise= new ExerciseInstance();
                  $exercise->exercise_id= $e['exercise_id'];
                  $exercise->day_id=$day->id;
                  $exercise->order=$e['exercise_order'];
                  $exercise->exercise_duration=$e['exercise_duration'];
                  $exercise->save();
              endforeach;
          endforeach;

          return response()->json($plan);
        }
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
        $difficulty_levels=Difficulty::orderBy('id','asc')->get();
        $exercises=Exercise::all();
        return response()->json(array('plan'=>$plan, 'difficulty_levels'=>$difficulty_levels, 'exercises'=>$exercises));
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

    public function edit($id)
    {
      $plan=Plan::find($id);
      $days=$plan->days;
      foreach ($days as $day) {
        $day_exercises=$day->exerciseInstances;
      }
      $exercises=Exercise::all();
      $difficulty_levels=DifficultyLevel::orderBy('id', 'asc')->get();
      $array['plan']=$plan;
      $array['exercises']=$exercises;
      $array['difficulty_levels']=$difficulty_levels;
      return response()->json($array);
    }
}
