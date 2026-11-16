<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentController extends Controller
{
    public function pay(Request $request, Order $order, MidtransService $midtrans): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($order->payment_method !== 'online') {
            return response()->json(['message' => 'Pesanan COD tidak dapat dibayar melalui Midtrans.'], 422);
        }

        if ($order->payment_status !== 'pending') {
            return response()->json(['message' => 'Pesanan ini tidak dalam status menunggu pembayaran.'], 422);
        }

        if ($order->order_status === 'dibatalkan') {
            return response()->json(['message' => 'Pesanan yang dibatalkan tidak dapat dibayar.'], 422);
        }

        $pendingPayment = $order->payment()
            ->whereNotNull('snap_token')
            ->where(function ($q) {
                $q->whereNull('transaction_status')
                    ->orWhere('transaction_status', 'pending');
            })
            ->latest()
            ->first();

        if ($pendingPayment && $pendingPayment->snap_token) {
            return response()->json(['token' => $pendingPayment->snap_token]);
        }

        try {
            $token = $midtrans->createSnapToken($order);
            $midtransOrderId = $order->getAttribute('_midtrans_order_id');

            Payment::create([
                'order_id' => $order->id,
                'midtrans_order_id' => $midtransOrderId,
                'transaction_status' => 'pending',
                'snap_token' => $token,
                'jumlah' => $order->total_harga,
            ]);

            return response()->json(['token' => $token]);
        } catch (Throwable $e) {
            Log::error('Gagal membuat Snap token Midtrans.', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Gagal memproses pembayaran. Silakan coba lagi.',
            ], 500);
        }
    }
}

