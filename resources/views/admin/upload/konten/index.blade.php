@extends('layouts.app-backend')
@section('page-title','Upload • Konten')


@section('page-header')
  <nav class="admin-breadcrumb" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="#">Upload</a></li>
      <li class="breadcrumb-item active" aria-current="page">Konten</li>
    </ol>
  </nav>

  <div class="mb-4"></div>

  <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
      <h3 class="mb-0">Konten Saya</h3>
      <span class="badge bg-light text-secondary border">{{ number_format($items->total()) }} item</span>
    </div>

    <div class="d-flex gap-2">
      <button class="btn btn-outline-secondary" onclick="location.reload()">
        <i class="fa-solid fa-rotate"></i>
      </button>
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdd">
        <i class="fas fa-plus me-2"></i>Add Konten
      </button>
    </div>
  </div>
@endsection

@section('main')
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if($items->count() === 0)
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center py-5">
        <img src="{{ asset('assets/images/site-logo.png') }}" class="mb-3 rounded-3" style="width:200px;height:120px;object-fit:cover;opacity:.3">
        <h5 class="mb-1">Belum ada konten</h5>
        <p class="text-muted mb-3">Klik tombol <b>“Add Konten”</b> untuk menambahkan.</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdd">
          <i class="fa-solid fa-plus me-1"></i> Add Konten
        </button>
      </div>
    </div>
  @else
    <div class="row g-4">
      @foreach($items as $k)
        <div class="col-12 col-sm-6 col-lg-4">
          <div class="card h-100 border-0 shadow-sm product-card">
            <div class="position-relative rounded-top overflow-hidden" style="aspect-ratio:16/10;background:#f7f7f9">
  @if($k->tipe_konten === 'foto')
    <img src="{{ $k->url }}" alt="{{ $k->judul_konten }}" class="w-100 h-100" style="object-fit:cover;">
  @else
    @php
      // Tentukan sumber path/url video
      $filePath = $k->file_path ?? $k->url;
      $videoId  = null;

      // Ekstrak ID YouTube
      if (\Illuminate\Support\Str::contains($filePath, 'youtube.com/watch')) {
          parse_str(parse_url($filePath, PHP_URL_QUERY), $q);
          $videoId = $q['v'] ?? null;
      } elseif (\Illuminate\Support\Str::contains($filePath, 'youtu.be/')) {
          $videoId = trim(basename(parse_url($filePath, PHP_URL_PATH)));
      }
    @endphp

    @if($videoId)
      {{-- Embed YouTube --}}
      <iframe
        class="w-100 h-100" style="border:0;object-fit:cover;"
        src="https://www.youtube.com/embed/{{ $videoId }}?rel=0&modestbranding=1"
        loading="lazy"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
        allowfullscreen>
      </iframe>
    @else
      {{-- File video lokal/mp4 --}}
      <video class="w-100 h-100" style="object-fit:cover;" controls controlsList="nodownload noplaybackrate">
        <source src="{{ $k->url }}" type="video/mp4">
      </video>
    @endif
  @endif

  <span class="price-chip shadow-sm text-uppercase small">
    {{ $k->tipe_konten }} • {{ $k->konten }}
  </span>
