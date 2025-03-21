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
        Account::createWithAttributes($request->validated());

        return response()->json([
            'message' => 'Account Created'
        ], 201);
    }
}
