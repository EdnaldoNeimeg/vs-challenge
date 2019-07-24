<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;

use Lcobucci\JWT\Parser;
use DB;

class UserController extends Controller 
{
    public $successStatus = 200;
    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function login(){ 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')-> accessToken;
            $success['user'] = $user->toJson();
            return response()->json(['success' => $success], $this-> successStatus); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }

    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function logout(Request $request){
        $token = $request->bearerToken();
        if ($token) {
            $id = (new Parser())->parse($token)->getHeader('jti');
            $revoked = DB::table('oauth_access_tokens')->where('id', '=', $id)->update(['revoked' => 1]);
            if(Auth::guard()->logout()){
                $code = $this-> successStatus;
                $data = ['success' => true];
            } else {
                $code = 500;
                $data = ['error'=>'There was an error when trying to logout'];
            }
        } else {
            $code = 400;
            $data = ['error'=>'You must provide the token'];
        }
        
        Auth::logout();
        
        return response()->json($data, $code);
    }

    /** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email', 
            'password' => 'required', 
            'password_confirmation' => 'required|same:password', 
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']); 
        $user = User::create($input); 
        $success['token'] =  $user->createToken('MyApp')-> accessToken; 
        $success['user'] =  $user->toJson();
        return response()->json(['success'=>$success], $this-> successStatus); 
    }
    /** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function details() 
    { 
        $user = Auth::user(); 
        return response()->json(['success' => $user], $this-> successStatus); 
    } 
}