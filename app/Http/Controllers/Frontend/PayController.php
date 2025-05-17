<?php


namespace App\Http\Controllers\Frontend;


use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayController
{
    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function choosePayWay(): JsonResponse
    {
        $result = $this->paymentService->startPaymentProcess();

        return response()->json(
            [
                'success' => true,
                'message' => 'لینک صفحه پرداخت',
                'data' => $result,
            ]
        );
    }

    public function verifyPayment(Request $request): JsonResponse
    {
        $result = $this->paymentService->verifyPayment($request->only(['Authority', 'Status','order']));

        if ($result->data->code == 100) {

            $message = $result->data->message;

        } elseif ($result->data->code == 101) {

            $message = $result->data->message . ' ' . 'تراکنش قبلا با موفقیت انجام شده بوده';

        }

        return response()->json(
            [
                'success' => true,
                'message' => $message,
                'data' => [
                    'ref_id' => $result->data->ref_id,//save???
                    'transaction_code' => '',
                ],

            ]);

    }

}
