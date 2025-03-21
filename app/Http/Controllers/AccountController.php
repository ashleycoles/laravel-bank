<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountRequest;
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
}
