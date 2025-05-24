<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OTPAuthController extends Controller
{
    private $OTPService;

    public function __construct(OTPService $OTPService)
    {
        $this->OTPService = $OTPService;
    }

    public function login(Request $request)
    {
        $request->validate(
            [
                'mobile' => 'required|string|regex:/^09[0-9]{9}$/',
            ]);

        $mobile = $request->input('mobile');

        $response = $this->OTPService->sendSms($mobile);

        return response()->json(
            [
                'success' => true,
                'message' => 'SMS sent successfully.',
                'data' => $mobile,
            ]);

    }

    public function checkCode(Request $request)
    {
        $request->validate(
            [
                'mobile' => 'required|string|regex:/^09[0-9]{9}$/',
                'code' => 'required|numeric',
            ]);

        $data = $request->only(['mobile', 'code']);

        $user = $this->OTPService->checkCode($data);

        $tokenResult = $this->generateToken($user);

        return response()->json(
            [
                'message' => 'Logged in successfully.',
                'access_token' => $tokenResult->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $tokenResult->accessToken->expires_at,
            ], 200);

    }

    protected function generateToken(User $user)
    {
        $tokenResult = $user->createToken('API Token', ['*'], now()->addDays(1)); // Expires in 1 day

        return $tokenResult;

    }
}
