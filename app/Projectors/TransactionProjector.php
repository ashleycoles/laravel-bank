<?php

namespace App\Projectors;

use App\Events\TransactionSent;
use App\Models\Transaction;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class TransactionProjector extends Projector
{
    public function onTransactionSent(TransactionSent $event): void
    {
        Transaction::create([
            'sender_id' => $event->sender->id,
            'recipient_id' => $event->recipient->id,
            'amount' => $event->amount,
            'created_at' => $event->createdAt(),
        ]);
    }
}
