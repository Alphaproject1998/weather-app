<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;

// This Controller handles all auth requests, such as creating users, logging in, logging out and creates JWTs.

class AuthController extends Controller
{
    // Create a new AuthController instance and require user to be authenticated with JWT for all functions with the exception of the login and register functions.
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    
    // Get a JWT via given credentials.
    public function login(Request $request){
        // Validate request parameters, for login details.
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required|string|min:6',
        ]);
        // If validation fails, return json response back with error 422 and what failed. 
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        // If the user details do not exist in database, return 401 Unauthorized, setting JWT while doing so.
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        // If no errors, return response as json with JWT, via function createNewToken.
        return $this->createNewToken($token);
    }
    
    // Register a User.
    public function register(Request $request) {
        // Validate request parameters, all details for user. using confirmed to pass password_confirmation param and check.
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'username' => 'required|string|max:100|unique:users',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
        // If validation fails, return json response back with error 422 and what failed. 
        if($validator->fails()){
            return response()->json(["errors" => $validator->errors()], 422);
        }
        // Create the user from the details given and encrypt the password
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
        // Return json message back indicating that the user was registered.
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    // Log the user out (Invalidate the token).
    public function logout() {
        // When auth is logged out the token becomes invalid.
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    
    // Refresh a token.
    public function refresh() {
        // Return new JWT + details.
        return $this->createNewToken(auth()->refresh());
    }
    
    // Get the authenticated User.
    public function userProfile() {
        // Return all user detials in json format.
        return response()->json(auth()->user());
    }
    
    // Catchall and respond with message
    public function exception() {
        // Return message in json.
        return response()->json(['error' => 'route not found']);
    }
    
    // Get the token array structure.
    protected function createNewToken($token){
        // Return new JWT, type, expire time and user details.
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
