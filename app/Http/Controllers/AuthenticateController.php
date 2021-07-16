<?php


namespace App\Http\Controllers;

use App\Repositories\AuthenticateRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use PHPUnit\Framework\InvalidDataProviderException;

class AuthenticateController extends Controller
{

    /**
     * @var AuthenticateRepository
     */
    private $repository;

    public function __construct(AuthenticateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function postAuthenticate(Request $request, string $provider){

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try{
            $fields = $request->only(['email', 'password']);
            $result = $this->repository->authenticate($provider, $fields);
        } catch (InvalidDataProviderException $exception) {
            return response()->json(['error' => $exception->getMessage()], 422);
        } catch (AuthorizationException $exception) {
            return response()->json(['error' => $exception->getMessage()], 401);
        }

        return response()->json($result);
    }
}
