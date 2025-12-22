@props([
  // PRIORITAS: kalau ada lat+lng dipakai; kalau tidak ada → pakai address
  'lat' => null,
  'lng' => null,
  'address' => null,

  // opsi
  'zoom'   => 15,
  'height' => 320,
  'class'  => '',
  'showLink' => false,         // tampilkan tombol "Buka di Google Maps"
//   'linkText' => 'Buka di Google Maps',
])

@php
  // Tentukan query peta
  $query = null;

  // angka string "1.234" atau "1,234" → float
  $norm = function($v) {
    if ($v === null || $v === '') return null;
    return (float) str_replace(',', '.', $v);
  };

  $latN = $norm($lat);
  $lngN = $norm($lng);

  if ($latN !== null && $lngN !== null) {
    $query = $latN . ',' . $lngN;
  } elseif (filled($address)) {
    $query = $address;
  }

  $src   = $query ? ('https://www.google.com/maps?output=embed&q=' . urlencode($query) . '&z=' . intval($zoom)) : null;
  $gmaps = $query ? ('https://www.google.com/maps?q=' . urlencode($query)) : null;
@endphp

<div {{ $attributes->merge(['class' => 'package-map ' . $class]) }}>
  @if($src)
    <div style="position:relative;width:100%;height:{{ intval($height) }}px;overflow:hidden;border-radius:12px;">
      <iframe
        src="{{ $src }}"
        style="border:0;width:100%;height:100%;"
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        allowfullscreen>
      </iframe>
    </div>

    @if($showLink && $gmaps)
    @endif
  @else
    <div class="alert alert-primary mb-0" style="text-align: center">Location Not Found</div>
  @endif
</div>
