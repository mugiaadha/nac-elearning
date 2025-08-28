<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
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

        try {
            $user = $request->user();
            $file = $request->file('bukti');

            // Hapus file lama jika ada
            if ($user->payment_proof && Storage::disk('public')->exists($user->payment_proof)) {
                Storage::disk('public')->delete($user->payment_proof);
            }

            $path = $file->store('payment_proofs', 'public');

            // Simpan path bukti pembayaran ke user atau tabel lain sesuai kebutuhan
            $user->payment_proof = $path;
            $user->status = 'admin-verif';
            $user->save();

            return $this->sendResponse([
                'payment_proof' => $path
            ], 'Bukti pembayaran berhasil diupload');
        } catch (Exception $e) {
            return $this->handleException($e, 'Upload payment proof');
        }
    // ...existing code...
    }
}
