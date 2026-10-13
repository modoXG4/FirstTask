<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\UserResource;
use App\Http\Requests\loginRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Traits\ExpectsJsonTrait;

class AuthController extends Controller
{
    use ApiResponseTrait, ExpectsJsonTrait;

    public function register(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->respondBasedOnRequest($request, [
            'user'  => new UserResource($user),
            'token' => $token,
        ], 'projects.index', __('auth.created'));
    }

    public function login(loginRequest $request)
    {
        $credentials = $request->validated();
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Auth::attempt($credentials)) {
            return $this->respondBasedOnRequest($request, null, 'login', __('auth.invalid_credentials')); 
        }

       return $this->respondBasedOnRequest($request, [
            'user'  => new UserResource($user),
            'token' => $user->createToken('auth_token')->plainTextToken,
        ], 'projects.index', __('auth.logged_in'));
    }

    public function logout(Request $request)
    {
        if($request->expectsJson()){
            $request->user()->currentAccessToken()->delete();
        }
        else {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return $this->respondBasedOnRequest($request, null, 'login', __('auth.logged_out'));
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('projects.index');
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('projects.index');
        }
        return view('auth.register');
    }
}
