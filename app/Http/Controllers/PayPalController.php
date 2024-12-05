<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="PayPal",
 *     description="API for handling PayPal payments"
 * )
 */
class PayPalController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/wallet/paypal",
     *     tags={"PayPal"},
     *     summary="Create a PayPal payment",
     *     description="Initiate a payment request with PayPal and redirect to approval URL.",
     *     operationId="createPayPalPayment",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="amount", type="number", example=10.00, description="Amount to be paid")
     *         )
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirects to PayPal approval page"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid amount"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Payment creation failed"
     *     )
     * )
     */
    public function paypal(Request $request)
    {
        $amount = $request->input('amount');

        if (!$amount || $amount <= 0) {
            return redirect()->back()->with('error', 'Invalid amount.');
        }

        $provider = new \Srmklive\PayPal\Services\PayPal;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $amount,
                    ],
                ],
            ],
            "application_context" => [
                "cancel_url" => route('wallet.paypal.cancel'),
                "return_url" => route('wallet.paypal.success'),
            ],
        ]);

        if (isset($response['links'])) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return redirect($link['href']);
                }
            }
        }

        return redirect()->back()->with('error', 'Something went wrong.');
    }

    /**
     * @OA\Get(
     *     path="/api/wallet/paypal/success",
     *     tags={"PayPal"},
     *     summary="Handle successful PayPal payment",
     *     description="Capture and finalize the payment after PayPal approval.",
     *     operationId="handlePayPalSuccess",
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string"),
     *         description="Token for the PayPal payment"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment completed successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Unable to retrieve payment amount"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Payment failed"
     *     )
     * )
     */
    public function success(Request $request)
    {
        $provider = new \Srmklive\PayPal\Services\PayPal;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $token = $request->query('token');
        $response = $provider->capturePaymentOrder($token);

        if (isset($response['status']) && $response['status'] === 'COMPLETED') {
            if (!empty($response['purchase_units'][0]['payments']['captures'][0]['amount']['value'])) {
                $amount = $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'];

                $user = Auth::user();
                $user->wallet->balance += $amount;
                $user->wallet->save();

                return redirect()->route('dashboard')->with('success', 'Balance added successfully.');
            }

            return redirect()->route('dashboard')->with('error', 'Unable to retrieve payment amount.');
        }

        return redirect()->route('dashboard')->with('error', 'Payment failed.');
    }

    /**
     * @OA\Get(
     *     path="/api/wallet/paypal/cancel",
     *     tags={"PayPal"},
     *     summary="Handle cancelled PayPal payment",
     *     description="Redirects back to the application after the payment was cancelled.",
     *     operationId="handlePayPalCancel",
     *     @OA\Response(
     *         response=200,
     *         description="Payment cancelled"
     *     )
     * )
     */
    public function cancel()
    {
        return redirect()->route('profile')->with('error', 'Payment was cancelled.');
    }
}
