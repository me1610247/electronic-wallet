<?php
namespace App\Observers;

use App\Models\User;
use App\Models\Wallet;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user)
    {
        // Create a wallet when a new user is created
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0, // Initial balance set to 0
        ]);
    }
}
