<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Wallet",
 *     description="API for wallet operations including balance inquiry and deduction"
 * )
 */
class WalletController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/wallet/balance",
     *     tags={"Wallet"},
     *     summary="Get wallet balance",
     *     description="Retrieve the current balance of the authenticated user's wallet.",
     *     operationId="getWalletBalance",
     *     @OA\Response(
     *         response=200,
     *         description="Balance retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="balance", type="number", example=100.50)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function showBalance()
    {
        $user = Auth::user();
        $balance = $user->wallet->balance;

        return response()->json(['balance' => $balance], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/wallet/deduct",
     *     tags={"Wallet"},
     *     summary="Deduct wallet balance",
     *     description="Deduct a specified amount from the authenticated user's wallet.",
     *     operationId="deductWalletBalance",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="amount", type="number", example=50.00, description="Amount to be deducted"),
     *             @OA\Property(property="description", type="string", example="Purchase of a service", description="Reason for deduction")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Payment successful.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Insufficient wallet balance",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Insufficient wallet balance.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred during the payment process",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="An error occurred during the payment process.")
     *         )
     *     )
     * )
     */
    public function deduct(Request $request)
    {
        $user = Auth::user();
        $amount = $request->input('amount');
        $description = $request->input('description', 'General Deduction');

        DB::beginTransaction();

        try {
            if ($user->wallet->balance < $amount) {
                return response()->json(['error' => 'Insufficient wallet balance.'], 400);
            }

            $user->wallet->balance -= $amount;
            $user->wallet->save();

            if (method_exists($user, 'walletTransactions')) {
                $user->walletTransactions()->create([
                    'amount' => $amount,
                    'type' => 'debit',
                    'description' => $description,
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Payment successful.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred during the payment process.'], 500);
        }
    }
}
