<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Support\TransactionReceiptFilesystem;

class TransactionReceiptController extends Controller
{
    /**
     * Stream a receipt image for the owning user or a super-admin (Filament admin panel).
     */
    public function show(Transaction $transaction)
    {
        $user = auth()->user();

        if ($transaction->user_id !== $user?->getKey() && ! $user?->hasRole('super_admin')) {
            abort(403);
        }

        if ($transaction->receipt_image === null || $transaction->receipt_image === '') {
            abort(404);
        }

        $resolved = TransactionReceiptFilesystem::resolve($transaction->receipt_image);
        if ($resolved === null) {
            abort(404);
        }

        $basename = basename($resolved['path']);

        $mimeType = $resolved['disk']->mimeType($resolved['path']);

        return $resolved['disk']->response(
            $resolved['path'],
            $basename,
            $mimeType ? ['Content-Type' => $mimeType] : [],
        );
    }
}
