{{-- resources/views/pokdarwis.blade.php --}}
@php
  $counters = [
    ['value' => 80,  'suffix' => 'K+', 'label' => 'Kunjungan Wisatawan'],
    ['value' => 18,  'suffix' => '+',  'label' => 'Total Destinations'],
    ['value' => 220, 'suffix' => '+',  'label' => 'Reviews'],
  ];
@endphp

@extends('layouts.pokdarwisLayout')
@section('title', $pokdarwis->name_pokdarwis)

@section('banner')
  <!-- ***Inner Banner html start form here*** -->
  <x-banner2 :pokdarwis="$pokdarwis" />
  <!-- ***Inner Banner html end here*** -->
@endsection

@section('main')      
  <section class="inner-about-wrap py-5">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-8">
          <div class="about-content">
            <figure class="about-image mb-4">
            @php
              $imgPath = $pokdarwis->content_img 
                ? asset('storage/'.$pokdarwis->content_img)   // file upload di storage/app/public
                : asset('assets/images/default.png'); // fallback
            @endphp

            <img src="{{ $imgPath }}" alt="{{ $pokdarwis->name_pokdarwis }}" class="img-fluid">

            <div class="about-image-content">
              <h3>WE ARE BEST FOR TOURS & TRAVEL !</h3>
              {{-- <h3>{{ $pokdarwis->name_pokdarwis }}</h3> --}}
            </div>
          </figure>
            <h2 class="title-outline">{{ $pokdarwis->name_pokdarwis }}</h2>
            <p>{{ $pokdarwis->deskripsi2 }}</p>
            </div>
        </div>

        {{-- Konten Side Bar --}}
        <div class="col-lg-4">
          <div class="icon-box mb-3">
            <div class="box-icon"><i class="fas fa-umbrella-beach"></i></div>
            <div class="icon-box-content">
              <h3>COMFORTABLE STAYS</h3>
              <p>Affordable and cozy accommodations to make your journey worry-free.</p>
            </div>
          </div>
          <div class="icon-box mb-3">
            <div class="box-icon"><i class="fas fa-user-tag"></i></div>
            <div class="icon-box-content">
              <h3>BEST TOUR GUIDES</h3>
              <p>Friendly and knowledgeable guides to ensure a rich and authentic experience.</p>
            </div>
          </div>
          <div class="icon-box">
            <div class="box-icon"><i class="fas fa-headset"></i></div>
            <div class="icon-box-content">
              <h3>24/7 SUPPORT</h3>
              <p>Our team is always ready to assist you anytime, anywhere.</p>
            </div>
          </div>
        </div>
        {{-- Konten Side Bar --}}
        
        {{-- Wisata Card --}}
{{-- 
        <x-video-wisata
            :bg="$coverUrl"
            :video="$videoUrl"
            title="ARE YOU READY TO TRAVEL? REMEMBER US !!"
            :description="$pokdarwis->deskripsi"
        /> --}}


        <x-video-wisata
            :bg="$pokdarwis->cover_img 
                    ? asset('storage/'.$pokdarwis->cover_img) 
                    : asset('assets/images/default-cover.jpg')" 
            :video="$pokdarwis->content_video 
                    ? (\Illuminate\Support\Str::startsWith($pokdarwis->content_video, ['http://','https://','//']) 
                        ? $pokdarwis->content_video 
                        : asset('storage/'.$pokdarwis->content_video))
                    : 'https://www.youtube.com/watch?v=2OYar8OHEOU'" 
            title="ARE YOU READY TO TRAVEL? REMEMBER US !!"
        />
        
        <x-counter :items="$counterItems" />

        <div class="container">
          
                  @if($pakets->count() > 0)
                  <div class="row">
                      <div class="col-lg-8">
                          <div class="section-heading">
                              <h2 class="section-title">OUR PACKAGES</h2>
                              <p>
                                  Discover journeys crafted to inspire, combining relaxation, adventure, and authentic local culture. 
                                  Each package is designed to give you lasting memories with every experience.
                              </p>
                          </div>
                      </div>
                  </div>

                  <div class="row">
                      @foreach ($pakets as $p)  
                          <x-wisata-card
                            :title="$p->nama_paket"
                            :image="$p->cover_url ?? $p->img"
                            :detail-link="route('paket.show', $p)"
                            :description="\Illuminate\Support\Str::limit(strip_tags($p->deskripsi ?? ''), 120)"
                            :duration="$p->waktu_penginapan"
                            :pax="$p->pax"
                            :location="$p->lokasi"
                            :currency="$p->currency"
                            :price="$p->harga"
                            :book-link="route('paket.show', $p)"          {{-- tombol tetap ke detail/booking --}}
                            :increment-url="route('paket.book.intent', $p)" {{-- <-- increment visit --}}
                            increment-mode="beacon"                        {{-- 'beacon' = mulus; 'ajax' = nunggu lalu redirect --}}
                        />
                      @endforeach
                  </div>
                  @endif
                
                {{-- Pagination --}}
                @if ($pakets->hasPages())
                <div class="post-navigation-wrap">
                  <nav aria-label="Pagination">
                    <ul class="pagination">

                      {{-- Prev --}}
                      @php $prevUrl = $pakets->previousPageUrl(); @endphp
                      <li class="{{ $pakets->onFirstPage() ? 'disabled' : '' }}">
                        <a
                          href="{{ $pakets->onFirstPage() ? 'javascript:void(0)' : $prevUrl }}"
                          aria-label="Halaman sebelumnya"
                          aria-disabled="{{ $pakets->onFirstPage() ? 'true' : 'false' }}"
                          rel="prev"
                          title="Sebelumnya"
                        >
                          <i class="fas fa-arrow-left" aria-hidden="true"></i>
                          <span class="sr-only">Prev</span>
                        </a>
                      </li>

                      {{-- Numbered pages with ellipses --}}
                      @php
                        $current = $pakets->currentPage();
                        $last    = $pakets->lastPage();
                        $start   = max($current - 1, 2);      // blok tengah mulai dari 2
                        $end     = min($current + 1, $last);  // blok tengah berakhir di last
                      @endphp

                      {{-- Page 1 --}}
                      <li class="{{ $current === 1 ? 'active' : '' }}">
                        <a href="{{ $pakets->url(1) }}" aria-current="{{ $current === 1 ? 'page' : 'false' }}">1</a>
                      </li>

                      {{-- Left ellipses --}}
                      @if ($start > 2)
                        <li class="ellipsis" role="separator" aria-hidden="true"><a href="javascript:void(0)">…</a></li>
                      @endif

                      {{-- Middle: current ±1 --}}
                      @for ($page = $start; $page <= $end; $page++)
                        @if ($page >= 2 && $page <= $last - 1)
                          <li class="{{ $page === $current ? 'active' : '' }}">
                            <a href="{{ $pakets->url($page) }}" aria-current="{{ $page === $current ? 'page' : 'false' }}">{{ $page }}</a>
                          </li>
                        @endif
                      @endfor

                      {{-- Right ellipses --}}
                      @if ($end < $last - 1)
                        <li class="ellipsis" role="separator" aria-hidden="true"><a href="javascript:void(0)">…</a></li>
                      @endif

                      {{-- Last page (jika >1) --}}
                      @if ($last > 1)
                        <li class="{{ $current === $last ? 'active' : '' }}">
                          <a href="{{ $pakets->url($last) }}" aria-current="{{ $current === $last ? 'page' : 'false' }}">{{ $last }}</a>
                        </li>
                      @endif

                      {{-- Next --}}
                      @php $nextUrl = $pakets->nextPageUrl(); @endphp
                      <li class="{{ $pakets->hasMorePages() ? '' : 'disabled' }}">
                        <a
                          href="{{ $pakets->hasMorePages() ? $nextUrl : 'javascript:void(0)' }}"
                          aria-label="Halaman berikutnya"
                          aria-disabled="{{ $pakets->hasMorePages() ? 'false' : 'true' }}"
                          rel="next"
                          title="Berikutnya"
                        >
                          <i class="fas fa-arrow-right" aria-hidden="true"></i>
                          <span class="sr-only">Next</span>
                        </a>
                      </li>

                    </ul>
                  </nav>
                </div>
              @endif
                
                <section class="single-package mb-5">
                </section>
                {{-- @if(method_exists($pakets, 'links'))
                  <div class="mt-5">
                    {{ $pakets->links() }}
                  </div>
                @endif --}}
                
                @if($items->count() > 0)
                <div class="row">
                    <div class="col-lg-8">
                        <div class="section-heading">
                            <h2 class="section-title">OUR PRODUCTS</h2>
                            <p>
                                Discover a wide range of local products that showcase creativity, quality, and the spirit of the community. 
                                From everyday essentials to specialty items, each product is carefully made to bring value and authenticity 
                                to your experience.
                            </p>
                        </div>
                    </div>
                </div>

                <x-product-card
                    class="pt-0 mt-2"      
                    subtitle="UNCOVER PLACE"
                    title="POPULAR PRODUCT"
                    text="Produk-produk pilihan dari berbagai Pokdarwis."
                    :items="$items"
                    :pdw="$pokdarwis"
                />
                @endif

                <div class="col-lg-8 offset-lg-2 text-sm-center">
                  <div class="section-heading">
                    <h2 class="section-title">PHOTO'S GALLERY</h2>
                    <p>Take a closer look through our photo collection…</p>
                  </div>
                </div>

                <x-gallery-card2 :items="$galleryPhotos" gallery="pokdarwis-{{ $pokdarwis->id }}" class="my-5" />
                <div class="grid blog-inner row">
                
      </div>
      

      @auth
  @if(auth()->user()->role === 'wisatawan') {{-- sesuaikan: nama role kamu --}}
    <section class="my-5">
      <div class="container">
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card p-4">
          <h4 class="mb-3">Tulis Review</h4>
          <form action="{{ route('pokdarwis.reviews.store', $pokdarwis) }}" method="POST">
            @csrf

            <div class="mb-3">
              <label class="form-label">Rating <span class="text-danger">*</span></label>
              <select name="rating" class="form-select" style="max-width:140px" required>
                <option value="">Pilih…</option>
                @for($i=5;$i>=1;$i--)
                  <option value="{{ $i }}">{{ $i }} ★</option>
                @endfor
              </select>
              @error('rating') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Ulasan</label>
              <textarea name="text" rows="4" class="form-control" placeholder="Ceritakan pengalamanmu… (opsional)"></textarea>
              @error('text') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary">Kirim</button>
          </form>
        </div>
      </div>
    </section>
  @endif
@else
  <section class="my-5">
    <div class="container">
      <div class="alert alert-info">
        Silakan <a href="{{ url('/login') }}">login</a> sebagai <strong>wisatawan</strong> untuk menulis review.
      </div>
    </div>
  </section>
@endauth


        <x-review-card :reviews="$reviews"
          {{-- subtitle="CLIENT'S REVIEWS" --}}
          title="TRAVELLER'S TESTIMONIAL"
          intro="What Tourists Say About {{ $pokdarwis->name_pokdarwis }}"
        />
        
    </div>
  </section>
@endsection
