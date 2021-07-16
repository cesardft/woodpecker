<?php


namespace App\Repositories;


use App\Exceptions\TransactionDeniedException;
use App\Models\User;
use App\Models\Retailer;
use App\Models\Wallet;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\Facades\Auth;
use phpseclib3\Exception\InsufficientSetupException;
use PHPUnit\Framework\InvalidDataProviderException;

class TransactionRepository
{

    public function handle(array $data): array
    {

        if (!$this->guardCanTransfer()){
            throw new TransactionDeniedException('Retailer is not authorized to make transactions', 401);
        }

        $model = $this->getProvider($data['provider']);

        $user = $model->findOrFail($data['payee_id']);

        if (!$this->hasBalance($user->wallet, $data['amount'])){
            throw new InsufficientSetupException('Not enough cash. Stranger.', 422);
        }
        return DB::transcation(function ($user){

        });
        $user->wallet->transaction();
    }

    public function guardCanTransfer(): bool
    {
        if (Auth::guard('users')->check()){
            return true;
        } else if (Auth::guard('retailers')->check()){
            return false;
        } else {
            throw new InvalidDataProviderException('Provider not found.', 422);
        }
    }

    public function getProvider(string $provider): AuthenticatableContract{
        if ($provider == 'users'){
            return new User();
        } else if ($provider == 'retailers'){
            return new Retailer();
        } else {
            throw new InvalidDataProviderException('Provider not found.', 422);
        }
    }

    private function hasBalance(Wallet $wallet, $cash): bool
    {
        return $wallet->amount >= $cash;
    }
}
