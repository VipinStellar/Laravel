<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon; 
use DB;
use Tymon\JWTAuth\Facades\JWTAuth;
class AuthController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register','forgotPassword']]);

    }

    public function login(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make(
            $request->all(),
            [
                'email'    => 'required',
                'password' => 'required|string|min:6',
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $token_validity = (5 * 60);

        $this->guard()->factory()->setTTL($token_validity);
        $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'emp_code';

        if (!$token = $this->guard()->attempt(array($fieldType => $input['email'], 'password' => $input['password']))) {
            return response()->json(['error' => 'Unauthorized'], 400);
        }

        return $this->respondWithToken($token);

    }

    public function logout()
    {
        $this->guard()->logout();
        return response()->json(['message' => 'User logged out successfully']);

    }

    public function profile()
    {
        return response()->json($this->guard()->user());

    }

    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());

    }

    protected function respondWithToken($token)
    {
        $user = User::find(auth()->user()->id);
        if($user->token != null)
        JWTAuth::setToken($user->token)->invalidate();
        $user->token = $token;
        $user->save();
        return response()->json(
            [
                'id' =>auth()->user()->id,
                'email' =>auth()->user()->email,
                'name' =>auth()->user()->name,
                'emp_code' =>auth()->user()->emp_code,
                'role_id' =>auth()->user()->role_id,
                'supervisor_id' =>auth()->user()->supervisor_id,
                'token'          => $token,
                'token_type'     => 'bearer',
                'token_validity' => ($this->guard()->factory()->getTTL()),
               // 'token_validity' => (5),
            ]
        );

    }

    protected function guard()
    {
        return Auth::guard();

    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email'    => 'required|email|exists:users',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $password = bcrypt(rand());
        $user = User::where('email', '=', $request->email)->first();
        $user->password = $password;
        $user->save();
       return response()->json(["msg" => 'Reset password link sent on your email id.']);

    }

}
