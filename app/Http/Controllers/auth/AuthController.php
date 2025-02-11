<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\userRequest;
use App\Models\User;
use Validator;
use Illuminate\Http\Request;

class AuthController
{
    use ApiResponse;

    public function register(userRequest $request) {

        $fields=$request->validated();
        $fields['password'] = bcrypt($fields['password']);
        $user = User::create($fields);
        $success['user'] =  $user;

        return $this->sendResponse($success, 'User register successfully.');
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }

        $success = $this->respondWithToken($token);

        return $this->sendResponse($success, 'User login successfully.');
    }

    public function profile()
    {
        $success = auth()->user();

        return $this->sendResponse($success, 'Refresh token return successfully.');
    }

    public function logout()
    {
        auth()->logout();

        return $this->sendResponse([], 'Successfully logged out.');
    }


    public function refresh()
    {
        $success = $this->respondWithToken(auth()->refresh());

        return $this->sendResponse($success, 'Refresh token return successfully.');
    }

    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }
}
