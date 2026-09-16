<?php

namespace App\Http\Controllers;

use App\Http\Requests\Alat\StoreAlatRequest;
use App\Http\Requests\Alat\UpdateAlatRequest;
use App\Models\Alat;
use App\Models\Kategori;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlatController extends Controller
{
    public function index(Request $request)
    {
        $alats = Alat::with('kategori')
            ->search($request->get('search'))
            ->byKategori($request->get('kategori_id'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('admin.alats.index', compact('alats', 'kategoris'));
    }
    public function katalog(Request $request)
    {
        $alats = Alat::with('kategori')
            ->where('status_kondisi', 'baik')
            ->where('stok', '>', 0)
            ->search($request->get('search'))
            ->byKategori($request->get('kategori_id'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('peminjam.katalog', compact('alats', 'kategoris'));
    }

    public function create()
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        return view('admin.alats.create', compact('kategoris'));
    }

    public function store(StoreAlatRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('alat', 'public');
        }

        $alat = Alat::create($data);

        ActivityLogger::log(
            'Buat Alat',
            "Alat baru: {$alat->nama_alat} | Kategori: {$alat->kategori?->nama_kategori} | Stok: {$alat->stok} | Kondisi: {$alat->status_kondisi}"
        );

        return redirect()->route('admin.alats.index')
            ->with('success', "Alat {$alat->nama_alat} berhasil ditambahkan.");
    }

    public function show(Alat $alat)
    {
        $alat->load('kategori', 'detailPinjam.peminjaman.user');
        return view('admin.alats.show', compact('alat'));
    }

    public function edit(Alat $alat)
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        return view('admin.alats.edit', compact('alat', 'kategoris'));
    }

    public function update(UpdateAlatRequest $request, Alat $alat)
    {
        $data    = $request->validated();
        $oldStok = $alat->stok;

        if ($request->hasFile('gambar')) {
            if ($alat->gambar) Storage::disk('public')->delete($alat->gambar);
            $data['gambar'] = $request->file('gambar')->store('alat', 'public');
        }

        $alat->update($data);

        $keterangan = "Alat: {$alat->nama_alat} | Kondisi: {$alat->status_kondisi}";
        if ($oldStok !== $alat->stok) {
            $keterangan .= " | Stok: {$oldStok} → {$alat->stok}";
        }

        ActivityLogger::log('Update Alat', $keterangan);

        return redirect()->route('admin.alats.index')
            ->with('success', "Alat {$alat->nama_alat} berhasil diperbarui.");
    }

    public function destroy(Alat $alat)
    {
        $nama = $alat->nama_alat;

        if ($alat->gambar) Storage::disk('public')->delete($alat->gambar);

        $alat->delete();

        ActivityLogger::log('Hapus Alat', "Alat dihapus: {$nama}");

        return redirect()->route('admin.alats.index')
            ->with('success', "Alat {$nama} berhasil dihapus.");
    }
}
