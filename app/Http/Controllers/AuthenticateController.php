<?php


namespace App\Http\Controllers;

use App\Repositories\AuthRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use PHPUnit\Framework\InvalidDataProviderException;

class AuthenticateController extends Controller
{

    /**
     * @var AuthRepository
     */
    private $repository;

    public function __construct(AuthRepository $repository)
    {
        $this->repository = $repository;
    }

    public function postAuthenticate(Request $request, string $provider){

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $fields = $request->only(['email', 'password']);

        try{
            $result = $this->repository->authenticate($provider, $fields);
            return response($result);
        } catch (InvalidDataProviderException $exception) {
            return response()->json(['error' => $exception->getMessage()], 422);
        } catch (AuthorizationException $exception) {
            return response()->json(['error' => $exception->getMessage()], 401);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 123);
        }

    }
}
