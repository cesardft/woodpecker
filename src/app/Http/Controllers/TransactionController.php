<?php


namespace App\Http\Controllers;


use App\Exceptions\TransactionDeniedException;
use App\Repositories\TransactionRepository;
use Laravel\Lumen\Http\Request;
use PHPUnit\Framework\InvalidDataProviderException;

class TransactionController
{

    /**
     * @var TransactionRepository
     */
    private $repository;

    public function __construct(TransactionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function postTransaction(Request $request)
    {
        $this->validate($request, [
            'provider' => 'required|in:users,retailers',
            'payee_id' => 'required',
            'amount' => 'required|numeric'

        ]);

       $fields = $request->only(['provider', 'payee_id', 'amount']);
        try {
            $result = $this->repository->handle($fields);
        } catch (InvalidDataProviderException $exception) {
            return response()->json(['error' => $exception->getMessage()], 422);
        } catch (TransactionDeniedException $exception){
            return response()->json(['error' => $exception->getMessage()], 401);

        }

        return response()->json($result);
    }
}
