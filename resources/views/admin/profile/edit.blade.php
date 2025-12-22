@extends('layouts.app-backend')

@section('page-title', 'Edit Profile')

@section('page-header')
  <h2 class="mb-0">Edit Profile Pokdarwis</h2>
@endsection

@section('page-breadcrumb')
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="{{ route('profile.index') }}">Profile</a></li>
      <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
  </nav>
@endsection

@section('main')
<style>
  .card-rounded { border:1px solid #e5e7eb; border-radius:16px; box-shadow:0 10px 22px rgba(16,24,40,.06); }
  .section-title { font-weight:700; font-size:1.05rem; color:#0f172a; margin-bottom:1rem; }
  .help { font-size:.85rem; color:#64748b; }
  .avatar-preview { width:120px;height:120px;border-radius:50%;object-fit:cover;border:1px solid #e5e7eb; }
  .label-icon i { width:18px; text-align:center; margin-right:.4rem; color:#475569; }
</style>

<div class="container py-4">
  <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">

      {{-- ========== Kolom Kiri: Info Utama + Avatar ========== --}}
      <div class="col-lg-7">
  <div class="card card-rounded">
    <div class="card-body">
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @php
        $imgNow        = $pokdarwis->img         ? asset('storage/'.$pokdarwis->img)         : asset('assets/images/default.png');
        $coverNow      = $pokdarwis->cover_img   ? asset('storage/'.$pokdarwis->cover_img)   : asset('assets/images/default-cover.jpg');
        $contentImgNow = $pokdarwis->content_img ? asset('storage/'.$pokdarwis->content_img) : asset('assets/images/default.png');

        $videoNow = $pokdarwis->content_video
                   ? (\Illuminate\Support\Str::startsWith($pokdarwis->content_video, ['http://','https://','//'])
                        ? $pokdarwis->content_video
                        : asset('storage/'.$pokdarwis->content_video))
                   : null;
      @endphp

      {{-- Avatar (live preview) --}}
      <div class="mb-4">
        <div class="d-flex align-items-center gap-3">
          <img id="previewImg" src="{{ $imgNow }}" alt="avatar" class="avatar-preview">
          <div>
            <label class="form-label mb-1">Foto Profil</label>
            <input type="file" name="img" id="f_img" class="form-control" accept="image/*">
            <div class="help">Maks 5MB. Format: JPG/PNG/WEBP.</div>
          </div>
        </div>
      </div>

      {{-- ====== Media Konten (BARU) ====== --}}
      <div class="section-title">Media Konten</div>

      {{-- Cover image --}}
      <div class="d-flex align-items-center gap-3 mb-4">
        <img id="previewCover" src="{{ $coverNow }}" alt="cover" class="avatar-preview" style="width:160px;height:90px;border-radius:12px;">
        <div>
          <label class="form-label mb-1">Cover Image (banner)</label>
          <input type="file" name="cover_img" id="f_cover" class="form-control" accept="image/*">
          <div class="help">Header/hero, disarankan rasio 16:9.</div>
        </div>
      </div>

      {{-- Content image --}}
      <div class="d-flex align-items-center gap-3 mb-4">
        <img id="previewContentImg" src="{{ $contentImgNow }}" alt="content" class="avatar-preview" style="width:140px;height:140px;">
        <div>
          <label class="form-label mb-1">Content Image</label>
          <input type="file" name="content_img" id="f_content_img" class="form-control" accept="image/*">
          <div class="help">Gambar untuk bagian konten utama.</div>
        </div>
      </div>

      {{-- Content video (URL atau file) --}}
      <div class="mb-3">
        <label class="form-label">Content Video (URL)</label>
        <input type="url" name="content_video" class="form-control"
               placeholder="https://www.youtube.com/watch?v=..."
               value="{{ old('content_video', \Illuminate\Support\Str::startsWith($pokdarwis->content_video ?? '', ['http://','https://','//']) ? $pokdarwis->content_video : '') }}">
        <div class="help">Isi URL YouTube/Vimeo. Jika URL diisi, file upload di bawah diabaikan.</div>
      </div>
      {{-- <div class="mb-4">
        <label class="form-label">Atau upload file video</label>
        <input type="file" name="content_video_file" class="form-control" accept="video/mp4,video/webm,video/quicktime">
        <div class="help">MP4/WEBM/MOV, maks 50MB.</div>

        @if($videoNow)
          <div class="mt-2">
            <div class="help mb-1">Video saat ini:</div>
            @if(\Illuminate\Support\Str::contains($videoNow, ['youtube.com','youtu.be','vimeo.com']))
              <a href="{{ $videoNow }}" target="_blank">{{ $videoNow }}</a>
            @else
              <video controls style="width:100%;max-width:420px;border-radius:12px;">
                <source src="{{ $videoNow }}" type="video/mp4">
              </video>
            @endif
          </div>
        @endif
      </div> --}}

      <hr>

      {{-- Nama --}}
      <div class="mb-3">
        <label class="form-label">Nama Pokdarwis</label>
        <input type="text" name="name_pokdarwis" class="form-control"
               value="{{ old('name_pokdarwis', $pokdarwis->name_pokdarwis) }}" required>
      </div>

      {{-- Lokasi --}}
      <div class="mb-3">
        <label class="form-label">Lokasi</label>
        <input type="text" name="lokasi" class="form-control"
               value="{{ old('lokasi', $pokdarwis->lokasi) }}">
      </div>

      {{-- Deskripsi --}}
      <div class="mb-3">
        <label class="form-label">Slogan</label>
        <textarea name="deskripsi" rows="3" class="form-control">{{ old('deskripsi', $pokdarwis->deskripsi) }}</textarea>
      </div>

      {{-- Deskripsi 2 --}}
      <div class="mb-0">
        <label class="form-label">Deskripsi Tempat</label>
        <textarea name="deskripsi2" rows="3" class="form-control">{{ old('deskripsi2', $pokdarwis->deskripsi2) }}</textarea>
      </div>
    </div>
  </div>
</div>

      {{-- ========== Kolom Kanan: Kontak & Media Sosial ========== --}}
      <div class="col-lg-5">

        {{-- Kontak --}}
        <div class="card card-rounded mb-4">
          <div class="card-body">
            <div class="section-title">Kontak</div>

            <div class="mb-3">
              <label class="form-label">Nomor WhatsApp</label>
              <input type="text"
                    name="kontak"
                    class="form-control"
                    placeholder="Contoh: 08123456789"
                    value="{{ old('kontak', $pokdarwis->kontak) }}">
              <div class="form-text">Gunakan format angka, misalnya <b>08123456789</b>.</div>
            </div>


            <div class="mb-3">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control"
                     value="{{ old('phone', $pokdarwis->phone) }}">
            </div>

            <div class="mb-0">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control"
                     value="{{ old('email', $pokdarwis->email) }}">
            </div>
          </div>
        </div>

        {{-- Media Sosial --}}
        <div class="card card-rounded mb-4">
          <div class="card-body">
            <div class="section-title">Media Sosial</div>

            <div class="mb-3">
              <label class="form-label label-icon"><i class="fab fa-facebook-f"></i> Facebook (URL)</label>
              <input type="url" name="facebook" class="form-control"
                     value="{{ old('facebook', $pokdarwis->facebook) }}">
            </div>

            <div class="mb-3">
              <label class="form-label label-icon"><i class="fab fa-twitter"></i> Twitter (URL)</label>
              <input type="url" name="twitter" class="form-control"
                     value="{{ old('twitter', $pokdarwis->twitter) }}">
            </div>

            <div class="mb-3">
              <label class="form-label label-icon"><i class="fab fa-instagram"></i> Instagram (URL)</label>
              <input type="url" name="instagram" class="form-control"
                     value="{{ old('instagram', $pokdarwis->instagram) }}">
            </div>

            <div class="mb-0">
              <label class="form-label label-icon"><i class="fas fa-globe"></i> Website (URL)</label>
              <input type="url" name="website" class="form-control"
                     value="{{ old('website', $pokdarwis->website) }}">
            </div>
          </div>
        </div>
{{-- Kunjungan Wisatawan (Additive) --}}
<div class="card card-rounded mb-4">
  <div class="card-body">
    <div class="section-title">Kunjungan Wisatawan</div>

    @php
      $seed   = (int) ($pokdarwis->visit_count_manual ?? 0);
  $clicks = (int) ($pokdarwis->visit_count_auto ?? 0);
  $total  = $seed + $clicks;
    @endphp

    <div class="mb-3">
      <label class="form-label">Kunjungan Wisatawan</label>
      <input type="number" name="visit_count_manual" class="form-control"
             value="{{ old('visit_count_manual', $seed) }}" min="0" step="1" />
      <div class="form-text">Isi angka kunjungan yang dimiliki Pokdarwis (data historis).</div>
    </div>

    <div class="row g-3">
      <div class="col">
        <label class="form-label d-block">Kunjungan</label>
        <div class="help"><b>{{ number_format($clicks,0,',','.') }}</b></div>
      </div>
      <div class="col">
        <label class="form-label d-block">Total Saat Ini</label>
        <div class="help"><b>{{ number_format($total,0,',','.') }}</b></div>
      </div>
    </div>
  </div>
</div>

{{-- Lokasi & Peta --}}
<div class="card card-rounded mb-4">
  <div class="card-body">
    <div class="section-title">Lokasi & Peta</div>

    @php
      // nilai saat ini
      $addr = old('alamat_maps', $pokdarwis->alamat_maps);
      $latv = old('lat', $pokdarwis->lat);
      $lngv = old('lng', $pokdarwis->lng);

      // build src untuk preview (prioritas lat,lng)
      $mapQuery = ($latv && $lngv) ? ($latv . ',' . $lngv) : ($addr ?: null);
      $mapSrc   = $mapQuery ? 'https://www.google.com/maps?output=embed&q=' . urlencode($mapQuery) . '&z=15' : null;
    @endphp

    <div class="mb-3">
      <label class="form-label">Alamat / Tautan Google Maps</label>
      <input type="text" name="alamat_maps" id="alamat_maps" class="form-control"
             placeholder="Masukan Link / URL Lokasi Pada Google Maps"
             value="{{ $addr }}">
      <div class="help">
        
      </div>
    </div>

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Latitude</label>
        <input type="text" name="lat" id="lat" class="form-control" inputmode="decimal"
               placeholder="-0.1234567" value="{{ $latv }}">
      </div>
      <div class="col-md-6">
        <label class="form-label">Longitude</label>
        <input type="text" name="lng" id="lng" class="form-control" inputmode="decimal"
               placeholder="104.1234567" value="{{ $lngv }}">
      </div>
    </div>

    <div class="d-flex gap-2 mt-3">
      <button type="button" class="btn btn-outline-secondary btn-sm" id="btnParseLink">
        Ambil Koordinat dari Link
      </button>
      <button type="button" class="btn btn-outline-secondary btn-sm" id="btnUseCoords">
        Pakai Lat/Lng untuk Preview
      </button>
      {{-- <button type="button" class="btn btn-outline-secondary btn-sm" id="btnUseAddress">
        Pakai Alamat untuk Preview
      </button> --}}
    </div>

    <div class="mt-3" id="mapWrap" style="display: {{ $mapSrc ? 'block' : 'none' }};">
      <div style="position:relative;width:100%;height:260px;overflow:hidden;border-radius:12px;">
        <iframe id="mapIframe"
                src="{{ $mapSrc ?? '' }}"
                style="border:0;width:100%;height:100%;"
                loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
      </div>
    </div>

    <div class="help mt-2">
      Tips: Di Google Maps, klik kanan titik lokasi untuk melihat titik Latitude & Longitude -> <b>Klik Koordinat untuk Salin</b>
    </div>
  </div>
</div>
        
      </div>
    </div>

    {{-- Tombol Simpan --}}
    <div class="text-end mt-4">
      <a href="{{ route('profile.index') }}" class="btn btn-secondary">Batal</a>
      <button type="submit" class="btn btn-primary">Simpan</button>
    </div>

  </form>
</div>

{{-- ===== JS Preview: hanya untuk foto ===== --}}
<script>
  (function(){
    const fImg = document.getElementById('f_img');
    const preview = document.getElementById('previewImg');
    if (!fImg || !preview) return;
    fImg.addEventListener('change', (e)=>{
      const file = e.target.files && e.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = ev => { preview.src = ev.target.result; };
      reader.readAsDataURL(file);
    });
  })();
</script>

<script>
  (function(){
    const bindPreview = (inputId, imgId) => {
      const input = document.getElementById(inputId);
      const img   = document.getElementById(imgId);
      if (!input || !img) return;
      input.addEventListener('change', (e)=>{
        const file = e.target.files && e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = ev => { img.src = ev.target.result; };
        reader.readAsDataURL(file);
      });
    };
    bindPreview('f_img', 'previewImg');
    bindPreview('f_cover', 'previewCover');
    bindPreview('f_content_img', 'previewContentImg');
  })();


(function(){
  const $ = s => document.querySelector(s);
  const alamat = $('#alamat_maps');
  const lat = $('#lat');
  const lng = $('#lng');
  const iframe = $('#mapIframe');
  const wrap = $('#mapWrap');

  function setPreviewByQuery(q, z=15){
    if(!iframe) return;
    if(q && q.trim()){
      iframe.src = 'https://www.google.com/maps?output=embed&q=' + encodeURIComponent(q) + '&z=' + z;
      wrap.style.display = 'block';
    } else {
      wrap.style.display = 'none';
    }
  }

  // Ambil koordinat dari link Google Maps di kolom alamat
  $('#btnParseLink')?.addEventListener('click', ()=>{
    const url = (alamat?.value || '').trim();
    if(!url){ alert('Isi dulu alamat / tautan.'); return; }

    // pola @-6.123,106.123
    let m = url.match(/@(-?\d+\.\d+),\s*(-?\d+\.\d+)/);
    if(!m){
      // pola ?q=-6.12,106.12
      m = url.match(/[?&]q=(-?\d+\.\d+),\s*(-?\d+\.\d+)/);
    }
    if(m){
      lat.value = m[1];
      lng.value = m[2];
      setPreviewByQuery(m[1] + ',' + m[2]);
    } else {
      alert('Tidak menemukan koordinat di tautan itu.\nCoba klik kanan di Maps -> Salin lintang & bujur.');
    }
  });

  // Pakai lat/lng untuk preview
  $('#btnUseCoords')?.addEventListener('click', ()=>{
    if(!lat.value || !lng.value){ alert('Isi lat & lng dulu.'); return; }
    setPreviewByQuery(lat.value + ',' + lng.value);
  });

  // Pakai alamat untuk preview
  $('#btnUseAddress')?.addEventListener('click', ()=>{
    if(!alamat.value){ alert('Isi alamat dulu.'); return; }
    setPreviewByQuery(alamat.value);
  });
})();

</script>

@endsection
