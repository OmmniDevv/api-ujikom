<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::search($request->get('search'))
            ->byRole($request->get('role'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('foto_profile')) {
            $data['foto_profile'] = $request->file('foto_profile')
                ->store('foto_profile', 'public');
        }

        $user = User::create($data);

        ActivityLogger::log(
            'Buat User',
            "User baru dibuat: {$user->name} | Email: {$user->email} | Role: {$user->role}"
        );

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} berhasil ditambahkan.");
    }

    public function show(User $user)
    {
        $user->load('peminjaman');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        if ($request->hasFile('foto_profile')) {
            if ($user->foto_profile) {
                Storage::disk('public')->delete($user->foto_profile);
            }
            $data['foto_profile'] = $request->file('foto_profile')
                ->store('foto_profile', 'public');
        }

        $oldRole = $user->role;
        $user->update($data);

        $keterangan = "User: {$user->name} | Email: {$user->email}";
        if ($oldRole !== $user->role) {
            $keterangan .= " | Role diubah: {$oldRole} → {$user->role}";
        }

        ActivityLogger::log('Update User', $keterangan);

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $nama = $user->name;
        $email = $user->email;
        $role = $user->role;

        if ($user->foto_profile) {
            Storage::disk('public')->delete($user->foto_profile);
        }

        $user->delete();

        ActivityLogger::log(
            'Hapus User',
            "User dihapus: {$nama} | Email: {$email} | Role: {$role}"
        );

        return redirect()->route('admin.users.index')
            ->with('success', "User {$nama} berhasil dihapus.");
    }
}
