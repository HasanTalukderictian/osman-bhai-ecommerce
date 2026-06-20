<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SMSController extends Controller
{
    // ✅ SEND OTP
    public function sendOtp(Request $request)
    {
        try {
            Log::info('Send OTP request received:', ['phone' => $request->phone]);

            $request->validate([
                'phone' => 'required|digits:11'
            ]);

            $otp = rand(1000, 9999);
            Log::info('Generated OTP:', ['otp' => $otp, 'phone' => $request->phone]);

            // Check if phone exists
            $existing = DB::table('otps')->where('phone', $request->phone)->first();

            if ($existing) {
                // Update existing
                DB::table('otps')
                    ->where('phone', $request->phone)
                    ->update([
                        'otp' => (string)$otp,
                        'is_verified' => 0,
                        'expires_at' => now()->addMinutes(5),
                        'updated_at' => now(),
                    ]);
            } else {
                // Insert new
                DB::table('otps')->insert([
                    'phone' => $request->phone,
                    'otp' => (string)$otp,
                    'is_verified' => 0,
                    'expires_at' => now()->addMinutes(5),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Send SMS (uncomment in production)
            // $this->sendSms($request->phone, "Your OTP is: $otp");

            return response()->json([
                'status' => true,
                'message' => 'OTP sent successfully',
                'otp' => (string)$otp,
                'phone' => $request->phone
            ]);

        } catch (\Exception $e) {
            Log::error('Send OTP error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ VERIFY OTP - FIXED
    public function verifyOtp(Request $request)
    {
        try {
            Log::info('Verify OTP request received:', [
                'phone' => $request->phone,
                'otp' => $request->otp
            ]);

            $request->validate([
                'phone' => 'required|digits:11',
                'otp' => 'required|digits:4'
            ]);

            // Get the latest OTP for this phone (not verified)
            $otpData = DB::table('otps')
                ->where('phone', $request->phone)
                ->where('is_verified', 0)
                ->orderBy('created_at', 'desc')
                ->first();

            Log::info('OTP found in database:', [
                'found' => $otpData ? 'Yes' : 'No',
                'db_otp' => $otpData ? $otpData->otp : 'None',
                'received_otp' => $request->otp
            ]);

            if (!$otpData) {
                // Check if there's any OTP at all for this phone
                $anyOtp = DB::table('otps')
                    ->where('phone', $request->phone)
                    ->first();

                if ($anyOtp) {
                    Log::info('Found OTP but it might be verified:', [
                        'is_verified' => $anyOtp->is_verified,
                        'otp' => $anyOtp->otp
                    ]);

                    if ($anyOtp->is_verified) {
                        return response()->json([
                            'status' => false,
                            'message' => 'OTP already verified. Please request a new one.'
                        ], 400);
                    }
                }

                return response()->json([
                    'status' => false,
                    'message' => 'No valid OTP found. Please request a new OTP.'
                ], 400);
            }

            // Check if OTP matches (convert both to string for comparison)
            if ((string)$otpData->otp !== (string)$request->otp) {
                Log::warning('OTP mismatch:', [
                    'received' => $request->otp,
                    'expected' => $otpData->otp
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP. Please check your code.',
                    'debug' => [
                        'received' => $request->otp,
                        'expected' => $otpData->otp
                    ]
                ], 400);
            }

            // Check if expired
            if (now()->gt($otpData->expires_at)) {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP has expired. Please request a new one.'
                ], 400);
            }

            // Mark as verified
            DB::table('otps')
                ->where('phone', $request->phone)
                ->where('otp', $request->otp)
                ->update(['is_verified' => 1]);

            Log::info('OTP verified successfully:', ['phone' => $request->phone]);

            return response()->json([
                'status' => true,
                'message' => 'OTP verified successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Verify OTP error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Verification failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ RESEND OTP
    public function resendOtp(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|digits:11'
            ]);

            $otp = rand(1000, 9999);

            DB::table('otps')
                ->where('phone', $request->phone)
                ->update([
                    'otp' => (string)$otp,
                    'is_verified' => 0,
                    'expires_at' => now()->addMinutes(5),
                    'updated_at' => now(),
                ]);

            Log::info('OTP resent:', ['phone' => $request->phone, 'otp' => $otp]);

            return response()->json([
                'status' => true,
                'message' => 'OTP resent successfully',
                'otp' => (string)$otp
            ]);

        } catch (\Exception $e) {
            Log::error('Resend OTP error:', ['message' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to resend OTP'
            ], 500);
        }
    }

    // ✅ SMS FUNCTION
    public function sendSms($phone, $message)
    {
        try {
            $response = Http::post('https://smsplus.sslwireless.com/api/v3/send-sms', [
                "api_token" => env('SMS_API_TOKEN', 'xhw7rusz-3yo58wqh-0dfjsxwu-bpitrk9x-dxmsyxvh'),
                "sid"       => env('SMS_SID', 'AKASHBARIHDMASKING'),
                "msisdn"    => $phone,
                "sms"       => $message,
                "csms_id"   => uniqid()
            ]);

            Log::info('SMS response:', ['response' => $response->json()]);
            return $response->json();

        } catch (\Exception $e) {
            Log::error('SMS sending failed:', ['message' => $e->getMessage()]);
            return ['status' => false, 'message' => 'SMS failed'];
        }
    }
}
