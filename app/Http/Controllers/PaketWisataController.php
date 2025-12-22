<?php

namespace App\Http\Controllers;

use App\Models\PaketWisata;
use App\Models\PaketFasilitas;
use App\Models\Pokdarwis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PaketWisataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        {
        $pakets = PaketWisata::where('pokdarwis_id', 6)
            ->latest('id')
            ->paginate(3);

        // untuk <x-produkcard>, kita bisa mapping jadi array items:
        $items = $pakets->map(function ($p) {
            return [
                'image'    => $p->cover_url,
                'cat'      => $p->lokasi,
                'catUrl'   => route('paket.index'),        // atau route kategori kalau ada
                'title'    => $p->nama_paket,
                'titleUrl' => route('paket.show', $p),
                'desc'     => Str::limit(strip_tags($p->deskripsi), 110),
                'rating'   => 5, // kalau nanti ada kolom rating, ganti dari DB
            ];
        })->all();

        return view('paket.index', compact('pakets', 'items'));
    }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('paket.create');
    }

    public function store(Request $r)
{
    dd($r->all());
    $data = $r->validate([
        'nama_paket'       => 'required|string|max:100',
        'deskripsi'        => 'nullable|string',
        'waktu_penginapan' => 'required|string|max:20',
        'pax'              => 'required|integer|min:1',
        'lokasi'           => 'required|string|max:100',
        'harga'            => 'required|numeric|min:0',
        'currency'         => 'nullable|string|max:10',
        'img'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        // fasilitas (ARRAY) sesuai form
        'include_items'    => 'nullable|array',
        'include_items.*'  => 'nullable|string|max:255',
        'exclude_items'    => 'nullable|array',
        'exclude_items.*'  => 'nullable|string|max:255',
    ]);

    // 2) Ambil Pokdarwis milik user login (tabel pokdarwis punya user_id)
    $pokdarwis = Pokdarwis::where('user_id', Auth::id())->first();
    abort_unless($pokdarwis, 403, 'Profil Pokdarwis belum terdaftar.');

    // 3) Rapikan nilai fasilitas (trim + buang kosong)
    $includes = array_values(array_filter(array_map('trim', (array) $r->input('include_items', [])), fn($v)=>$v!==''));
    $excludes = array_values(array_filter(array_map('trim', (array) $r->input('exclude_items', [])), fn($v)=>$v!==''));

    // (opsional) debug bila perlu:
    // dd($r->all(), $includes, $excludes);

    // 4) Simpan paket + fasilitas dalam transaction
    DB::transaction(function () use ($r, $data, $pokdarwis, $includes, $excludes) {

        $path = $r->hasFile('img') ? $r->file('img')->store('paket', 'public') : null;

        $paket = PaketWisata::create([
            'pokdarwis_id'     => $pokdarwis->id,
            'nama_paket'       => $data['nama_paket'],
            'deskripsi'        => $data['deskripsi'] ?? null,
            'waktu_penginapan' => $data['waktu_penginapan'],
            'pax'              => $data['pax'],
            'lokasi'           => $data['lokasi'],
            'img'              => $path,
            'slug'             => Str::slug($data['nama_paket']).'-'.Str::random(5),
            'harga'            => $data['harga'],
            'currency'         => $data['currency'] ?? 'IDR',
        ]);

        // siapkan rows fasilitas untuk insert batch
        $rows = [];

        foreach ($includes as $i => $val) {
            $rows[] = [
                'paket_wisata_id' => $paket->id,
                'nama_item'       => $val,
                'tipe'            => 'include',    // sesuai ENUM di DB
                'sort_order'      => (int) $i,
                'created_at'      => now(),
                'updated_at'      => now(),
            ];
        }
        foreach ($excludes as $i => $val) {
            $rows[] = [
                'paket_wisata_id' => $paket->id,
                'nama_item'       => $val,
                'tipe'            => 'exclude',    // sesuai ENUM di DB
                'sort_order'      => (int) $i,
                'created_at'      => now(),
                'updated_at'      => now(),
            ];
        }

        if ($rows) {
            PaketFasilitas::insert($rows);
        }
    });

    return back()->with('success','Paket berhasil dibuat.');
}

    public function show($slug)
    {
        $paket = PaketWisata::with(['fasilitasInclude','fasilitasExclude'])
                 ->where('slug',$slug)->firstOrFail();

        $others = PaketWisata::where('pokdarwis_id', $paket->pokdarwis_id)
        ->where('id', '!=', $paket->id)
        ->orderBy('id', 'desc')   // atau ->inRandomOrder()
        ->take(5)
        ->get();

        return view('detailpaket', compact('paket', 'others'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaketWisata $paketWisata)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaketWisata $paketWisata)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaketWisata $paketWisata)
    {
        //
    }
}


// Route
// use App\Http\Controllers\PaketWisataController;

// Route::get('/paket/create', [PaketWisataController::class,'create'])->name('paket.create');
// Route::post('/paket', [PaketWisataController::class,'store'])->name('paket.store');

// Route::get('/paket/{slug}', [PaketWisataController::class,'show'])->name('paket.show');

// // daftar paket untuk publik/dashboard (opsional singkat)
// Route::get('/paket', function () {
//     return \App\Models\PaketWisata::latest()->paginate(12);
// })->name('paket.index');
