<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate(
            [
            'name' => 'required|string',
            'email' => 'required|email|string|unique:users,email',
            'password' => [
                'required',
                Password::min(8)->mixedCase()->numbers()->symbols()
            ],

        ]);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);
        // $token = $user->createToken('main')->plainTextToken;

        return response([
            'message' => 'Successfully created',
            'user' => $user,
            // 'token' => $token

        ],200);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);
        $user = User::where('email',$fields['email'])-> first();

        //check password
        if(!$user || !hash::check($fields['password'], $user -> password)){
            return response([
                'message' => 'Incorrect email or password'
            ],401);
        }

        $token = $user->createToken('main')->plainTextToken;
        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response,201);
    }
    public function logout(Request $request){
        auth() ->user() -> tokens() -> delete();
        return response([
            'message' => 'Log out success'
        ]);
    }
}
