<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\SSLCommerz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SSLCommerzController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     */
    public function index(Request $request)
    {
        $postData = [];
        $postData['total_amount'] = $request->input('payable');
        $postData['currency'] = $request->input('currency');
        $postData['tran_id'] = $request->input('transaction_id');
        $postData['success_url'] = route('payment.success');
        $postData['fail_url'] = route('payment.failed');
        $postData['cancel_url'] = route('payment.cancelled');

        # CUSTOMER INFORMATION
        $postData['cus_name'] = $request->input('name');
        $postData['cus_email'] = $request->input('email');
        $postData['cus_add1'] = '';
        $postData['cus_add2'] = "";
        $postData['cus_city'] = "";
        $postData['cus_state'] = "";
        $postData['cus_postcode'] = "";
        $postData['cus_country'] = "Bangladesh";
        $postData['cus_phone'] = "";
        $postData['cus_fax'] = "";

        # SHIPMENT INFORMATION
        $postData['ship_name'] = '';
        $postData['ship_add1 '] = '';
        $postData['ship_add2'] = "";
        $postData['ship_city'] = "";
        $postData['ship_state'] = "";
        $postData['ship_postcode'] = "";
        $postData['ship_country'] = "Bangladesh";

        # OPTIONAL PARAMETERS
//        $post_data['value_a'] = "ref001";
//        $post_data['value_b'] = "ref002";
//        $post_data['value_c'] = "ref003";
//        $post_data['value_d'] = "ref004";

        #Before  going to initiate the payment order status need to update as Pending.
        $sslc = new SSLCommerz();
        # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payment gateway here )
        $paymentOptions = $sslc->initiate($postData, false);

        if (!is_array($paymentOptions)) {
            print_r($paymentOptions);
            $paymentOptions = [];
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function paymentSuccess(Request $request)
    {
        Log::info('success url hook received');
        // get transaction id from session and
        $transactionId = $request->input('tran_id');
        $order = Order::where('transaction_id', $transactionId)->firstOrFail();

        if ($order->status == Order::STATUS_PENDING &&
            !in_array(strtolower($request->input('status')), [Order::STATUS_CANCELLED, Order::STATUS_FAILED])
        ) {
            $sslc = new SSLCommerz();
            $validation = $sslc->orderValidate($transactionId, $order->paid_amount, $order->currency, $request->all());
            if ($validation == true) {
                /*
                That means IPN did not work or IPN URL was not set in your merchant panel.
                Here you need to update order status in order table as Processing or Complete.
                Here you can also sent sms or email for successful transaction to customer
                */
                $order->update(['status' => Order::STATUS_COMPLETE]);

                Log::info(json_encode([
                    'ip' => $request->ip(),
                    'request' => $request->all(),
                    'message' => 'in success url, payment successful',
                    'order' => $order
                ]));
                return redirect(route('payment.response.success', ['order' => $order]));
            } else {
                /*
                That means IPN did not work or IPN URL was not set in your merchant panel and
                Transaction validation failed. Here you need to update order status as Failed in order table.
                */
                $order->update(['status' => Order::STATUS_FAILED]);
                Log::error(json_encode([
                    'ip' => $request->ip(),
                    'request' => $request->all(),
                    'message' => 'in success url, payment failed',
                    'order' => $order
                ]));

                return redirect(route('payment.response.failed', ['order' => $order]));
            }
        } elseif ($order->status == Order::STATUS_COMPLETE) {
            /*
             That means through IPN Order status already updated. Now you can just show the customer that
             transaction is completed. No need to update database.
             */
            Log::info(json_encode([
                'ip' => $request->ip(),
                'request' => $request->all(),
                'message' => 'in success url, order status is already updated and payment successful',
                'order' => $order
            ]));
            return redirect(route('payment.response.success', ['order' => $order]));
        } else {
            Log::error(json_encode([
                'ip' => $request->ip(),
                'request' => $request->all(),
                'message' => 'in success url, unrecognized status',
                'order' => $order
            ]));
            #That means something wrong happened. You can redirect customer to your product page.
            echo "Invalid Transaction";
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function paymentFailed(Request $request)
    {
        Log::info('payment failed hook received');
        $transactionId = $request->input('tran_id');
        $order = Order::where('transaction_id', $transactionId)->firstOrFail();

        if ($order->status == Order::STATUS_PENDING) {
            $order->update(['status' => Order::STATUS_FAILED]);
            Log::error(json_encode([
                'ip' => $request->ip(),
                'request' => $request->all(),
                'message' => 'in payment failed url, payment failed',
                'order' => $order
            ]));

            return redirect(route('payment.response.failed', ['order' => $order]));
        } elseif ($order->status == Order::STATUS_COMPLETE) {
            /*
             That means through IPN Order status already updated. Now you can just show the customer that
             transaction is completed. No need to update database.
             */
            Log::info(json_encode([
                'ip' => $request->ip(),
                'request' => $request->all(),
                'message' => 'in payment failed url, order status is already updated and payment successful',
                'order' => $order
            ]));
            return redirect(route('payment.response.success', ['order' => $order]));
        } else {
            Log::error(json_encode([
                'ip' => $request->ip(),
                'request' => $request->all(),
                'message' => 'in payment failed url, unrecognized status',
                'order' => $order
            ]));
            echo "Transaction is Invalid";
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function paymentCancelled(Request $request)
    {
        Log::info('payment cancelled hook received');
        $transactionId = $request->input('tran_id');
        $order = Order::where('transaction_id', $transactionId)->firstOrFail();

        if ($order->status == Order::STATUS_PENDING) {
            $order->update(['status' => Order::STATUS_CANCELLED]);
            Log::error(json_encode([
                'ip' => $request->ip(),
                'request' => $request->all(),
                'message' => 'in payment cancelled url, payment cancelled',
                'order' => $order
            ]));

            return redirect(route('payment.response.cancelled', ['order' => $order]));
        } elseif ($order->status == Order::STATUS_COMPLETE) {
            /*
             That means through IPN Order status already updated. Now you can just show the customer that
             transaction is completed. No need to update database.
             */
            Log::info(json_encode([
                'ip' => $request->ip(),
                'request' => $request->all(),
                'message' => 'in payment cancelled url, order status is already updated and payment successful',
                'order' => $order
            ]));
            return redirect(route('payment.response.success', ['order' => $order]));
        } else {
            Log::error(json_encode([
                'ip' => $request->ip(),
                'request' => $request->all(),
                'message' => 'in payment cancelled url, unrecognized status',
                'order' => $order
            ]));
            echo "Transaction is Invalid";
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function paymentIpn(Request $request)
    {
        Log::info('IPN validation hook received');
        $transactionId = $request->input('tran_id');

        # Check transaction id is posted or not.
        if ($transactionId) {
            # Check order status in order tabel against the transaction id or order id.
            $order = Order::where('transaction_id', $transactionId)->firstOrFail();
            if ($order->status == Order::STATUS_PENDING &&
                !in_array(strtolower($request->input('status')), [Order::STATUS_CANCELLED, Order::STATUS_FAILED])
            ) {
                $sslc = new SSLCommerz();
                $validation = $sslc->orderValidate($transactionId, $order->paid_amount, $order->currency, $request->all());

                if ($validation == true) {
                    /*
                    That means IPN worked. Here you need to update order status
                    in order table as Processing or Complete.
                    Here you can also sent sms or email for successful transaction to customer
                    */
                    $order->update(['status' => Order::STATUS_COMPLETE]);

                    // enroll courses according to payment
                    $order->packages()->each(function ($item) use ($order) {
                        // do not enroll already enrolled courses
                        if (is_null($item->course->students->find($order->user_id))) {
                            $item->course->students()->attach($order->user_id);
                        }
                    });

                    if ($order->voucher_id != 0) {
                        $userVoucherExist = UserVoucher::find($order->voucher_id);

                        $userVoucherExist->remaining = 0;
                        $userVoucherExist->save();

                        $voucherExist = Voucher::find($userVoucherExist->voucher_id);
                        $voucherExist->used = $voucherExist->used + 1;
                        $voucherExist->save();
                    }

                    // Send SMS

                    Log::info(json_encode([
                        'ip' => $request->ip(),
                        'request' => $request->all(),
                        'message' => 'ipn validation and payment successful',
                        'order' => $order
                    ]));
                    return redirect(route('payment.response.success', ['order' => $order]));
                } else {
                    /*
                    That means IPN worked, but Transaction validation failed.
                    Here you need to update order status as Failed in order table.
                    */
                    $order->update(['status' => Order::STATUS_FAILED]);

                    Log::error(json_encode([
                        'ip' => $request->ip(),
                        'request' => $request->all(),
                        'message' => 'ipn validation is not successful, payment failed',
                        'order' => $order
                    ]));

                    return redirect(route('payment.response.failed', ['order' => $order]));
                }
            } elseif ($order->status == Order::STATUS_COMPLETE) {
                /*
                 That means through IPN Order status already updated. Now you can just show the customer that
                 transaction is completed. No need to update database.
                 */
                Log::info(json_encode([
                    'ip' => $request->ip(),
                    'request' => $request->all(),
                    'message' => 'order status is already updated and payment successful',
                    'order' => $order
                ]));

                return redirect(route('payment.response.success', ['order' => $order]));
            } else {
                Log::error(json_encode([
                    'ip' => $request->ip(),
                    'request' => $request->all(),
                    'message' => 'unrecognized status',
                    'order' => $order
                ]));
                # That means something wrong happened. You can redirect customer to your product page.
                echo "Invalid Transaction";
            }
        } else {
            Log::error(json_encode([
                'ip' => $request->ip(),
                'request' => $request->all(),
                'message' => 'invalid payload sent',
            ]));
            echo "Invalid Data";
        }
    }

    /**
     * @param \App\Models\Order $order
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function responseSuccess(Order $order)
    {
        if(session()->forget('cart')){
            session()->forget('cart');
        }

        return view('success', compact('order'));
    }

    /**
     * @param \App\Models\Order $order
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function responseFailed(Order $order)
    {
        if(session()->forget('cart')){
            session()->forget('cart');
        }

        return view('failed', compact('order'));
    }

    /**
     * @param \App\Models\Order $order
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function responseCancelled(Order $order)
    {
        if(session()->forget('cart')){
            session()->forget('cart');
        }

        return view('cancelled', compact('order'));
    }
}
