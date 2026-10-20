<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('peminjaman');

        $stats = [
            'total' => $user->peminjaman->count(),
            'aktif' => $user->peminjaman->whereIn('status', ['diajukan', 'dipinjam', 'pengajuan_kembali'])->count(),
            'selesai' => $user->peminjaman->where('status', 'dikembalikan')->count(),
        ];

        return view('profile.me', compact('user', 'stats'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan user lain.',
            'foto_profile.image' => 'Foto profil harus berupa gambar.',
            'foto_profile.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        if ($request->hasFile('foto_profile')) {
            if ($user->foto_profile) {
                Storage::disk('public')->delete($user->foto_profile);
            }
            $data['foto_profile'] = $request->file('foto_profile')
                ->store('foto_profile', 'public');
        }

        $user->update($data);

        return redirect()->route('me')
            ->with('success', 'Biodata berhasil diperbarui.');
    }
}
