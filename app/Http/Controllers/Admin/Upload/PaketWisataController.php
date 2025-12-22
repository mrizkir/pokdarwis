<?php

namespace App\Http\Controllers\Admin\Upload;

use App\Http\Controllers\Controller;
use App\Models\PaketWisata;
use App\Models\PaketFasilitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class PaketWisataController extends Controller
{
    public function index()
    {
        $pokdarwis = Auth::user()->pokdarwis;
        abort_unless($pokdarwis, 403, 'Profil Pokdarwis belum terdaftar.');

        $pakets = PaketWisata::where('pokdarwis_id', $pokdarwis->id)
            ->latest('id')->paginate(12);

        return view('admin.upload.paket.index', compact('pakets'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
        'nama_paket'       => 'required|string|max:100',
        'deskripsi'        => 'nullable|string',
        'waktu_penginapan' => 'required|string|max:20',
        'pax'              => 'required|integer|min:1',
        'lokasi'           => 'required|string|max:100',
        'harga'            => 'required|numeric|min:0',
        'currency'         => 'nullable|string|max:10',
        'img'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

        // fasilitas
        'include'             => ['nullable','array'],
        'include.*.nama_item' => ['nullable','string','max:255'],
        'exclude'             => ['nullable','array'],
        'exclude.*.nama_item' => ['nullable','string','max:255'],
    ]);
    
Log::info('REQ include', $r->input('include', []));
Log::info('REQ exclude', $r->input('exclude', []));



    $pokdarwis = Auth::user()->pokdarwis;
    abort_unless($pokdarwis, 403, 'Profil Pokdarwis belum terdaftar.');

    DB::transaction(function () use ($r, $data, $pokdarwis) {
        // Simpan paket (HANYA SEKALI)
        $path = $r->hasFile('img') ? $r->file('img')->store('paket','public') : null;

        $paket = PaketWisata::create([
            'pokdarwis_id'     => $pokdarwis->id,
            'nama_paket'       => $data['nama_paket'],
            'deskripsi'        => $data['deskripsi'] ?? null,
            'waktu_penginapan' => $data['waktu_penginapan'],
            'pax'              => $data['pax'],
            'lokasi'           => $data['lokasi'],
            'img'              => $path,
            'slug'             => Str::slug($data['nama_paket']).'-'.Str::random(6),
            'harga'            => $data['harga'],
            'currency'         => $data['currency'] ?? 'IDR',
        ]);

        // Helper insert fasilitas
        $insertFacilities = function(array $rows = null, string $tipe) use ($paket) {
    collect($rows ?? [])
        ->map(fn($row) => ['nama_item' => trim($row['nama_item'] ?? '')])
        ->filter(fn($row) => $row['nama_item'] !== '')
        ->each(function($row) use ($paket, $tipe) {
            $paket->fasilitas()->create([   // <-- pakai RELASI
                'tipe'       => $tipe,
                'nama_item'  => $row['nama_item'],
                // 'sort_order' => 0, // opsional, default 0 dari DB kamu
            ]);
        });
};

        // Simpan include & exclude (opsional, yang kosong otomatis di-skip)
        $insertFacilities($data['include'] ?? [], 'include');
        $insertFacilities($data['exclude'] ?? [], 'exclude');
    });

    return back()->with('success','Paket berhasil ditambahkan.');
    }

    public function update(Request $r, PaketWisata $paket)
    {
        $this->authorizePaket($paket);

    $data = $r->validate([
        'nama_paket'       => 'required|string|max:100',
        'deskripsi'        => 'nullable|string',
        'waktu_penginapan' => 'required|string|max:20',
        'pax'              => 'required|integer|min:1',
        'lokasi'           => 'required|string|max:100',
        'harga'            => 'required|numeric|min:0',
        'currency'         => 'nullable|string|max:10',
        'img'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

        'include'             => ['nullable','array'],
        'include.*.nama_item' => ['nullable','string','max:255'],
        'exclude'             => ['nullable','array'],
        'exclude.*.nama_item' => ['nullable','string','max:255'],
    ]);

    DB::transaction(function () use ($r, $paket, $data) {
        // Update paket (HANYA SEKALI)
        if ($r->hasFile('img')) {
            if ($paket->img && str_starts_with($paket->img, 'paket/')) {
                Storage::disk('public')->delete($paket->img);
            }
            $data['img'] = $r->file('img')->store('paket','public'); // konsisten di folder 'paket'
        }

        $paket->update([
            'nama_paket'       => $data['nama_paket'],
            'deskripsi'        => $data['deskripsi'] ?? null,
            'waktu_penginapan' => $data['waktu_penginapan'],
            'pax'              => $data['pax'],
            'lokasi'           => $data['lokasi'],
            'harga'            => $data['harga'],
            'currency'         => $data['currency'] ?? 'IDR',
            'img'              => $data['img'] ?? $paket->img,
        ]);

        // Sinkron fasilitas: hapus lama → tulis ulang yang baru
        $paket->fasilitas()->delete();

        $save = function(array $rows = null, string $tipe) use ($paket) {
            collect($rows ?? [])
                ->map(fn($r) => ['nama_item' => trim($r['nama_item'] ?? '')])
                ->filter(fn($r) => $r['nama_item'] !== '')
                ->each(fn($r) => $paket->fasilitas()->create([
                    'tipe'      => $tipe,
                    'nama_item' => $r['nama_item'],
                ]));
        };

        $save($data['include'] ?? [], 'include');
        $save($data['exclude'] ?? [], 'exclude');
    });

    return back()->with('success','Paket berhasil diperbarui.');
    }

    public function destroy(PaketWisata $paket)
    {
        $this->authorizePaket($paket);

        if ($paket->img && str_starts_with($paket->img, 'paket/')) {
            Storage::disk('public')->delete($paket->img);
        }
        $paket->delete();

        return back()->with('success','Paket berhasil dihapus.');
    }

    protected function authorizePaket(PaketWisata $paket)
    {
        $pd = Auth::user()->pokdarwis;
        abort_unless($pd && $paket->pokdarwis_id == $pd->id, 403);
    }
}