</div>
            <div class="card-body">
              <h5 class="card-title mb-1 text-truncate" title="{{ $k->judul_konten }}">{{ $k->judul_konten }}</h5>
              @if($k->product)
                <div class="text-muted small">Produk: {{ $k->product->name_product }}</div>
              @endif
            </div>
            <div class="card-footer bg-white border-0 pt-0 pb-3 px-3">
              <div class="d-flex gap-2">
                <button
                  class="btn btn-outline-primary btn-sm flex-fill btn-edit"
                  data-id="{{ $k->id }}"
                  data-judul="{{ $k->judul_konten }}"
                  data-tipe="{{ $k->tipe_konten }}"
                  data-konten="{{ $k->konten }}"
                  data-product="{{ $k->product_id }}"
                  data-file="{{ $k->url }}"
                  data-update-url="{{ route('pokdarwis.konten.update',$k->id) }}"
                  data-bs-toggle="modal" data-bs-target="#modalEdit">
                  <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                </button>

                <form action="{{ route('pokdarwis.konten.destroy',$k->id) }}" method="POST" class="flex-fill"
                      onsubmit="return confirm('Hapus konten ini?');">
                  @csrf @method('DELETE')
                  <button class="btn btn-outline-danger btn-sm w-100">
                    <i class="fa-solid fa-trash me-1"></i> Hapus
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-4">{{ $items->onEachSide(1)->links() }}</div>
  @endif

  {{-- MODAL ADD --}}
  <div class="modal fade has-scroll" id="modalAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa-solid fa-image me-2"></i>Tambah Konten</h5>

          <button type="button" 
                  class="btn btn-link text-secondary ms-2 p-0" 
                  data-bs-toggle="modal" 
                  data-bs-target="#videoGuideModal" 
                  title="Lihat panduan upload">
            <i class="fa-regular fa-circle-question fa-lg"></i>
          </button>

          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="{{ route('pokdarwis.konten.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-8">
                <label class="form-label">Judul <span class="text-danger">*</span></label>
                <input type="text" name="judul_konten" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Konten <span class="text-danger">*</span></label>
                <select name="konten" class="form-select" id="addKontenSelect">
                  <option value="produk">Produk</option>
                  <option value="wisata">Wisata</option>
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label">Tipe <span class="text-danger">*</span></label>
                <select name="tipe_konten" class="form-select" id="addTipeSelect">
                  <option value="foto">Foto</option>
                  <option value="video">Video</option>
                </select>
              </div>

              <div class="col-md-8" id="addProductWrap">
                <label class="form-label">Produk (opsional)</label>
                <select name="product_id" class="form-select">
                  <option value="">— Pilih Produk —</option>
                  @foreach($products as $p)
                    <option value="{{ $p->id }}">{{ $p->name_product }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-6" id="addFileWrap">
                <label class="form-label">File (Gambar/Video)</label>
                <input type="file" name="file" class="form-control" accept="image/*,video/mp4" onchange="previewAdd(this)">
              </div>

              <div class="col-md-6 d-none" id="addVideoUrlWrap">
                <label class="form-label">URL Video (YouTube/Vimeo)</label>
                <input type="url" name="video_url" class="form-control" placeholder="https://…">
              </div>

              <div class="col-12">
                <label class="form-label d-block">Preview</label>
                <div class="preview-box d-flex align-items-center justify-content-center overflow-hidden">
                  <img id="addPreview" src="{{ asset('assets/images/site-logo.png') }}" alt="">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Video Guide --}}
<div class="modal fade" id="videoGuideModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

      <div class="modal-header bg-light border-bottom">
        <h5 class="modal-title d-flex align-items-center text-primary mb-0">
          <i class="fa-regular fa-circle-question me-2"></i>
          Panduan Upload Paket
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body bg-dark p-0 position-relative">
        <video id="guideVideo" 
               controls 
               preload="metadata" 
               class="w-100" 
               style="border-radius:0 0 .5rem .5rem; max-height:70vh; object-fit:cover;">
          <source src="{{ asset('assets/video/panduan-upload-konten.mp4') }}" type="video/mp4">
          Browser kamu tidak mendukung pemutaran video.
        </video>
      </div>

      <div class="modal-footer bg-light text-muted small justify-content-center">
        <i class="fa-solid fa-info-circle me-1"></i>
        Klik tombol <strong><i class="fa-regular fa-circle-question fa-lg"></i></strong> kapan pun untuk melihat panduan.
      </div>

    </div>
  </div>
