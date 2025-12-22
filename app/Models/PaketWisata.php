<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; 

class PaketWisata extends Model
{
      protected $table = 'paket_wisata';

    protected $fillable = [
        'pokdarwis_id','nama_paket','deskripsi','waktu_penginapan','pax',
        'lokasi','img','slug','harga','currency',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'pax'   => 'integer',
    ];

    // Route model binding pakai slug
    public function getRouteKeyName()
    {
        return 'slug';
    }
    protected static function booted()
    {
        static::creating(function ($m) {
            if (empty($m->slug)) {
                $base = Str::slug(Str::limit($m->nama_paket, 60, ''));
                $m->slug = $base.'-'.Str::random(6);
            }
        });
    }

    // Relasi (opsional kalau ada model Pokdarwis)
    public function pokdarwis()
    {
        return $this->belongsTo(Pokdarwis::class,'pokdarwis_id');
    }

    // Accessor: URL gambar siap pakai di view/komponen
    public function getCoverUrlAttribute()
    {
        // default fallback kalau img null
        $fallback = asset('assets/images/img1.jpg');

        $img = $this->img;
        if (!$img) return $fallback;

        // Jika sudah absolute URL, langsung pakai
        if (Str::startsWith($img, ['http://','https://','//'])) {
            return $img;
        }

        // Kalau path relatif (contoh: "products/...", "paket/...", "images/xyz.webp")
        // coba lewat disk 'public' dulu
        if (Storage::disk('public')->exists($img)) {
            return Storage::url($img); // -> /storage/<path>
        }

        // Jika bukan file di storage, anggap itu asset di public/ (mis: "assets/images/...")
        return asset($img) ?: $fallback;
    }

    public function getImageUrlAttribute()
    {
        $img = $this->img ?: 'assets/images/img1.jpg';
        if (Str::startsWith($img, ['http://','https://','//'])) return $img;
        if (Str::startsWith($img, 'paket/')) return asset('storage/'.$img); // upload disk public
        return asset($img);
    }

    // Accessor harga terformat
    public function getHargaFormattedAttribute()
    {
        return number_format((float)$this->harga, 2, '.', ',');
    }

    public function fasilitas()
    {
        return $this->hasMany(PaketFasilitas::class, 'paket_wisata_id');
    }

    // helper
    public function fasilitasInclude()
    {
        // return $this->hasMany(PaketFasilitas::class)->where('tipe','include')->orderBy('id');
        return $this->fasilitas()->where('tipe','include')->orderBy('sort_order')->orderBy('id');
    }
    public function fasilitasExclude()
    {
        // return $this->hasMany(PaketFasilitas::class)->where('tipe','exclude')->orderBy('id');
        return $this->fasilitas()->where('tipe','exclude')->orderBy('sort_order')->orderBy('id');
    }

    //Map
    public function getMapAddressAttribute()
        {
            return $this->pokdarwis?->alamat_maps;
        }
        public function getMapLatAttribute()
        {
        return $this->pokdarwis?->lat;
        }
        public function getMapLngAttribute()
        {
            return $this->pokdarwis?->lng;
        }
}
