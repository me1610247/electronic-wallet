<?php

namespace App\Services;

use App\Models\User;

class WalletService
{
    public static function deduct(User $user, $amount, $description = null)
    {
        // Check if the wallet has enough balance
        if ($user->wallet->balance < $amount) {
            return false; // Insufficient funds
        }

        // Deduct the amount
        $user->wallet->balance -= $amount;
        $user->wallet->save();

        // Optionally record the transaction (if you have a transactions table)
        if (method_exists($user, 'walletTransactions')) {
            $user->walletTransactions()->create([
                'amount' => $amount,
                'type' => 'debit',
                'description' => $description,
            ]);
        }

        return true;
    }
}
