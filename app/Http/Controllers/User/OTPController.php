<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Otp\SendOtpRequest;
use App\Http\Requests\User\Otp\VerifyOtpRequest;
use App\Services\OTP\OtpService;
use Illuminate\Http\Request;

class OTPController extends Controller
{
    protected $otpService;

    public function __construct()
    {
        $this->otpService = new OtpService();
    }
    public function generateOTP(SendOtpRequest $request)
    {
        $data = $request->validated();

        $phoneNumber = $data['phone_number'];
        $purpose = $data['purpose'];

        $otp = $this->otpService->generateOtp($phoneNumber, $purpose);

        return response()->json(['message' => __('otp.otp_sent_successfully')], 200);
    }

    public function verifyOTP(VerifyOtpRequest $request)
    {
        $data = $request->validated();

        $phoneNumber = $data['phone_number'];
        $otpCode = $data['otp'];

        $isVerified = $this->otpService->verifyOtp($phoneNumber, $otpCode);

        if ($isVerified) {
            return response()->json(['message' => __('otp.otp_verified_successfully')], 200);
        } else {
            return response()->json(['message' => __('otp.invalid_or_expired_otp')], 400);
        }
    }
}
