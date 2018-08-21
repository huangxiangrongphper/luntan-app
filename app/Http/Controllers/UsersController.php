<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Http\Requests;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function register()
    {
        return view('users.register');
    }

    public function index()
    {
        //
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
    public function store(Requests\UserRegisterRequest $request)
    {
        $data = [
            'confirm_code'=>str_random(48),
            'avatar'=>'/images/default-avatar.png'
        ];
      $user = User::create(array_merge($request->all(),$data));

        $subject = 'Confirm Your Email';
        $view = 'email.register';

        $this->sendTo($user,$subject,$view,$data);
        
        return redirect('/');
    }

    public function confirmEmail($confirm_code)
    {
        $user = User::where('confirm_code',$confirm_code)->first();
        if(is_null($user)){
            return redirect('/');
        }
        $user->is_confirmed = 1;
        $user->confirm_code = str_random(48);
        $user->save();

        return redirect('user/login');
    }

    public function login()
    {
        return view('users.login');
    }

    public function signin(Requests\UserLoginRequest $request)
    {
        if(\Auth::attempt([
            'email'=>$request->get('email'),
            'password'=>$request->get('password'),
            'is_confirmed' => 1
        ])){
            $path = \Session ::get('discount_id') != '' ? '/discussions/' . \Session::get('discount_id') : '/';
            \Session::forget('discount_id');
            return redirect($path);
        }
        \Session::flash('user_login_failed','密码不正确或邮箱没有验证');
        return redirect('/user/login')->withInput();
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function sendTo($user, $subject, $view, $data=[])
    {
        \Mail::queue($view,$data,function($message) use ($user,$subject){
            $message->to($user->email)->subject($subject);
        });
    }

    public function logout()
    {
        \Auth::logout();
        return redirect('/');
    }
}
