<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\AppBaseController;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Lang;
use Response;
/**
 * Class UserController.
 */
class UserApiController extends AppBaseController
{
    /**
     * Update the specified User in storage.
     * PUT/PATCH /users/{id}.
     *
     * @param UpdateUserAPIRequest|Request $request
     * @return Response
     * @internal param int $id
     */

    

    public function update(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return $this->sendError('Please login !', 401);
        }
        $inputs = $request->all();
        // dd($user->id);
        $rules = [];
        $rules['email'] = 'required|string|email|max:255|unique:users,email,' . $user->id;
        $rules['name'] = 'required' . $user->id;
        if (isset($inputs['password']) && $inputs['password'] != '') {
            $rules['password'] = 'required|string|min:6';
        }
        // validate all inputs
        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->all(), 409);
        }
        $inputs['password'] = bcrypt($inputs['password']);
        $user->update($inputs);
        return $this->sendResponse($user->toArray(), 'User updated successfully');
    }
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->sendError('User not found or credentials not matched', 401);
        }
        $user = auth()->user();
        if (!$user || $user->email != $request->get('email')) {
            return $this->sendError('User not matched !', 401);
        }
        return $this->sendResponse(compact('token', 'user'));
    }
    // handle  user register
    public function register(Request $request)
    {
        $inputs = $request->all();
        // dd($user->id);
        $rules = [];
        $rules['email'] = 'required|string|email|max:255|unique:users,email';
        //$rules['phone'] = 'required|unique:users,phone';
        if (isset($inputs['password']) && $inputs['password'] != '') {
            $rules['password'] = 'required|string|min:6';
        }
        // validate all inputs
        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->all(), 409);
        }
        $inputs['password'] = bcrypt($inputs['password']);
        $user = User::create($inputs);
        return $this->sendResponse(compact( 'user'));
    }
    /*
        get user information base on his token
     */
    public function my()
    {
        $user = JWTAuth::parseToken()->toUser();
        return $this->sendResponse(compact('user'));
    }
    public function Ratescreate(Request $request){
        $rateable = User::find($request->input('id'));
        $user = auth()->user();
        if (!$user) {
            return $this->sendError('Please login !', 401);
        }
        $rate = new Rate();
        $rate->value = $request->input('value');
        $rate->user_id = $user->id;
        $rate->rateable_id = $rateable->id;
        $rate->save();
        return $this->sendResponse($rate->toArray(), 'Rate created successfully');
    }
    public function Ratesupdate(Request $request){
        $rate = Rate::find($request->input('id'));
        $user = auth()->user();
        if (!$user) {
            return $this->sendError('Please login !', 401);
        }
        $inputs = $request->all();
        $rules = [];
        $rules['value'] = 'required|numeric'. $user->id;
        $rules['user_id'] = 'required|numeric' . $user->id;
        $rules['rateable_id'] = 'required|numeric' . $user->id;
        // validate all inputs
        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->all(), 409);
        }
        $rate->update($inputs);
        return $this->sendResponse($rate->toArray(), 'Rate updated successfully');
    }
}