<?php

namespace App\Http\Controllers\Admin\Profile;


use App\Http\Controllers\Controller;
use App\Models\Pokdarwis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // pastikan login
    }

    //New public function edit Admin + Pokdarwis
    public function edit(Request $request)
{
    $user   = Auth::user();
    $userId = $user->id;

    // 1) Jika bukan role 'pokdarwis', jangan auto-create record
    if ($user->role !== 'pokdarwis') {
        // arahkan ke dashboard admin atau tampilkan info yang sesuai
        return redirect()
            ->route('admin.settings.users.superadmin.index') // ganti route ini dengan punyamu
            ->with('warning', 'Admin tidak memiliki profil Pokdarwis.');
}

    // 2) Ambil record jika ada
    $pokdarwis = Pokdarwis::where('user_id', $userId)->first();

    // 3) Jika belum ada, buat + generate slug unik
    if (!$pokdarwis) {
        $base = Str::slug($user->name ?: 'my-pokdarwis');
        $slug = $base;
        $i = 1;
        while (Pokdarwis::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        $pokdarwis = Pokdarwis::create([
            'user_id'         => $userId,
            'name_pokdarwis'  => $user->name ?? 'My Pokdarwis',
            'slug'            => $slug,
        ]);
    }

    return view('admin.profile.edit', compact('pokdarwis'));
}


    /**
     * Tampilkan form edit profil Pokdarwis milik user yang login. /Before Edit untuk Admin
     */
    // public function edit(Request $request)
    // {
    //     $userId = Auth::id();

    //     // Ambil pokdarwis milik user; kalau belum ada, siapkan instance kosong (opsional)
    //     $pokdarwis = Pokdarwis::firstOrCreate(
    //         ['user_id' => $userId],
    //         [
    //             'name_pokdarwis' => Auth::user()->name ?? 'My Pokdarwis',
    //             // 'slug'           => Str::slug(Auth::user()->name ?? 'my-pokdarwis-'.uniqid()),
    //         ]
    //     );

    //     return view('admin.profile.edit', compact('pokdarwis'));
    // }

    /**
     * Update profil Pokdarwis milik user yang login.
     */
    public function update(Request $request)
    {
        $userId    = Auth::id();
        $pokdarwis = Pokdarwis::where('user_id', $userId)->firstOrFail();

        $validated = $request->validate([
            // Pokdarwis basic
            'name_pokdarwis' => ['required','string','max:150'],
            'lokasi'         => ['nullable','string','max:150'],
            'deskripsi'      => ['nullable','string'],
            'deskripsi2'     => ['nullable','string'],
            'kontak' => 'nullable|regex:/^[0-9]{9,15}$/',
            'phone' => ['nullable','string','max:50'],
            'email' => ['nullable','email','max:190'],
            'facebook' => ['nullable','url','max:255'],
            'twitter' => ['nullable','url','max:255'],
            'instagram' => ['nullable','url','max:255'],
            'website' => ['nullable','url','max:255'],
            'visit_count_manual' => ['nullable','integer','min:0'],
            

            // Sosial/Contact opsional (buat kolomnya jika belum)
            // 'phone'      => ['nullable','string','max:50'],
            // 'email'      => ['nullable','email','max:190'],
            // 'facebook'   => ['nullable','url','max:255'],
            // 'twitter'    => ['nullable','url','max:255'],
            // 'instagram'  => ['nullable','url','max:255'],
            // 'pinterest'  => ['nullable','url','max:255'],
            // 'website'    => ['nullable','url','max:255'],

            // Avatar image
            'img'         => ['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],
            // Jika izinkan ubah slug manual (opsional)
            'slug'        => ['nullable','string','max:190','unique:pokdarwis,slug,'.$pokdarwis->id],
            'cover_img'          => ['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],
            'content_img'        => ['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],
            'content_video'      => ['nullable','url','max:1024'],           // URL (YouTube/Vimeo)
            'content_video_file' => ['nullable','mimetypes:video/mp4,video/webm,video/quicktime','max:51200'], // 50MB

            // ===== Lokasi & peta (INI YANG BELUM ADA) =====
            'alamat_maps' => ['nullable','string','max:1000'],
            'lat'         => ['nullable','numeric','between:-90,90'],
            'lng'         => ['nullable','numeric','between:-180,180'],
         ]);

         // ===== Normalisasi angka desimal (jaga-jaga pakai koma) =====
    foreach (['lat','lng'] as $k) {
        if (array_key_exists($k, $validated) && $validated[$k] !== null && $validated[$k] !== '') {
            $validated[$k] = (float) str_replace(',', '.', $validated[$k]);
        } else {
            $validated[$k] = null;
        }
    }

    // ===== Auto-parse koordinat dari tautan Google jika lat/lng kosong =====
    if ((!$validated['lat'] || !$validated['lng']) && !empty($validated['alamat_maps'])) {
        [$autoLat, $autoLng] = $this->extractLatLngFromGoogleUrl($validated['alamat_maps']);
        $validated['lat'] = $validated['lat'] ?? $autoLat;
        $validated['lng'] = $validated['lng'] ?? $autoLng;
    }
    

        // Handle upload foto profil (public storage)
        if ($request->hasFile('img')) {
            // hapus file lama jika ada
            if (!empty($pokdarwis->img) && !str_starts_with($pokdarwis->img, 'assets/')) {
                // hanya hapus kalau path lama disimpan di storage (bukan assets/)
                Storage::disk('public')->delete($pokdarwis->img);
            }

            $path = $request->file('img')->store('pokdarwis', 'public'); // simpan ke storage/app/public/pokdarwis
            $validated['img'] = $path; // simpan relative path (akan dipanggil via asset('storage/'.$path))
        }

        // Jika slug kosong, auto-generate dari name_pokdarwis
        if (empty($validated['slug']) && !empty($validated['name_pokdarwis'])) {
    $base = Str::slug($validated['name_pokdarwis']);
    $slug = $base;
    $i = 1;
    while (Pokdarwis::where('slug', $slug)->where('id','!=',$pokdarwis->id)->exists()) {
        $slug = $base.'-'.$i++;
    }
    $validated['slug'] = $slug;
}

        if ($request->hasFile('cover_img')) {
            if ($pokdarwis->cover_img) {
                Storage::disk('public')->delete($pokdarwis->cover_img);
            }
            $validated['cover_img'] = $request->file('cover_img')->store('pokdarwis/cover','public');
        }

        if ($request->hasFile('content_img')) {
            if ($pokdarwis->content_img) {
                Storage::disk('public')->delete($pokdarwis->content_img);
            }
            $validated['content_img'] = $request->file('content_img')->store('pokdarwis/content','public');
        }

        if (!empty($validated['content_video'])) {
            // pakai URL, hapus file lama kalau sebelumnya file
            if ($pokdarwis->content_video && Str::startsWith($pokdarwis->content_video, ['http://','https://','//'])) {
                Storage::disk('public')->delete($pokdarwis->content_video);
            }
        } elseif ($request->hasFile('content_video_file')) {
            if ($pokdarwis->content_video && Str::startsWith($pokdarwis->content_video, ['http://','https://','//'])) {
                Storage::disk('public')->delete($pokdarwis->content_video);
            }
            $path = $request->file('content_video_file')->store('pokdarwis/video','public');
            $validated['content_video'] = $path;
        }

        $pokdarwis->fill($validated)->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function index()
    {
        $userId = Auth::id();
        $pokdarwis = Pokdarwis::where('user_id', $userId)->firstOrFail();

        return view('admin.profile.index', compact('pokdarwis'));
        
    }
    

}
