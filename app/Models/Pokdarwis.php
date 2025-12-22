<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pokdarwis extends Model
{
    use HasFactory;

    protected $table = 'pokdarwis';

    protected $fillable = [
        'user_id',
        'name_pokdarwis',
        'slug',
        'lokasi',
        'deskripsi',
        'kontak',
        'img',
        'deskripsi2',
        'phone',
        'email',
        'facebook',
        'twitter',
        'instagram',
        'website',
        'visit_count_manual',
        'visit_count_auto',
        'cover_img','content_img','content_video',
        'alamat_maps', 'lat', 'lng'
    ];

    protected $casts = [
    'lat' => 'float',
    'lng' => 'float',
];

    /**
     * Relasi balik ke User (One to One)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paketWisata()
    {
        return $this->hasMany(PaketWisata::class, 'pokdarwis_id');
    }

    public function mediaKonten()
    {
        return $this->hasMany(\App\Models\MediaKonten::class , 'pokdarwis_id');
        
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
    public function pakets()
    {
        return $this->hasMany(\App\Models\PaketWisata::class, 'pokdarwis_id');
    }

    public function getVisitsTotalAttribute(): int
    {
        return (int)($this->visit_count_manual ?? 0) + (int)($this->visit_count_auto ?? 0);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    private function url(?string $p, ?string $fallback = null): ?string
{
    if (!$p) return $fallback;
    return Str::startsWith($p, ['http://','https://','//']) ? $p
         : (Str::startsWith($p,'assets/') ? asset($p) : asset('storage/'.$p));
}
    public function getImgUrlAttribute()          { return $this->url($this->img, asset('assets/images/default.png')); }
    public function getCoverImgUrlAttribute()     { return $this->url($this->cover_img, asset('assets/images/cover-default.jpg')); }
    public function getContentImgUrlAttribute()   { return $this->url($this->content_img); }
    public function getContentVideoUrlAttribute() { return $this->url($this->content_video); }
}
