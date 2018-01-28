<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exercise;
use App\Plan;
use App\PlanDay;
use App\DifficultyLevel;
use App\ExerciseInstance;
use Illuminate\Support\Facades\Input;
use Validator;
use App\Mail\DeletePlanMail;
use App\Mail\UpdatePlanMail;

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
        $messages = [
            'plan_name.required' => 'Workout plan name is required!',
            'plan_difficulty.required' => 'Workout plan difficulty is required!',
            'days.day_name.required' => 'Plan day anme is required!',
            'days.*.exercises.*.exercise_duration.numeric'=>'Exercise duration must be a number!'
        ];
        $data =json_decode($request->data,true);
        $validator = Validator::make($data,[
          'plan_name' => 'required',
          'plan_difficulty' =>'required',
          'days.day_name' => 'required',
          'days.*.exercises.*.exercise_duration'=>'numeric'
        ],$messages);
        if($validator->fails()){
          $response = array('msg' => $validator->messages(), 'success' => false);
          return $response;
        } else {
          // Create plan
          $plan = new Plan();
          $plan->plan_name = $data['plan_name'];
          $plan->plan_difficulty = $data['plan_difficulty'];
          $plan->plan_description = $data['plan_description'];
          $plan->save();
          foreach ($data['days'] as $d):
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
          return response()->json(array('plan'=>$plan,'success'=>true));
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
        $difficulty=$plan->difficulty;
        $days=$plan->days;
        if($days!==null){
          foreach ($days as $day) {
            $exercise_instances=$day->exerciseInstances;
            foreach ($exercise_instances as $exercise_instance) {
              if($exercise_instance!==null){
                $exercise_instance->exerciseName;
              }
            }
          }
        }
        return response()->json(array('plan'=>$plan));
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
      $data =json_decode($request->data,true);
      $messages = [
        'plan_name.required' => 'Workout plan name is required!',
        'plan_difficulty.required' => 'Workout plan difficulty is required!',
        'days.day_name.required' => 'Plan day anme is required!',
        'days.*.exercises.*.exercise_duration.numeric'=>'Exercise duration must be a number!'
      ];
      $validator = Validator::make($data,[
        'plan_name' => 'required',
        'plan_difficulty' =>'required',
        'days.day_name' => 'required',
        'days.*.exercises.*.exercise_duration'=>'numeric'
      ],$messages);
      if($validator->fails()){
        $response = array('msg' => $validator->messages(), 'success' => false);
        return $response;
      } else {
        // Find an plan
        $plan = Plan::find($id);
        $plan->plan_name = $data['plan_name'];
        $plan->plan_difficulty = $data['plan_difficulty'];
        $plan->plan_description = $data['plan_description'];
        if(isset($plan->days)){
            foreach ($plan->days as $day):
                if(isset($day->exerciseInstances)){
                    foreach($day->exerciseInstances as $exercise_instance):
                        $exercise_instance->delete();
                    endforeach;
                }
                $day->delete();
            endforeach;
        }
        if(isset($data['days'])){
            foreach ($data['days'] as $d):
                $day= new PlanDay();
                $day->plan_id=$plan->id;
                $day->day_name=$d['day_name'];
                $day->order=$d['day_order'];
                $day->save();
                if(isset($d['exercises'])){
                    foreach ($d['exercises'] as $e):
                      if($e['exercise_id']!==""){
                        $exercise= new ExerciseInstance();
                        $exercise->exercise_id= $e['exercise_id'];
                        $exercise->day_id=$day->id;
                        $exercise->order=$e['exercise_order'];
                        $exercise->exercise_duration=$e['exercise_duration'];
                        $exercise->save();
                      }
                    endforeach;
                }
            endforeach;
        }

        $plan->update();
        $users=$plan->users;
        foreach($users as $user){
            \Mail::to($user)->send(new UpdatePlanMail($user,$plan));
        }
        return response()->json(array('plan'=>$plan,'success'=>true));
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
      if(isset($days)){
        foreach ($days as $day):
            $exercises=$day->exerciseInstances;
            if(isset($exercises)){
              foreach ($exercises as $exercise):
                  $exercise->delete();
              endforeach;
            }
            $day->delete();
        endforeach;
      }
      $plan->delete();

      $users=$plan->users;
      foreach($users as $user){
          \Mail::to($user)->send(new DeletePlanMail($user,$plan));
      }

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

    public function addexerisedropdown(Request $request)
    {
        $exercises=Exercise::all();
        return response()->json(array('exercises'=>$exercises));
    }

}
