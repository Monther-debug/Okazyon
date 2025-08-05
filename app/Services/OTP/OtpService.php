<?php
namespace App\Services\OTP;

use App\Models\Otp;
use ISend\SMS\Facades\ISend;
use ISend\SMS\Exceptions\ISendException;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * Generate a new OTP code.
     *
     * @param string $phoneNumber
     * @param string $purpose
     */
    public function generateOtp(string $phoneNumber, string $purpose)
    {
        $otpCode = rand(100000, 999999);

        try {
            Otp::create([
                'phone_number' => $phoneNumber,
                'otp_code' => (string) $otpCode,
                'expires_at' => now()->addMinutes(5),
                'purpose' => $purpose,
            ]);

            $message = "Your OTP code for $purpose is: $otpCode. It is valid for 5 minutes.";
            $isend = ISend::to($phoneNumber)
                ->message($message)
                ->send();
            if (!$isend->getId()) {
                Log::error("Failed to send OTP to {$phoneNumber}", [
                    'purpose' => $purpose,
                    'response' => $isend->getLastResponse()
                ]);
                return false;
            }
        } catch (ISendException $e) {
            Log::error("Exception while sending OTP to {$phoneNumber}", [
                'purpose' => $purpose,
                'exception' => $e->getMessage()
            ]);
            return false;
        }
        return true;
    }

    /**
     * Verify the OTP code.
     *
     * @param string $phoneNumber
     * @param string $otpCode
     */
    public function verifyOtp(string $phoneNumber, string $otpCode)
    {
        $otp = Otp::forPhoneNumber($phoneNumber)
            ->forOtpCode($otpCode)
            ->forUnverified()
            ->first();
        if ($otp && !$otp->isExpired()) {
            $otp->verify();
            return true;
        }
        return false;
    }
}