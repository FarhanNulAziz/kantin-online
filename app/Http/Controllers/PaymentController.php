<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        if ($order->order_status === 'dibatalkan') {
            return response()->json(['message' => 'Pesanan yang dibatalkan tidak dapat dibayar.'], 422);
        }

        return DB::transaction(function () use ($order, $midtrans): JsonResponse {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($lockedOrder->payment_status !== 'pending') {
                return response()->json(['message' => 'Pesanan ini tidak dalam status menunggu pembayaran.'], 422);
            }

            $pendingPayment = $lockedOrder->payments()
                ->whereNotNull('snap_token')
                ->where(function ($q) {
                    $q->whereNull('transaction_status')
                        ->orWhere('transaction_status', 'pending');
                })
                ->latest()
                ->first();

            if ($pendingPayment) {
                return response()->json(['token' => $pendingPayment->snap_token]);
            }

            try {
                $result = $midtrans->createSnapToken($lockedOrder);

                Payment::create([
                    'order_id' => $lockedOrder->id,
                    'midtrans_order_id' => $result['order_id'],
                    'transaction_status' => 'pending',
                    'snap_token' => $result['token'],
                    'jumlah' => $lockedOrder->total_harga,
                ]);

                return response()->json(['token' => $result['token']]);
            } catch (Throwable $e) {
                Log::error('Gagal membuat Snap token Midtrans.', [
                    'order_id' => $lockedOrder->id,
                    'error' => $e->getMessage(),
                ]);

                return response()->json([
                    'message' => 'Gagal memproses pembayaran. Silakan coba lagi.',
                ], 500);
            }
        });
    }
}
