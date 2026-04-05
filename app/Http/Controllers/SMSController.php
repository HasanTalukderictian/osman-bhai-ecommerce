<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SMSController extends Controller
{
    // ✅ SEND OTP
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:11'
        ]);

        $otp = rand(1000, 9999);

        DB::table('otps')->updateOrInsert(
            ['phone' => $request->phone],
            [
                'otp' => $otp,
                'is_verified' => 0,
                'expires_at' => now()->addMinutes(5),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        // Send SMS
        $this->sendSms($request->phone, "Your OTP is: $otp");

        return response()->json([
            'status' => true,
            'message' => 'OTP sent successfully'
        ]);
    }

    // ✅ VERIFY OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required'
        ]);

        $otpData = DB::table('otps')
            ->where('phone', $request->phone)
            ->where('otp', $request->otp)
            ->first();

        if (!$otpData) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP'
            ]);
        }

        if (now()->gt($otpData->expires_at)) {
            return response()->json([
                'status' => false,
                'message' => 'OTP expired'
            ]);
        }

        // ✅ mark as verified
        DB::table('otps')
            ->where('phone', $request->phone)
            ->update(['is_verified' => 1]);

        return response()->json([
            'status' => true,
            'message' => 'OTP verified'
        ]);
    }

    // ✅ SMS FUNCTION
    public function sendSms($phone, $message)
    {
        $response = Http::post('https://smsplus.sslwireless.com/api/v3/send-sms', [
            "api_token" => "xhw7rusz-3yo58wqh-0dfjsxwu-bpitrk9x-dxmsyxvh",
            "sid"       => "AKASHBARIHDMASKING",
            "msisdn"    => $phone,
            "sms"       => $message,
            "csms_id"   => uniqid()
        ]);

        return $response->json();
    }
}
