<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AccountKitController extends Controller
{
    public function smsVerify(Request $request, AuthService $authService)
    {
        \Log::info("in sms verify");
        $phone = $authService->getPhoneNumberFromAccountKit($request->input('code'));

        \Log::info($phone);

        if (is_null($phone)) {
            return response()->json([
                'status' => false,
                'phone' => '',
                'message' => 'The phone number could not be verified.',
            ]);
        }

        return response()->json([
            'status' => true,
            'phone' => $phone,
            'message' => 'The phone number verification is successful.',
        ]);
    }

}
