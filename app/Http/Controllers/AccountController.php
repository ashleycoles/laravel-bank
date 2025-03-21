<?php

namespace App\Http\Controllers;

use App\Exceptions\CouldNotUpdateOverdraftLimit;
use App\Exceptions\CouldNotWithdraw;
use App\Http\Requests\ChangeFundsRequest;
use App\Http\Requests\CreateAccountRequest;
use App\Http\Requests\UpdateOverdraftLimitRequest;
use App\Models\Account;
use Exception;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    public function create(CreateAccountRequest $request): JsonResponse
    {
        $account = Account::createWithAttributes($request->validated());

        if (! $account) {
            return response()->json([
                'message' => 'Account creation failed',
            ], 500);
        }

        return response()->json([
            'message' => 'Account Created',
            'data' => [
                'uuid' => $account->uuid,
            ],
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

        if (! $account) {
            return response()->json([
                'message' => 'Account not found',
            ], 404);
        }

        try {
            $account->deposit($request->amount);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Deposited successfully',
        ]);
    }

    public function withdraw(ChangeFundsRequest $request): JsonResponse
    {
        $account = Account::uuid($request->uuid);

        if (! $account) {
            return response()->json([
                'message' => 'Account not found',
            ], 404);
        }

        try {
            $account->withdraw($request->amount);
        } catch (CouldNotWithdraw $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        }

        return response()->json([
            'message' => 'Withdrawn successfully',
        ]);
    }

    public function updateOverdraftLimit(UpdateOverdraftLimitRequest $request): JsonResponse
    {
        $account = Account::uuid($request->uuid);

        if (! $account) {
            return response()->json([
                'message' => 'Account not found',
            ], 404);
        }

        try {
            $account->updateOverdraftLimit($request->limit);
        } catch (CouldNotUpdateOverdraftLimit $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        }

        return response()->json([
            'message' => 'Overdraft limit updated successfully',
        ]);
    }
}
