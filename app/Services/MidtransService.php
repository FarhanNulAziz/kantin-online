<?php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = (string) config('services.midtrans.server_key');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createSnapToken(Order $order): string
    {
        $params = $this->buildPayload($order);
        $token = Snap::getSnapToken($params);

        $order->setAttribute('_midtrans_order_id', $params['transaction_details']['order_id']);

        return (string) $token;
    }

    public function buildPayload(Order $order): array
    {
        $order->loadMissing(['items.menu', 'user']);

        $midtransOrderId = $this->generateMidtransOrderId($order);

        $itemDetails = [];
        foreach ($order->items as $item) {
            $name = $item->menu?->nama ?? 'Menu';
            $itemDetails[] = [
                'id' => (string) $item->menu_id,
                'price' => (int) $item->harga_satuan,
                'quantity' => (int) $item->qty,
                'name' => mb_substr($name, 0, 50),
            ];
        }

        $biayaAdmin = (int) ($order->biaya_admin ?? 0);
        if ($biayaAdmin > 0) {
            $itemDetails[] = [
                'id' => 'ADMIN-FEE',
                'price' => $biayaAdmin,
                'quantity' => 1,
                'name' => 'Biaya Admin',
            ];
        }

        $sum = 0;
        foreach ($itemDetails as $detail) {
            $sum += (int) $detail['price'] * (int) $detail['quantity'];
        }

        $grossAmount = (int) $order->total_harga;
        if ($sum !== $grossAmount) {
            throw new \RuntimeException('Jumlah item_details tidak sama dengan gross_amount.');
        }

        $user = $order->user;

        return [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => $grossAmount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => mb_substr((string) ($user?->name ?? 'Pelanggan'), 0, 50),
                'email' => (string) ($user?->email ?? ''),
                'phone' => (string) ($user?->no_hp ?? ''),
            ],
        ];
    }

    public function generateMidtransOrderId(Order $order): string
    {
        $base = 'KANTIN-'.$order->id.'-'.time();
        $id = mb_substr($base, 0, 50);
        if (strlen($id) > 50) {
            $id = substr($id, 0, 50);
        }

        return $id;
    }
}
