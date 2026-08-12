<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreJadwalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        $rules = [
            'id_murid' => 'required|exists:murids,id_murid',
            'id_program' => 'required|exists:program_kursus,id_program',
            'id_guru' => 'required|exists:gurus,id_guru',
            'total_sesi' => 'required|integer|in:4,8,12,16,20,24',
            'tipe_les' => 'required|in:Onsite,Home Private',
            'tipe_jadwal' => 'required|in:tetap,pola,manual',
            'tanggal_mulai' => 'required|date',
        ];

        if ($this->tipe_jadwal === 'tetap') {
            $rules['pola_tunggal.hari'] = 'required|string';
            $rules['pola_tunggal.jam_mulai'] = 'required|date_format:H:i';
            $rules['pola_tunggal.jam_selesai'] = 'required|date_format:H:i|after:pola_tunggal.jam_mulai';
        } elseif ($this->tipe_jadwal === 'pola') {
            $rules['pola'] = 'required|array|size:4';
            $rules['pola.*.hari'] = 'required|string';
            $rules['pola.*.jam_mulai'] = 'required|date_format:H:i';
            $rules['pola.*.jam_selesai'] = 'required|date_format:H:i|after:pola.*.jam_mulai';
        } elseif ($this->tipe_jadwal === 'manual') {
            $rules['jadwal_manual'] = 'required|array';
            $rules['jadwal_manual.*.tanggal'] = 'required|date';
            $rules['jadwal_manual.*.jam_mulai'] = 'required|date_format:H:i';
            $rules['jadwal_manual.*.jam_selesai'] = 'required|date_format:H:i|after:jadwal_manual.*.jam_mulai';
        }

        return $rules;
    }
}
