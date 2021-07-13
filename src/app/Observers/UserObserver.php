<?php


namespace App\Observers;


use App\Models\User;

class UserObserver
{
    public function created(User $user){
        $user->wallet()->create([
            'id' => random_int(1, 9999),
            'amount' => 0]);
    }
}
