<?php


namespace App\Repositories;


use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\InvalidDataProviderException;
use App\Models\User;
use App\Models\Retailer;

class AuthRepository
{

    public function authenticate(string $provider, array $fields): array
    {
        $providers = ['user', 'retailer'];

        if(!in_array($provider, $providers)){
            throw new InvalidDataProviderException('Wrong provider.', 422);
        }

        $hasProvider = $this->getProvider($provider);
        $model = $hasProvider->where('email', $fields['email'])->first();

        if (!$model){
            throw new AuthorizationException('Wrong credentials.', 401);
        }

        if (!Hash::check($fields['password'], $model->password)){
            throw new AuthorizationException('Wrong credentials.', 401);

        }

        $token = $model->createToken($provider);

        return [
            'access_token' => $token->accessToken,
            'expires_at' => $token->token->expires_at,
            'provider' => $provider
        ];
    }

    public function getProvider(string $provider): AuthenticatableContract{
        if ($provider == 'user'){
            return new User();
        } else if ($provider == 'retailer'){
            return new Retailer();
        } else {
            throw new InvalidDataProviderException('Provider not found.');
        }
    }
}
