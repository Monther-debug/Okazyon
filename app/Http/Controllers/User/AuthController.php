<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\LogInRequest;
use App\Http\Requests\User\Auth\RegisterAnAccountRequest;
use App\Http\Requests\User\Auth\ReSetPasswordRequest;
use Illuminate\Http\Request;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterAnAccountRequest $request)
    {
        $data = $request->validated();

        $validOtp = Otp::forPhoneNumber($data['phone_number'])
            ->forPurpose('register')
            ->forOtpCode($data['otp'])
            ->firstOrFail()
            ->isVerifiedUnderIntime();
        if (!$validOtp) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        } else {
            unset($data['otp']);
            $data['password'] = bcrypt($data['password']);
            User::create($data);
        }
        return response()->json(['message' => 'User registered successfully.'], 201);
    }

    public function login(LogInRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'Login successful.',
            'token' => $token,
            'user' => $user,
        ], 200);

    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json(['message' => 'Logout successful.'], 200);
    }

    public function reSetPassword(ReSetPasswordRequest $request)
    {
        $data = $request->validated();


        $validOtp = Otp::forPhoneNumber($data['phone_number'])
            ->forPurpose('reset_password')
            ->forOtpCode($data['otp'])
            ->firstOrFail()
            ->isVerifiedUnderIntime();

        if (!$validOtp) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }

        $user = User::where('phone_number', $data['phone_number'])->firstOrFail();

        $user->password = bcrypt($data['password']);
        $user->save();

        return response()->json(['message' => 'Password reset successfully.'], 200);
    }

}
