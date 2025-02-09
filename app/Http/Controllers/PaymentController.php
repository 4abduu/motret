<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Transaction;

class PaymentController extends Controller
{
    public function createTransaction(Request $request)
    {
        $params = [
            'transaction_details' => [
                'order_id' => uniqid(),
                'gross_amount' => 10000, // Jumlah pembayaran
            ],
            'customer_details' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '081234567890',
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function handleNotification(Request $request)
    {
        $notification = new \Midtrans\Notification();

        $transaction = $notification->transaction_status;
        $type = $notification->payment_type;
        $orderId = $notification->order_id;
        $fraud = $notification->fraud_status;

        if ($transaction == 'capture') {
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    // TODO: Update database status to 'challenge'
                } else {
                    // TODO: Update database status to 'success'
                }
            }
        } else if ($transaction == 'settlement') {
            // TODO: Update database status to 'success'
        } else if ($transaction == 'pending') {
            // TODO: Update database status to 'pending'
        } else if ($transaction == 'deny') {
            // TODO: Update database status to 'deny'
        } else if ($transaction == 'expire') {
            // TODO: Update database status to 'expire'
        } else if ($transaction == 'cancel') {
            // TODO: Update database status to 'cancel'
        }

        return response()->json(['status' => 'success']);
    }
}