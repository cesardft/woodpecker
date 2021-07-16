<?php


namespace App\Http\Controllers;


use App\Exceptions\TransactionDeniedException;
use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;
use phpseclib3\Exception\InsufficientSetupException;
use PHPUnit\Framework\InvalidDataProviderException;

class TransactionController extends Controller
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
            return response()->json($result);

        } catch (InvalidDataProviderException | InsufficientSetupException $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        } catch (TransactionDeniedException $exception){
            return response()->json(['error' => $exception->getMessage()], 401);
        }
    }
}
