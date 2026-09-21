<?php

namespace App\Http\Controllers;

use App\Http\Requests\Kategori\StoreKategoriRequest;
use App\Http\Requests\Kategori\UpdateKategoriRequest;
use App\Models\Kategori;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $kategoris = Kategori::search($request->get('search'))
            ->withCount('alat')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.kategoris.index', compact('kategoris'));
    }

    public function create()
    {
        return view('admin.kategoris.create');
    }

    public function store(StoreKategoriRequest $request)
    {
        $kategori = Kategori::create($request->validated());

        ActivityLogger::log(
            'Buat Kategori',
            "Kategori baru: {$kategori->nama_kategori}".($kategori->deskripsi ? " | Deskripsi: {$kategori->deskripsi}" : '')
        );

        return redirect()->route('admin.kategoris.index')
            ->with('success', "Kategori {$kategori->nama_kategori} berhasil ditambahkan.");
    }

    public function show(Kategori $kategori)
    {
        $kategori->load('alat');

        return view('admin.kategoris.show', compact('kategori'));
    }

    public function edit(Kategori $kategori)
    {
        return view('admin.kategoris.edit', compact('kategori'));
    }

    public function update(UpdateKategoriRequest $request, Kategori $kategori)
    {
        $oldNama = $kategori->nama_kategori;
        $kategori->update($request->validated());

        $keterangan = "Kategori diperbarui: {$oldNama}";
        if ($oldNama !== $kategori->nama_kategori) {
            $keterangan .= " → {$kategori->nama_kategori}";
        }

        ActivityLogger::log('Update Kategori', $keterangan);

        return redirect()->route('admin.kategoris.index')
            ->with('success', "Kategori {$kategori->nama_kategori} berhasil diperbarui.");
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->masihDigunakan()) {
            return back()->with('error',
                "Kategori \"{$kategori->nama_kategori}\" tidak dapat dihapus karena masih digunakan oleh {$kategori->alat()->count()} alat."
            );
        }

        $nama = $kategori->nama_kategori;
        $kategori->delete();

        ActivityLogger::log(
            'Hapus Kategori',
            "Kategori dihapus: {$nama}"
        );

        return redirect()->route('admin.kategoris.index')
            ->with('success', "Kategori {$nama} berhasil dihapus.");
    }
}
