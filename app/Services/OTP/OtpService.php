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
            
            if (config('app.env') === 'local') {
                Log::info("OTP Generated for development", [
                    'phone_number' => $phoneNumber,
                    'otp_code' => $otpCode,
                    'purpose' => $purpose
                ]);
                
                if (!config('isend.api_token')) {
                    Log::info("SMS sending skipped in local environment - no ISend API token configured");
                    return true; 
                }
            }
            
            if (config('isend.api_token')) {
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
            } else {
                if (config('app.env') !== 'local') {
                    Log::error("No ISend API token configured in production environment");
                    return false;
                }
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