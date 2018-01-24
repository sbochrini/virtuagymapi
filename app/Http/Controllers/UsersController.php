<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Plan;
use Illuminate\Support\Facades\DB;

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
      return response()->json($users);
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
      //$data = $request->all();
      $user = new User();
      $user->firstname=$request->input('firstname');
      $user->lastname=$request->input('lastname');
      $user->email=$request->input('email');
      $user->phone=$request->input('phone');
      $user->save();
      foreach ($request->input('plans') as $plan):
          DB::table('user_plans')->insert(
              ['user_id' => $user->id, 'plan_id' => $plan]
          );
      endforeach;
      return response()->json($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
      $user_plans=DB::table('user_plans')
          ->select('plan_id')
          ->whereRaw('user_id = :id',[ 'id' => $id] )
          ->get();
      $plans=Plan::select('id','plan_name')->get();
      $array['user']=$user;
      $array['user_plans']=$user_plans;
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
      $user->save();
      return response()->json($user);
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