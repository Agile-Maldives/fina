<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Auth;
use Validator;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Auth\Events\Verified;
class UserApiController extends Controller
{
    use VerifiesEmails;


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users= DB::table('users')->get();

        return response()->json([
            "status" => "success",
            "users" => $users,
        ],200);
    }




    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            if($user->email_verified_at !== NULL){
                $success['message'] = "Login successfull";
                return response()->json(['success' => $success], $this-> successStatus);
            }else{
                return response()->json(['error'=>'Please Verify Email'], 401);
            }
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
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
            'c_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $user->sendApiEmailVerificationNotification();
        $success['message'] = 'Please confirm yourself by clicking on verify user button sent to you on your email';

        return response()->json(['success'=>$success], $this-> successStatus);

        return response()->json([
            "status" => "success",
        ],200);

    }

    /**
     * Create user api
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'role_admin' => 'required|numeric',
            'role_moderator' => 'required|numeric',
            'role_standard' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $data = $request->all();
        $_name = $data['name'];
        $_email = $data['email'];
        $_password = $data['password'];
        $_role_admin = $data['role_admin'];
        $_role_moderator = $data['role_moderator'];
        $_role_standard = $data['role_standard'];

        $user = new User();
        $user->name= $_name;
        $user->email = $_email;
        $user->password = bcrypt($_password);
        $user->role_admin = $_role_admin;
        $user->role_moderator = $_role_moderator;
        $user->role_standard = $_role_standard;
        $user->save();

        return response()->json([
            "status" => "success",
        ],200);

    }



    public function update(Request $request, $id)
    {
        $requestData = $request->all();
        $requestData['id'] = $id;
        $validator = Validator::make($requestData, [
            'id' => 'required|numeric|min:0',
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'sometimes',
            'c_password' => 'sometimes|same:password',
            'role_admin' => 'required|numeric',
            'role_moderator' => 'required|numeric',
            'role_standard' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $data = $request->all();
        $_name = $data['name'];
        $_email = $data['email'];
        $_password = isset($data['password'])?$data['password']:null;
        $_role_admin = $data['role_admin'];
        $_role_moderator = $data['role_moderator'];
        $_role_standard = $data['role_standard'];

        $user=User::find($id);
        $user->name= $_name;
        $user->email = $_email;
        if($_password){
            $user->password = bcrypt($_password);
        }
        $user->role_admin = $_role_admin;
        $user->role_moderator = $_role_moderator;
        $user->role_standard = $_role_standard;
        $user->save();

        return response()->json([
            "status" => "success",
        ],200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $requestData['id'] = $id;

        $validator = Validator::make($requestData, [
            "id" => 'required|numeric|min:0'
        ]);

        if($validator->fails()) {
            return response()->json([
                "status" => "validationFail",
                "validationMessages" => $validator->messages()
            ],400);
        }
        else {
            $user = User::find($id);
            $user->delete();

            return response()->json([
                "status" => "success"
            ],200);
        }
    }


    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        $user = Auth::user();
        return response()->json([
            "status" => "success",
            "user" => $user
        ],200);

    }
}
