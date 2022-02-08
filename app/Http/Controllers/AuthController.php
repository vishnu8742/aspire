<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helper\Reply;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register User
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function register(Request $request){
        $validated = $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|max:32',
            'name'  => 'required|max:55',
            'phone' => 'required|digits_between:10,15|unique:users',
        ]);

        $user = new \App\Models\User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->phone = $request->phone;

        $user->save();

        return Reply::successWithData('Registration Success', [
            "authToken" =>  $user->createToken($user->name)->plainTextToken,
            "name"      =>  $user->name,
            "phone"     =>  $user->phone,
            "email"     =>  $user->email,
            "role"      =>  $user->role,
        ]);
    }

    /**
     * Check credentials and return auth token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    function login(Request $request){
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        $user = \App\Models\User::where('email', $request->email)->firstOrFail();

        if(Hash::check($request->password, $user->password)){
            return Reply::successWithData('Login Success', [
                "authToken" =>  $user->createToken($user->name)->plainTextToken,
                "name"      =>  $user->name,
                "phone"     =>  $user->phone,
                "email"     =>  $user->email,
                "role"      =>  $user->role,
            ]);
        }else{
            return Reply::error('Invalid Credentials');
        }
    }
}
