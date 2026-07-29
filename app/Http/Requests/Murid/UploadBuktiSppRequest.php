<?php

namespace App\Http\Requests\Murid;

use Illuminate\Foundation\Http\FormRequest;

class UploadBuktiSppRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'murid';
    }

    public function rules(): array
    {
        return [
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'nominal_bayar'  => 'required|numeric|min:0',
            'tanggal_bayar'  => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'bukti_transfer.required' => 'File bukti transfer wajib diunggah.',
            'bukti_transfer.mimes'    => 'Format file bukti transfer harus berupa JPG, PNG, atau PDF.',
            'bukti_transfer.max'      => 'Ukuran file bukti transfer maksimal 5 MB.',
            'nominal_bayar.required'  => 'Nominal pembayaran wajib diisi.',
            'tanggal_bayar.required'  => 'Tanggal pembayaran wajib diisi.',
        ];
    }
}
