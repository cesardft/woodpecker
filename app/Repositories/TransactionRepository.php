<?php


namespace App\Repositories;


use App\Exceptions\InsufficientAmountException;
use App\Exceptions\TransactionDeniedException;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Retailer;
use App\Models\Wallet;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\InvalidDataProviderException;

class TransactionRepository
{

    public function handle(array $data): array
    {

        if (!$this->guardCanTransfer()){
            throw new TransactionDeniedException('Retailer is not authorized to make transactions', 401);
        }

        $this->checkIfUserProviderExists($data);

        $myWallet = Auth::guard($data['provider'])->user()->wallet;

        if (!$this->hasBalance($myWallet, $data['amount'])){
            throw new InsufficientAmountException('Not enough cash. Stranger.', 422);
        }

        if(!$this->checkIfUserProviderExists($data)){
            throw new InvalidDataProviderException('teste');
        }

        return $this->makeTransaction($data);
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

    private function makeTransaction(array $data)
    {
        $payload = [
            'id' => random_int(0, 9999),
            'payer_wallet_id' => Auth::guard($data['provider'])->user()->wallet->id,
            'payee_wallet_id' => Auth::guard($data['provider'])->user()->wallet->id,
            'amount' => $data['amount']
        ];

        return DB::transaction(function () use ($payload) {
            $transaction = Transaction::create($payload);

            $transaction->walletPayer->withdraw($payload['amount']);
            $transaction->walletPayee->deposit($payload['amount']);

            return $transaction($payload);

        });
    }

    private function checkIfUserProviderExists(array $data): bool
    {
        try {
            $model = $this->getProvider($data['provider']);
            return (bool)$model->findOrFail($data['payee_id']);
        } catch (InvalidDataProviderException | \Exception $exception){
            return false;
        }
    }
}
