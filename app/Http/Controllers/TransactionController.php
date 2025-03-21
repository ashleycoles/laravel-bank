<?php

namespace App\Http\Controllers;

use App\Exceptions\CouldNotSendTransaction;
use App\Http\Requests\TransactionRequest;
use App\Models\Account;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function send(TransactionRequest $request): JsonResponse
    {
        $sender = Account::uuid($request->sender_uuid);

        if (! $sender) {
            return response()->json([
                'message' => "No account found with UUID: $request->sender_uuid",
            ], 404);
        }

        $recipient = Account::uuid($request->recipient_uuid);

        if (! $recipient) {
            return response()->json([
                'message' => "No account found with UUID: $request->recipient_uuid",
            ], 404);
        }

        try {
            $sender->sendTransaction($request->amount);
            $recipient->receiveTransaction($request->amount);
        } catch (CouldNotSendTransaction $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        }

        return response()->json([
            'message' => 'Transaction sent',
        ]);
    }
}
