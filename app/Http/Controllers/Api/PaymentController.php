<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Exception;

class PaymentController extends BaseController
{
    /**
     * Upload bukti pembayaran
     */
    public function uploadProof(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bukti' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Data tidak valid', $validator->errors(), 422);
        }

        $user = $request->user();
        $file = $request->file('bukti');
        $path = $file->store('payment_proofs', 'public');

        // Simpan path bukti pembayaran ke user atau tabel lain sesuai kebutuhan
        $user->payment_proof = $path;
        $user->status = 'admin-verif';
        $user->save();

        return $this->sendResponse([
            'payment_proof' => $path
        ], 'Bukti pembayaran berhasil diupload');
    }
}
