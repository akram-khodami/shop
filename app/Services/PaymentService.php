<?php


namespace App\Services;


use App\Exceptions\ApiException;
use App\Models\Order;
use App\Repositories\CartRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\OrderRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    private $payService;
    private $merchant_id;
    private $cartRepository;
    private $orderRepository;
    private $invoiceRepository;
    private $transactionRepository;

    public function __construct(
        PayServiceInterface $payService,
        CartRepository $cartRepository,
        OrderRepository $orderRepository,
        InvoiceRepository $invoiceRepository,
        TransactionRepository $transactionRepository
    )
    {
        $this->payService = $payService;
        $this->merchant_id = Str::uuid();//???get from confige
        $this->cartRepository = $cartRepository;
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function startPaymentProcess()
    {
        //===get order
        $order = $this->orderRepository->getUserPendingOrder();

        if (!$order) {

            throw new ApiException('You don`t have panding order.', [], 404);

        }

        //===get payWays and show them to user
        //===1-online pay 2-wallet 3-credit card(for organizations and companies and banks) 4-our credit pay or etc


        //===making pay page for online pay and get and show it`s link to user
        $postField = [
            "merchant_id" => $this->merchant_id,
            "amount" => intVal($order->total_price),
            'currency' => 'IRR',//IRT
            "description" => 'pay for test',
            "callback_url" => url('api/verifyPayment?order=' . $order->id),
            "metadata" => [
                "mobile" => strval($order->user->mobile),
                "email" => $order->user->email,
                'order_id' => $order->order_number,
            ]
        ];

        $json_response = $this->payService->sendRequest($postField);

        $result = json_decode($json_response);

        if (!isset($result->data->code)) {

            throw new ApiException($result->errors->message, $result->data, 500);//code???

        }

        if (isset($result->data->code) && $result->data->code != 100) {

            throw new ApiException($result->data->message, $result->data, 402);//code???

        }

        $pay_link = "https://sandbox.zarinpal.com/pg/StartPay/" . $result->data->authority;

        //create invoice
        $invoice_data = [
            'order_id' => $order->id,
            'invoice_number' => Str::random(9),
            'amount' => $order->total_price,
            'status' => 'unpaid',
            'notes' => 'create invoice',
            'due_date' => now(),
        ];

        $invoice = $this->invoiceRepository->firstOrCreate(['order_id' => $order->id, 'status' => 'unpaid'], $invoice_data);

        return [
            'payLink' => $pay_link,
            'invoice' => $invoice,
        ];

    }

    public function verifyPayment(array $data)
    {
        //===get order
        $order = $this->orderRepository->getUserPendingOrder();

        if (!$order) {

            throw new ApiException('You don`t have panding order.', $data, 404);

        }

        $authority = $data['Authority'];
        $status = $data['Status'];
        $order_id = $data['order'] ?? '';

        if ($order->id != $order_id) {

            throw new ApiException('The request for this order is not available', [$order], 403);

        }

        //===باید توسط میدلور انجام بشه.
        if ($status != 'OK') {

            $this->handleFailedPayment($order, $authority, $status);

            throw new ApiException(' تراكنش ناموفق بوده یا توسط خریدار لغو شده است', [], 201);

        }

        $postField = [
            "merchant_id" => $this->merchant_id,
            "amount" => intVal($order->total_price),
            "authority" => $authority,
        ];

        $json_response = $this->payService->verify($postField);

        $result = json_decode($json_response);

        if (!isset($result->data->code)) {

            throw new ApiException(' تراكنش ناموفق بوده یا توسط خریدار لغو شده است', $result, 404);

        }

        $this->handleSuccessfulPayment($order, $authority, $status, $result->data->ref_id);

        return $result;

    }

    public function handleSuccessfulPayment($order, $authority, $status, $ref_id)
    {
        DB::transaction(function () use ($order, $authority, $status, $ref_id) {

            //===create transaction
            $this->transactionRepository->firstOrCreate(['order_id' => $order->id, 'status' => 'completed'],
                [
                    'order_id' => $order->id,
                    'transaction_number' => $ref_id,
                    'amount' => intVal($order->total_price),
                    'payment_method' => 1,//
                    'payment_details' => $authority,//
                    'status' => 'completed',
                ]);

            //===update invoice
            $this->invoiceRepository->updateWhere(['order_id' => $order->id, 'status' => 'unpaid'], ['status' => 'paid', 'notes' => 'done']);

            //===update order
            $order->update(['status' => 'paid', 'paid_at' => now()]);//date&time?

            //===update cart
            $this->cartRepository->updateWhere(['user_id' => auth()->id()], ['is_active' => false]);

        });
    }

    public function handleFailedPayment($order, $authority, $status, $ref_id)
    {
        DB::transaction(function () use ($order, $authority, $status, $ref_id) {

            //===create transaction
            $this->transactionRepository->firstOrCreate(['order_id' => $order->id, 'transaction_number' => $authority, 'status' => 'failed'],
                [
                    'order_id' => $order->id,
                    'transaction_number' => $ref_id,
                    'amount' => intVal($order->total_price),
                    'payment_method' => 1,//
                    'payment_details' => $authority,//
                    'status' => 'failed',
                ]);

            //===update invoice
            $this->invoiceRepository->updateWhere(['order_id' => $order->id, 'status' => 'unpaid'], ['status' => 'unpaid', 'notes' => 'cancel']);

            //update order
            $order->update(['status' => 'failed', 'cancel_reason' => 'cancel_reason']);

            //===update cart
            $this->cartRepository->updateWhere(['user_id' => auth()->id()], ['is_active' => false]);

        });
    }
}
