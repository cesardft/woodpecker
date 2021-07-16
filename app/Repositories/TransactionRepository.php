<?php


namespace App\Repositories;

use App\Exceptions\InsufficientAmountException;
use App\Exceptions\ServiceUnavailableException;
use App\Exceptions\TransactionDeniedException;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Retailer;
use App\Models\Wallet;
use App\Services\MockService;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Boolean;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\InvalidDataProviderException;

class TransactionRepository
{

    public function handle(array $data): Transaction // $data -> provider, payee_id, amount
    {

        // Se o provedor for retailer não autorizará fazer transações
        if (!$this->guardCanTransfer()){
            throw new TransactionDeniedException('Retailer is not authorized to make transactions.', 401);
        }

        if(!$payee = $this->checkIfUserProviderExists($data)){
            throw new InvalidDataProviderException('User not found.', 404);
        }

        $myWallet = Auth::guard($data['provider'])->user()->wallet;

        // Checa se há saldo na carteira
        if (!$this->hasBalance($myWallet, $data['amount'])){
            throw new InsufficientAmountException('Not enough cash. Stranger.', 422);
        }

        if($this->isAbleToMakeTransaction()){
            throw new ServiceUnavailableException('Service Unavailable.');
        }

        return $this->makeTransaction($payee, $data);
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

    private function checkIfUserProviderExists(array $data)
    {
        try {
            $model = $this->getProvider($data['provider']);
            return $model->find($data['payee_id']);
        } catch (InvalidDataProviderException | \Exception $exception){
            return false;
        }
    }

    private function makeTransaction($payee, array $data)
    {
        $payload = [
            'id' => random_int(0, 9999),
            'payer_wallet_id' => Auth::guard($data['provider'])->user()->wallet->id,
            'payee_wallet_id' => $payee->wallet->id,
            'amount' => $data['amount']
        ];

        return DB::transaction(function () use ($payload) {
            $transaction = Transaction::create($payload);

            $transaction->walletPayer->withdraw($payload['amount']);
            $transaction->walletPayee->deposit($payload['amount']);

            return $transaction;
        });
    }

    private function isAbleToMakeTransaction(): bool
    {
        $service = app(MockService::class)->authorizeTransaction();
        return $service == 'Autorizado';
    }
}
