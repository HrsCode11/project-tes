<?php

namespace App\Services;

use App\Models\Tagihan;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');
    }

    public function createTransaction(Tagihan $tagihan): array
    {
        $warga   = $tagihan->warga;
        $orderId = 'INV-' . $tagihan->id . '-' . time();

        // Nama dari data warga. Email milik akun login, jadi hanya
        // disertakan kalau warga punya akun (email bersifat opsional di Midtrans)
        $customer = ['first_name' => $warga->nama_lengkap];

        if ($warga->user?->email) {
            $customer['email'] = $warga->user->email;
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $tagihan->nominal,
            ],
            'customer_details' => $customer,
            'item_details' => [
                [
                    'id'       => 'IURAN-' . $tagihan->iuran_id,
                    'price'    => (int) $tagihan->nominal,
                    'quantity' => 1,
                    'name'     => $tagihan->iuran->nama_iuran,
                ],
            ],
            'enabled_payments' => [
                'bca_va', 'bni_va', 'bri_va', 'permata_va', 'other_va', 'gopay',
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return [
            'order_id'   => $orderId,
            'snap_token' => $snapToken,
        ];
    }
}