</div>


  {{-- MODAL EDIT --}}
  <div class="modal fade has-scroll" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header">
          <h5 class="modal-title">Edit Konten</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="formEdit" action="#" method="POST" enctype="multipart/form-data">
          @csrf @method('PUT')
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-8">
                <label class="form-label">Judul</label>
                <input type="text" name="judul_konten" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Konten</label>
                <select name="konten" class="form-select" id="editKontenSelect">
                  <option value="produk">Produk</option>
                  <option value="wisata">Wisata</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Tipe</label>
                <select name="tipe_konten" class="form-select" id="editTipeSelect">
                  <option value="foto">Foto</option>
                  <option value="video">Video</option>
                </select>
              </div>
              <div class="col-md-8" id="editProductWrap">
                <label class="form-label">Produk (opsional)</label>
                <select name="product_id" class="form-select">
                  <option value="">— Pilih Produk —</option>
                  @foreach($products as $p)
                    <option value="{{ $p->id }}">{{ $p->name_product }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6" id="editFileWrap">
                <label class="form-label">File (Gambar/Video)</label>
                <input type="file" name="file" class="form-control" accept="image/*,video/mp4" onchange="previewEdit(this)">
              </div>
              <div class="col-md-6 d-none" id="editVideoUrlWrap">
                <label class="form-label">URL Video</label>
                <input type="url" name="video_url" class="form-control" placeholder="https://…">
              </div>
              <div class="col-12">
                <label class="form-label d-block">Preview</label>
                <div class="preview-box d-flex align-items-center justify-content-center overflow-hidden">
                  <img id="editPreview" src="{{ asset('assets/images/site-logo.png') }}" alt="">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button class="btn btn-primary">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('styles')
<style>
  .product-card{ transition:.2s ease }
  .product-card:hover{ transform: translateY(-2px); box-shadow:0 .5rem 1rem rgba(0,0,0,.08)!important }
  .price-chip{ position:absolute; right:.75rem; bottom:.75rem; background:#fff; border:1px solid rgba(0,0,0,.06); padding:.3rem .55rem; border-radius:.6rem; font-weight:600; font-size:.75rem }
  .has-scroll .modal-body{ max-height:calc(100vh - 200px); overflow-y:auto }
  .preview-box{ height:220px; background:#f7f7f9; border-radius:.5rem }
  .preview-box img{ max-height:100%; max-width:100%; object-fit:contain; display:block; margin:0 auto }
</style>
@endpush

@push('scripts')
<script>
  // toggle antar file vs url saat tipe=video
  function toggleAddInputs(){
    const tipe = document.getElementById('addTipeSelect').value;
    document.getElementById('addFileWrap').classList.toggle('d-none', tipe==='video' && !!document.querySelector('[name="video_url"]').value);
    document.getElementById('addVideoUrlWrap').classList.toggle('d-none', tipe!=='video');
  }
  function toggleEditInputs(){
    const tipe = document.getElementById('editTipeSelect').value;
    document.getElementById('editFileWrap').classList.toggle('d-none', false);
    document.getElementById('editVideoUrlWrap').classList.toggle('d-none', tipe!=='video');
  }
  document.getElementById('addTipeSelect')?.addEventListener('change', toggleAddInputs);
  document.getElementById('editTipeSelect')?.addEventListener('change', toggleEditInputs);

  function previewAdd(input){
    const t = document.getElementById('addPreview');
    if (input.files && input.files[0]) {
      const r = new FileReader();
      r.onload = e => t.src = e.target.result; r.readAsDataURL(input.files[0]);
    }
  }
  function previewEdit(input){
    const t = document.getElementById('editPreview');
    if (input.files && input.files[0]) {
      const r = new FileReader();
      r.onload = e => t.src = e.target.result; r.readAsDataURL(input.files[0]);
    }
  }

  // pasang data saat Edit
  document.querySelectorAll('.btn-edit').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const f = document.getElementById('formEdit');
      f.action = btn.dataset.updateUrl;
      f.querySelector('[name="judul_konten"]').value = btn.dataset.judul||'';
      f.querySelector('[name="konten"]').value       = btn.dataset.konten||'produk';
      f.querySelector('[name="tipe_konten"]').value  = btn.dataset.tipe||'foto';
      f.querySelector('[name="product_id"]').value   = btn.dataset.product||'';
      document.getElementById('editPreview').src     = btn.dataset.file || "{{ asset('assets/images/site-logo.png') }}";
      toggleEditInputs();
    });
  });

  // initial
  toggleAddInputs();

</script>
<script>
  const modal = document.getElementById('videoGuideModal');
  const video = document.getElementById('guideVideo');

  modal?.addEventListener('hidden.bs.modal', () => {
    if (video) {
      video.pause();
      video.currentTime = 0;
    }
  });
</script>

@endpush
