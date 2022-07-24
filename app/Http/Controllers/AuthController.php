<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
//use Validator;

use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'accountCheck','registerAdmin','loginAdmin','showAllAdmin']]);
    }

    public function showAllAdmin(){
        //$showalladmin = User::where('account_permit', '!=', "0")->get();
        return User::where('account_permit', '!=', "0")->get();
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function accountCheck(Request $request){
        return User::where('account', '=', $request->all())->count();
        // if(User::find() ($request->all()) )
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'account' =>'required|string|min:6',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
            }
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
            }
        if( (auth()->user()->account_permit) == "0"){
            return $this->createNewToken($token);
        }else{
            return response()->json(['error' => '權限錯誤'],401);
        }
    }

    public function loginAdmin(Request $request){
        $validator = Validator::make($request->all(), [
            'account' =>'required|string',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
            }
        if (! $token = auth()->attempt(array_merge(
                $validator->validated(),
            ))){
            return response()->json(['error' => 'Unauthorized'], 401);
            }

        if( (auth()->user()->account_permit) != "0"){
            return $this->createNewToken($token);
        }else{
            return response()->json(['error' => '權限錯誤'],401);
        }
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'account' => 'required|string|min:6|unique:users',
            'name' => 'required|string|between:2,100',
            'password' => 'required|string|confirmed|min:6',
            'email' => 'required|string|email|max:100|unique:users',
            'address' => 'required|string',
            'telephone' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)],
            ['account_permit' => "0" ],
        ));
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /**
     * Register a Admin.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerAdmin(Request $request) {
        $validator = Validator::make($request->all(), [
            'account' => 'required|string|unique:users',
            'password' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)],
            //暫時設定權限為0，之後要改
            ['account_permit' => "1" ],
        ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
        ], 201);
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(auth()->user());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 600,
            'user' => auth()->user()
        ]);
    }
}
