<?php

namespace App\Broadcasting;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user, $userId): bool
    {
        // O usuário só pode acessar seu próprio canal
        return Auth::check() && Auth::id() == $userId;
    }
}
