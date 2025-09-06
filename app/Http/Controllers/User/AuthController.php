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
use App\Services\OTP\OtpService;

class AuthController extends Controller
{
    public function register(RegisterAnAccountRequest $request)
    {
        $data = $request->validated();

        $data['password'] = bcrypt($data['password']);
        
        // Add default values for required fields that we removed from registration
        $data['last_name'] = $data['last_name'] ?? '';
        $data['gender'] = $data['gender'] ?? 'other';
        
        $user = User::create($data);
        
        $otpService = new OtpService();
        $otpService->generateOtp($user->phone_number, 'account_verification');
        
        return response()->json(['message' => __('auth.user_registered_successfully_otp_sent')], 201);
    }

    public function login(LogInRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => __('auth.invalid_credentials')], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => __('auth.login_successful'),
            'token' => $token,
            'user' => $user,
        ], 200);

    }

    public function logout(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json(['message' => __('auth.logout_successful')], 200);
    }

    public function reSetPassword(ReSetPasswordRequest $request)
    {
        $data = $request->validated();

        $otpRecord = Otp::forPhoneNumber($data['phone_number'])
            ->forPurpose('reset_password')
            ->forOtpCode($data['otp'])
            ->forUnverified()  // Only get unverified OTPs
            ->first();
            
        if (!$otpRecord) {
            return response()->json(['message' => __('auth.invalid_or_expired_otp')], 400);
        }
        
        // Check if OTP is expired
        if ($otpRecord->isExpired()) {
            return response()->json(['message' => __('auth.invalid_or_expired_otp')], 400);
        }
        
        // Verify the OTP (mark it as used)
        $otpRecord->verify();

        $user = User::where('phone_number', $data['phone_number'])->firstOrFail();

        $user->password = bcrypt($data['password']);
        $user->save();

        return response()->json(['message' => __('auth.password_reset_successfully')], 200);
    }

}
