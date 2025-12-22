<?php

namespace App\Http\Controllers\Admin\Upload;

use App\Http\Controllers\Controller;
use App\Models\PaketWisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PackagesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    // GET /pokdarwis/upload/paket
    public function create()
    {
        return view('admin.upload.paket.uploadPaket');
    }

    // POST /pokdarwis/upload/paket
    public function store(Request $request)
    {
        // VALIDASI
        $data = $request->validate([
            'nama_paket'       => ['required','string','max:255'],
            'deskripsi'        => ['nullable','string'],
            'waktu_penginapan' => ['nullable','string','max:50'], // ex: 3D2N
            'pax'              => ['nullable','integer','min:1'],
            'lokasi'           => ['nullable','string','max:255'],
            'harga'            => ['required','numeric','min:0'],
            'currency'         => ['nullable','string','max:10'],
            'img'              => ['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],

            // fasilitas
            'include'             => ['nullable','array'],
            'include.*.nama_item' => ['nullable','string','max:255'],
            'exclude'             => ['nullable','array'],
            'exclude.*.nama_item' => ['nullable','string','max:255'],
        ]);

        $pokdarwis = Auth::user()->pokdarwis;
        abort_unless($pokdarwis, 403, 'Profil Pokdarwis belum terdaftar.');

        DB::transaction(function () use ($request, $data, $pokdarwis) {

            // siapkan payload paket
            $payload = [
                'pokdarwis_id'     => $pokdarwis->id,
                'nama_paket'       => $data['nama_paket'],
                'deskripsi'        => $data['deskripsi']        ?? null,
                'waktu_penginapan' => $data['waktu_penginapan'] ?? null,
                'pax'              => $data['pax']              ?? 1,
                'lokasi'           => $data['lokasi']           ?? null,
                'harga'            => $data['harga'],
                'currency'         => $data['currency']         ?? 'IDR',
            ];

            if ($request->hasFile('img')) {
                // simpan ke storage/app/public/paket
                $payload['img'] = $request->file('img')->store('paket', 'public');
            }

            // 1) simpan paket
            $paket = PaketWisata::create($payload);

            // 2) normalisasi fasilitas → buang baris kosong
            $map = function(array $rows = null, string $tipe) {
                $out = [];
                foreach ($rows ?? [] as $row) {
                    $nama = isset($row['nama_item']) ? trim($row['nama_item']) : '';
                    if ($nama !== '') {
                        $out[] = ['tipe' => $tipe, 'nama_item' => $nama];
                    }
                }
                return $out;
            };

            $rows = array_merge(
                $map($data['include'] ?? [], 'include'),
                $map($data['exclude'] ?? [], 'exclude'),
            );

            // 3) simpan fasilitas (kalau ada)
            if (!empty($rows)) {
                $paket->fasilitas()->createMany($rows); // FK otomatis diisi
            }
        });

        return back()->with('success', 'Paket wisata berhasil diupload.');
    }
}
