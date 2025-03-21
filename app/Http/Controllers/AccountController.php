<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeFundsRequest;
use App\Http\Requests\CreateAccountRequest;
use App\Http\Requests\UpdateOverdraftLimitRequest;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function create(CreateAccountRequest $request): JsonResponse
    {
        $account = Account::createWithAttributes($request->validated());

        return response()->json([
            'message' => 'Account Created',
            'data' => [
                'uuid' => $account->uuid
            ]
        ], 201);
    }

    public function index(): JsonResponse
    {
        return response()->json([
           'message' => 'Accounts retrieved',
           'data' => Account::select(['uuid', 'firstname', 'lastname'])->get(),
        ]);
    }

    public function deposit(ChangeFundsRequest $request): JsonResponse
    {
        $account = Account::uuid($request->uuid);

        $account->deposit($request->amount);

        return response()->json([
            'message' => 'Deposited successfully'
        ]);
    }

    public function withdraw(ChangeFundsRequest $request): JsonResponse
    {
        $account = Account::uuid($request->uuid);

        $account->withdraw($request->amount);

        return response()->json([
            'message' => 'Withdrawn successfully'
        ]);
    }

    public function updateOverdraftLimit(UpdateOverdraftLimitRequest $request): JsonResponse
    {
        $account = Account::uuid($request->uuid);

        $account->updateOverdraftLimit($request->limit);

        return response()->json([
            'message' => 'Overdraft limit updated successfully'
        ]);
    }
}
