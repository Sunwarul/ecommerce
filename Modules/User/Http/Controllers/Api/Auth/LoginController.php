<?php

namespace Modules\User\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Modules\User\Http\Requests\Api\Auth\UserLoginRequest;
use Modules\User\Http\Response\ApiResponse;
use Modules\User\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(UserLoginRequest $request)
    {
        $credentials = $request->validated();
        $user = User::where('email', $credentials['email'])->first();
        $isVerified = $user->hasVerifiedEmail();
        if ($isVerified && Auth::attempt($credentials)) {
            $token = auth()->user()->createToken('login-token')->plainTextToken;

            return response()->json([
                'data' => auth()->user(),
                'token' => $token,
            ]);
        }

        if (! $isVerified) {
            return ApiResponse::error(null, 'Your account is not verfied yet. Please verify in order to login.');
        }

        return ApiResponse::error(null, 'Login failed, please try again with correct credentials.');
    }
}
