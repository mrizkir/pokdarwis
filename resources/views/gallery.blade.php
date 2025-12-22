@extends('layouts.galleryLayout')

@section('banner')
  <div class="inner-banner-wrap">
    <div class="inner-baner-container" style="background-image:url('{{ asset('assets/images/bintantourism6.jpg') }}');">
      <div class="container">
        <div class="inner-banner-content text-center text-white">
          <h1 class="page-title">Gallery</h1>
          <p class="mb-0">Beautiful Bintan</p>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('main')

  {{-- ========== FILTERS (LETAMKAN DI ATAS GRID) ========== --}}
  <div class="container mt-4">
    <form method="GET" action="{{ route('gallery') }}" class="row g-2 align-items-end">
      <div class="col-12 col-md-4">
        <label class="form-label mb-1">Tours</label>
        <select name="pokdarwis_id" class="form-select" onchange="this.form.submit()">
          <option value="">All</option>
          @foreach($pokdarwisMenu as $pd)
            <option value="{{ $pd->id }}" @selected($filters['pokdarwis_id']==$pd->id)>
              {{ $pd->name_pokdarwis }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-6 col-md-2">
        <label class="form-label mb-1">Type</label>
        <select name="tipe" class="form-select" onchange="this.form.submit()">
          <option value="">All</option>
          <option value="foto"  @selected($filters['tipe']=='foto')>Photo</option>
          <option value="video" @selected($filters['tipe']=='video')>Video</option>
        </select>
      </div>

      {{-- <div class="col-6 col-md-2">
        <label class="form-label mb-1">Page</label>
        <select name="per_page" class="form-select" onchange="this.form.submit()">
          @foreach([12,24,36,48,60] as $pp)
            <option value="{{ $pp }}" @selected($filters['per_page']==$pp)>{{ $pp }}</option>
          @endforeach
        </select>
      </div> --}}

    </form>
  </div>

  {{-- ========== GRID GALLERY ========== --}}
  <x-gallery-card :items="$items" gallery="global-gallery" class="my-4" />

  {{-- ========== PAGINATION (LETakkan DI BAWAH GRID) ========== --}}
  @if(isset($page) && $page->hasPages())
    <div class="container mb-5">
      {{ $page->onEachSide(1)->links() }}
    </div>
  @endif

@endsection
