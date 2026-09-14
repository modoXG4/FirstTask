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

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function register(StoreUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($request->expectsJson()) {
            return $this->success(new UserResource($user), 'User registered successfully', 201);
        }

        return redirect()->route('login')->with('success', 'تم التسجيل بنجاح، سجّل دخولك.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            if ($request->expectsJson()) {
                return $this->error('Invalid credentials', 401);
            }
            return back()->withErrors(['email' => 'البيانات غير صحيحة'])->onlyInput('email');
        }

        if ($request->expectsJson()) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return $this->success([
                'user'  => new UserResource($user),
                'token' => $token,
            ], 'User logged in successfully');
        }
      
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        return redirect()->intended(route('projects.index'));
    }

    public function logout(Request $request)
    {
        if ($request->expectsJson()) {
            $request->user()->currentAccessToken()->delete();
            return $this->success(null, 'User logged out successfully');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
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
