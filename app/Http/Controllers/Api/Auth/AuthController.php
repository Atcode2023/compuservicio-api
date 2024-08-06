<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required',
            'email'      => 'required|email',
            'password'   => 'required',
            'c_password' => 'required|same:password',
            'ci'         => 'required|numeric',
            'role_id'    => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Check if user already exists
        if (User::where('email', $request->email)->exists()) {
            return $this->sendError('User already exists.', ['error' => 'User with this email already exists.']);
        }

        $input             = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user              = User::create($input);
        // dd('ok');
        $success['token']  = $user->createToken('MyApp')->plainTextToken;
        $success['name']   = $user->name;

        return $this->sendResponse($success, 'User register successfully.');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            if ($user->status == 0)
                return $this->sendError('Inactive.', ['error' => 'Usuario Inactivo']);
            $success['token'] = $user->createToken($user->email)->plainTextToken;
            $success['user']  = $user;

            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorized.', ['error' => 'Oops,los datos no son correcto']);
        }
    }

    public function me(Request $request)
    {
        return $this->sendResponse(['user' => Auth::user()], 'User login successfully.');
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();

        return $this->sendResponse(['success' => true], 'User login successfully.');
    }
}
