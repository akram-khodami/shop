<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Services\OTPService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Traits\AuthTrait;

class OTPAuthController extends Controller
{
    use AuthTrait;
    protected $OTPService;

    public function __construct(OTPService $OTPService)
    {
        $this->OTPService = $OTPService;
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate(
            [
                'mobile' => 'required|string|regex:/^09[0-9]{9}$/',
            ]);

        $mobile = $request->input('mobile');

        try {

            $this->OTPService->sendSms($mobile, false);

        } catch (ApiException $e) {

            throw $e; // Re-throw API exceptions to be handled by Laravel//json

        }

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

}
