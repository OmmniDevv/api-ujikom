<?php

namespace App\Http\Requests\Pengembalian;

use Illuminate\Foundation\Http\FormRequest;

class StorePengembalianRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'peminjaman_id' => ['required', 'exists:peminjaman,id'],
            'tgl_kembali'   => ['required', 'date'],
            'kondisi_alat'  => ['required', 'in:baik,rusak,perbaikan'],
            'catatan'       => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'peminjaman_id.required' => 'Data peminjaman wajib dipilih.',
            'peminjaman_id.exists'   => 'Data peminjaman tidak ditemukan.',
            'tgl_kembali.required'   => 'Tanggal kembali wajib diisi.',
            'kondisi_alat.required'  => 'Kondisi alat wajib diisi.',
            'kondisi_alat.in'        => 'Kondisi alat tidak valid.',
        ];
    }
}
