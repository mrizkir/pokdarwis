@props([
  'pokdarwis' => null,
  'active' => null, // 'home'|'gallery'|'tour'|'packages'|'pages'|'contact'|'blog'
])

@php
use Illuminate\Support\Facades\Route;

$slug = $pokdarwis->slug ?? null;

$rl = function (string $name, $params = [], string $fallback = '#') {
    if (!Route::has($name)) return $fallback;
    try { return route($name, $params); } catch (\Throwable $e) { return $fallback; }
};

$hrefHome    = $slug ? $rl('pdw.home', ['slug' => $slug], url('/home'))           : url('/home');
// $hrefTour   = $slug ? $rl('pdw.tour', ['slug' => $slug], url('pokdarwis.show', ['pokdarwis' => $slug]))     : url('pokdarwis.show', ['pokdarwis' => $slug]);
$hrefTour = Route::has('pokdarwis.index')
    ? route('pokdarwis.index')
    : url('/tour');
$activeTourByRoute = (Route::is('pokdarwis.show') || Route::is('pokdarwis.*')) ? 'menu-active' : '';
/** PENANDA ITEM DROPDOWN YANG SEDANG AKTIF */
$isTourCurrent = function ($pd) use ($pokdarwis) {
    return ($pokdarwis && ($pokdarwis->id === $pd->id)) ? 'current-menu-item' : '';
};




$hrefDest    = $slug ? $rl('pdw.gallery', ['slug' => $slug], '/gallery')        : url('/gallery');
$hrefProd    = $slug ? $rl('pdw.products', ['slug' => $slug], '#')            : '#';
$hrefPages = $slug ? $rl('pdw.pages', ['slug' => $slug], url('/blogarchive')) : url('/blogarchive');
$hrefContact = $slug ? $rl('pdw.contact', ['slug' => $slug], url('/contact')) : url('/contact');


$isActive = fn ($name) => $active === $name ? 'menu-active' : '';

// 👉 fungsi untuk bikin URL detail pokdarwis
$pdUrl = function ($pd) {
    return route('pokdarwis.show', $pd->id); 
    // kalau pakai slug: return route('pokdarwis.show', $pd->slug);
};

// penanda menu item aktif
$isTourCurrent = function ($pd) use ($pokdarwis) {
    return $pokdarwis && ($pokdarwis->id === $pd->id) ? 'current-menu-item' : '';
};
@endphp

<div class="navigation-container-admin d-none d-lg-block">
  <nav id="navigation" class="navigation" aria-label="Main">
    <ul>
      @if(!auth()->check() || auth()->user()->role !== 'admin')
      <li class="{{ $isActive('home') }}"><a href="{{ $hrefHome }}">Home</a></li>
      {{-- <li class="{{ $isActive('about') }}"><a href="{{ $hrefAbout }}">about us</a></li> --}}
      <li class="{{ $isActive('gallery') }}"><a href="{{ $hrefDest }}">Gallery</a></li>
      @endif
      {{-- TOUR: aktif jika active="tour" ATAU route sekarang pokdarwis.show --}}
      @if (auth()->user()->role === 'wisatawan')
      <li class="menu-item-has-children {{ $isActive('tour') ?: $activeTourByRoute }}">
        <a href="{{ $hrefTour }}">tour</a>
        <ul>
          @forelse(($pokdarwisMenu ?? []) as $pd)
            <li class="{{ $isTourCurrent($pd) }}">
              <a href="{{ $pdUrl($pd) }}">{{ $pd->name_pokdarwis }}</a>
            </li>
          @empty
            <li><a href="#" tabindex="-1">— No data —</a></li>
          @endforelse
        </ul>
      </li>
      @endif

      @if (auth()->user()->role === 'pokdarwis')
      <li class="menu-item-has-children">
        <a href="">Upload</a>
        <ul>
          <li>
            <a href="{!! route('pokdarwis.product.index') !!}">Upload Produk</a>
          </li>
          <li>
            <a href="{!! route('pokdarwis.paket.index') !!}">Upload Paket</a>
          </li>
          <li>
            <a href="{!! route('pokdarwis.konten.index') !!}">Upload Konten</a>
          </li>
        </ul>
      </li>
      @endif

      @if(!auth()->check() || auth()->user()->role !== 'admin')
      <li class="menu-item-has-children">
      <a href="">Blog</a>
        <ul>
          <li>
            <a href="{!! route('pokdarwis.posts.index') !!}">Post Blog</a>
          </li>
        </ul>
      </li>
      @endif
      
      @if (auth()->user()->role === 'admin')
      <li class="menu-item-has-children {{ $isActive('pages') }}">
        <a href="#">Settings</a>
        <ul>          
          <li class="menu-item-has-children">
            <a href="#">Users</a>
            <ul>
              <li><a href="{!! route('settings-users-superadmin.index') !!}">Superadmin</a></li>
              <li><a href="{!! route('settings-users-pokdarwis.index') !!}">Pokdarwis</a></li>
              {{-- <li><a href="{!! route('settings-users-wisatawan.index') !!}">Wisatawan</a></li> --}}
            </ul>
          </li>                    
        </ul>
      </li>
      @endif
    </ul>
  </nav>
</div>
