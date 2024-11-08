<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'=> ['required', 'email'],
            'password' => 'required',
            'remember' => 'boolean'
        ]);
        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);
        if (!Auth::attempt($credentials, $remember)) {
            return response([
                'message' => 'Email or password is incorrect'
            ], 422);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->is_admin && !$user->is_seller) {
            Auth::logout();
            return response([
                'message' => 'You don\'t have permission to authenticate as customer'
            ], 403);
        }
        
        $token = $user->createToken('main')->plainTextToken;
        return response([
            'user' => new UserResource($user),
            'token' => $token
        ]);

    }

    public function logout()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->currentAccessToken()->delete();

        return response('', 204);
    }

    public function getUser(Request $request)
    {
        return new UserResource($request->user());
    }

    public function registerSeller(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email'=> ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::beginTransaction();
        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email'=> $request->email,
                'password' => Hash::make($request->password),
                'is_seller' => 1
            ]);

            event(new Registered($user));

            Auth::login($user);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());

            return response([
                'message' => 'Unable to register right now.'
            ], 500);
        }

        DB::commit();

        $token = $user->createToken('main')->plainTextToken;

        return response([
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }
}
