<?php

namespace Modules\User\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Modules\User\Http\Requests\Api\Auth\UserRegistrationRequest;
use Modules\User\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function register(UserRegistrationRequest $request)
    {
        $userRequest = $request->validated();
        $userRequest['password'] = Hash::make($userRequest['password']);

        $user = User::create($userRequest);
        $user->sendEmailVerificationNotification();

        // event(new Registered($user));

        return response()->json([
            'data' => $user,
            'message' => 'Registration successful,please confirm your email',
        ], Response::HTTP_CREATED);
    }
}
