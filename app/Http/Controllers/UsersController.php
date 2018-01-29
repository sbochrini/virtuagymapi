<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Plan;
use App\PlanDay;
use App\DifficultyLevel;
use App\Exercise;
use App\ExerciseInstance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Validator;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $users = User::orderBy('id', 'desc')->get();
      $plans = Plan::all();

      return response()->json(array('users'=>$users,'plans'=>$plans));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      // TODO: validator
      $messages=[
        'firstname.required' => 'Firstame is required!',
        'lastname.required' => 'Lastame is required!',
        'email.required' => 'Email is required!',
        'email.email' => 'Email must be a valid email address!',
        'email.unique' => 'The email address already exists!'
      ];
      $validator = Validator::make($request->all(),[
          'firstname' => 'required',
          'lastname' => 'required',
          'email' => 'required | email | unique:users,email',
          //'phone' => 'required',
        ],$messages);
        if($validator->fails()){
          $response = array('msg' => $validator->messages(), 'success' => false);
          return $response;
        }else{
        $user = new User();
        $user->firstname=$request->firstname;
        $user->lastname=$request->lastname;
        $user->lastname=$request->lastname;
        $user->email=$request->email;
        $user->phone=$request->phone;
        $user->save();
        // TODO: check an den uparxoun
        foreach ($request->plans as $plan):
            DB::table('user_plans')->insert(
                ['user_id' => $user->id, 'plan_id' => $plan]
            );
        endforeach;
        return response()->json(array('user'=>$user, 'success'=>true));
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

      $user = User::find($id);
      $userplans_ids=DB::table('user_plans')->select("plan_id")->where('user_id', $id)->pluck('plan_id');

      if($userplans_ids !== null){
          $userplans=Plan::whereIn('id',$userplans_ids)->get();
          if($userplans!==null){
            foreach ($userplans as $plan) {
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
            }
          }
      }

       return response()->json(array('user'=>$user, 'userplans'=>$userplans));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $user=User::find($id);
      $user_plans=$user->plans->pluck('id');
      // $user_plans=DB::table('user_plans')
      //     ->select('plan_id')
      //     ->whereRaw('user_id = :id',[ 'id' => $id] )
      //     ->get();
      $plans=Plan::select('id','plan_name')->get();
      $array['user']=$user;
      $array['user_plans']= $user_plans;
      $array['plans']=$plans;
      return response()->json($array);
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
      $messages=[
        'firstname.required' => 'Firstame is required!',
        'lastname.required' => 'Lastame is required!',
        'email.required' => 'Email is required!',
        'email.email' => 'Email must be a valid email address!'
      ];
      $validator = Validator::make($request->all(),[
          'firstname' => 'required',
          'lastname' => 'required',
          'email' => 'required | email',
          //'phone' => 'required',
        ],$messages);
        if($validator->fails()){
          $response = array('msg' => $validator->messages(), 'success' => false);
          return $response;
        }else{
          $user = User::find($id);
          $user->firstname = Input::get('firstname');
          $user->lastname = Input::get('lastname');
          $user->email = Input::get('email');
          $user->phone = Input::get('phone');
          $user_plans=DB::table('user_plans')
              ->select('*')
              ->whereRaw('user_id = :id',[ 'id' => $id] )
              ->delete();
          $input_plans=Input::get('plans');
          if(count($input_plans)>0){
              foreach ($input_plans as $plan):
                  DB::table('user_plans')->insert(
                      ['user_id' => $user->id, 'plan_id' => $plan]
                  );
              endforeach;
          }else{
              DB::table('user_plans')->insert(
                  ['user_id' => $user->id, 'plan_id' => $input_plans]
              );
          }
          $user->update();
          return response()->json(array('user'=>$user,'success'=>true));
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
      $user=User::find($id);
      $user->delete();
      DB::table('user_plans')
          ->select('*')
          ->whereRaw('user_id = :id',[ 'id' => $id] )
          ->delete();
    }

    public function addusersform()
    {
        $plans=Plan::select('id','plan_name')->get()->toArray();

        return response()->json($plans);
    }
}
