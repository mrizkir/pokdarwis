@props([
//   'subtitle' => 'UNCOVER PLACE',
  'title'    => 'POPULAR DESTINATION',
  'text'     => 'Fusce hic augue velit wisi quibusdam pariatur, iusto primis, nec nemo, rutrum. Vestibulum cumque laudantium. Sit ornare mollitia tenetur, aptent.',

  /**
   * items: array destinasi.
   * Tiap item: [
   *   'image'   => 'assets/images/img1.jpg' | 'https://...',
   *   'cat'     => 'ITALY',
   *   'catUrl'  => 'destination.html',
   *   'title'   => 'SAN MIGUEL',
   *   'titleUrl'=> 'package-detail.html',
   *   'desc'    => 'teks deskripsi',
   *   'rating'  => 5 (0..5),
   * ]
   */

  'items'    => [],
  'ctaHref'  => 'destination.html',
  'pdw' => null,
//   'ctaText'  => 'More Product',
])

@php
  // Demo default jika belum ada items
  if (empty($items)) {
    $items = [
      ['image'=>'assets/images/img1.jpg','cat'=>'ITALY','catUrl'=>'destination.html','title'=>'SAN MIGUEL','titleUrl'=>'package-detail.html','desc'=>'Fusce hic augue velit wisi ips quibusdam pariatur, iusto.','rating'=>5],
      ['image'=>'assets/images/img2.jpg','cat'=>'Dubai','catUrl'=>'destination.html','title'=>'BURJ KHALIFA','titleUrl'=>'package-detail.html','desc'=>'Fusce hic augue velit wisi ips quibusdam pariatur, iusto.','rating'=>5],
      ['image'=>'assets/images/img3.jpg','cat'=>'Japan','catUrl'=>'destination.html','title'=>'KYOTO TEMPLE','titleUrl'=>'package-detail.html','desc'=>'Fusce hic augue velit wisi ips quibusdam pariatur, iusto.','rating'=>5],
    ];
  }

  $toUrl = function ($path) {
    $isAbs = \Illuminate\Support\Str::startsWith($path, ['http://','https://','//']);
    return $isAbs ? $path : asset($path);
  };
@endphp

<section {{ $attributes->merge(['class' => 'home-destination']) }}>
  <div class="container">
    <div class="row">
                    <div class="col-lg-8">
                        <div class="section-heading">
                            <h2 class="section-title">BINTAN PRODUCTS</h2>
                            <p>
                                Discover a wide range of local products that showcase creativity, quality, and the spirit of the community. 
                                From everyday essentials to specialty items, each product is carefully made to bring value and authenticity 
                                to your experience.
                            </p>
                        </div>
                    </div>
                </div>
    {{-- <div class="row">
      <div class="col-lg-8 offset-lg-2 text-sm-center">
        <div class="section-heading">
          <h5 class="sub-title">{{ $subtitle }}</h5> 
          <h2 class="section-title">{{ $title }}</h2>
          <p>{{ $text }}</p>
        </div>
      </div>
    </div> --}}

      <div class="row">
@foreach($items as $it)
  @php
    $img     = $toUrl(data_get($it,'image','assets/images/img1.jpg'));
    $cat     = data_get($it,'cat','-');
    $catUrl  = data_get($it,'catUrl','#');
    $ttl     = data_get($it,'title','Untitled');
    $ttlUrl  = data_get($it,'titleUrl','#'); // tidak dipakai untuk kartu, tetap dipakai di modal button "Kunjungi" kalau mau
    $desc    = trim(strip_tags(data_get($it,'desc','')));
    $rating  = (float) data_get($it,'rating',5);
    $rWidth  = max(0, min(100, ($rating/5)*100));
    $mId     = 'prodModal-'.$loop->index;     // id modal unik
  @endphp

  @php
  $pdwForThis = data_get($it, 'pdw') ?: $pdw;
@endphp

  <div class="col-lg-4 col-md-6">
    <article class="destination-item position-relative" style="background-image:url('{{ $img }}')">

      <div class="destination-content position-relative">
        {{-- <div class="rating-start-wrap">
          <div class="rating-start"><span style="width: {{ $rWidth }}%"></span></div>
        </div> --}}

        <span class="cat-link"><a href="{{ $catUrl }}">{{ $cat }}</a></span>

        {{-- Judul: klik membuka modal --}}
        <h3 class="mb-2">
          <a href="#" class="text-decoration-none"
             data-bs-toggle="modal" data-bs-target="#{{ $mId }}">{{ $ttl }}</a>
        </h3>

        {{-- Deskripsi: clamp 2 baris --}}
        <p class="desc line-clamp-2 mb-0">{{ $desc }}</p>

        {{-- Fade lembut di bawah bubble --}}
        <span class="fade-tail"></span>
      </div>

      {{-- COVER: klik di mana pun pada card membuka modal --}}
      <button type="button" class="card-cover" aria-label="Buka detail {{ $ttl }}"
              data-bs-toggle="modal" data-bs-target="#{{ $mId }}"></button>
    </article>
  </div>

  {{-- Modal detail / full text --}}
  <div class="modal fade product-modal" id="{{ $mId }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0">
      <div class="modal-header border-0 pb-3">
        {{-- <h5 class="modal-title fw-bold">{{ $ttl }}</h5>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button> --}}
      </div>

      <div class="modal-body pt-0">
        <div class="row g-4 align-items-start">
          <div class="col-md-5">
            <figure class="prod-hero mb-0">
              <img src="{{ $img }}" alt="{{ $ttl }}" class="w-100 h-100 object-cover">
            </figure>
          </div>

          <div class="col-md-7">
            <div class="prod-eyebrow mb-1">{{ strtoupper($cat) }}</div>
            <h3 class="prod-title mb-3">{{ $ttl }}</h3>

            @if($desc)
              <p class="prod-desc mb-3">{{ $desc }}</p>
            @else
              <p class="text-muted fst-italic mb-3">Belum ada deskripsi.</p>
            @endif
          </div>
        </div>
      </div>

      {{-- === Footer: sosmed di kiri, action di kanan === --}}
      @php
        // Normalisasi WA: ambil digit saja, ganti 0 awal → 62
        $waLink = null;
        if (isset($pdwForThis) && !empty($pdwForThis->kontak)) {
          $digits = preg_replace('/\D+/', '', $pdwForThis->kontak);
          $waLink = preg_replace('/^0/', '62', $digits);
        }

        $hasSocial = isset($pdwForThis) && (
          $waLink || !empty($pdwForThis->facebook) || !empty($pdwForThis->twitter) ||
          !empty($pdwForThis->instagram) || !empty($pdwForThis->website)
        );
      @endphp

      <div class="modal-footer border-0 pt-0">
        @if($hasSocial)
          <div class="socialgroup bookhere-socials me-auto">
            <ul class="list-unstyled d-flex align-items-center gap-2 mb-0">
              @if($waLink)
                <li>
                  <a href="https://wa.me/{{ $waLink }}" target="_blank" rel="noopener"
                      aria-label="WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                  </a>
                </li>
              @endif
              @if(!empty($pdwForThis->facebook))
                <li>
                  <a href="{{ $pdwForThis->facebook }}" target="_blank" rel="noopener"
                   aria-label="Facebook">
                    <i class="fab fa-facebook-f"></i>
                  </a>
                </li>
              @endif
              @if(!empty($pdwForThis->instagram))
                <li>
                  <a href="{{ $pdwForThis->instagram }}" target="_blank" rel="noopener"
                      aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                  </a>
                </li>
              @endif
              @if(!empty($pdwForThis->twitter))
                <li>
                  <a href="{{ $pdwForThis->twitter }}" target="_blank" rel="noopener"
                      aria-label="X">
                    <i class="fab fa-x-twitter"></i>
                  </a>
                </li>
              @endif
              @if(!empty($pdwForThis->website))
                <li>
                  <a href="{{ $pdwForThis->website }}" target="_blank" rel="noopener"
                     aria-label="Website">
                    <i class="fas fa-globe"></i>
                  </a>
                </li>
              @endif
            </ul>
          </div>
        @endif

        @if($ttlUrl !== '#')
          <a href="{{ $ttlUrl }}" class="btn btn-primary">
            <i class="fa-solid fa-arrow-up-right-from-square me-2"></i>Kunjungi
          </a>
        @endif
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endforeach
</div>
      <div class="section-btn-wrap text-center">
        {{-- <a href="{{ $ctaHref }}" class="round-btn">{{ $ctaText }}</a> --}}
      </div>
  </div>
</section>

@push('styles')
<style>
  .line-clamp-2{
  display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;
  overflow:hidden;
}

/* fade di bawah bubble biar potongannya halus */
.destination-content{ position:relative; }
.destination-content .fade-tail{
  position:absolute; left:0; right:0; bottom:0; height:24px;
  background:linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,.96));
  border-bottom-left-radius:1rem; border-bottom-right-radius:1rem;
  pointer-events:none; content:""; display:block;
}

/* tombol transparan yang menutupi seluruh kartu → semua titik klik buka modal */
.card-cover{
  position:absolute; inset:0; border:0; background:transparent; cursor:pointer;
  z-index:4; /* di atas konten lain */
}

/* jaga elemen lain tetap di bawah cover (tidak perlu diklik terpisah) */
.destination-content .rating-start-wrap,
.destination-content .cat-link,
.destination-content h3,
.destination-content .desc{ position:relative; z-index:3; }

/* Modal look */
product-modal .modal-dialog{ max-width: 920px; }
.product-modal .modal-content{
  border-radius:18px;
  background: linear-gradient(180deg,#ffffff 0%,#fcfdff 100%);
  box-shadow:0 1rem 2rem rgba(0,0,0,.14);
}
.product-modal .modal-header{ 
  padding: 20px 24px 0 24px; 
  border: 0;
  position: relative; 
  z-index: 2;
}
.product-modal .modal-body{ 
  padding: 20px 24px 24px 24px; 
}
.product-modal .modal-footer{ 
  padding: 16px 24px 24px; 
  gap: 8px; 
  border: 0; 
}

/* Close button feel (kalau header+btn-close diaktifkan) */
.product-modal .btn-close{
  width:38px; height:38px; border-radius:50%;
  background-color: rgba(15,23,42,.06);
  background-size: 12px 12px;
  opacity: 1;
  transition: transform .18s ease, background-color .18s ease;
}
.product-modal .btn-close:hover{
  transform: rotate(90deg) scale(1.05);
  background-color: rgba(15,23,42,.12);
}

/* Hero image */
.prod-hero{
  border-radius:16px; 
  overflow:hidden; 
  aspect-ratio:4/3; 
  background:#f6f7f9;
  box-shadow:0 .6rem 1rem rgba(0,0,0,.06);
}
.object-cover{ object-fit:cover; }
.prod-hero img{ transform: scale(1); transition: transform .35s ease; }
.product-modal .modal-content:hover .prod-hero img{ transform: scale(1.02); }

/* Typography */
.prod-eyebrow{
  font-size:.75rem; letter-spacing:.08em; font-weight:700;
  color:#1f6feb; text-transform:uppercase;
}
.prod-title{ font-weight:800; letter-spacing:.2px; color:#0f172a; }
.prod-desc{ color:#475569; line-height:1.7; }

/* Buttons */
.product-modal .btn-primary{
  border-radius:10px; border:0;
  padding:.625rem 1rem;
  background: linear-gradient(180deg,#2f7ff2 0%, #1f6feb 100%);
  box-shadow:0 .5rem 1rem rgba(31,111,235,.15);
  transition: transform .15s ease, filter .15s ease;
}
.product-modal .btn-primary:hover{ transform: translateY(-1px); filter: brightness(.98); }
.product-modal .btn-light{
  border-radius:10px; border:1px solid #e6eaf2;
}

/* Optional: sosial list (kalau ada .bookhere-socials) */


/* Mobile tweak */
@media (max-width:575.98px){
  .product-modal .modal-body{ padding: 8px 16px 16px; }
  .product-modal .modal-header{ padding: 12px 16px 0; }
  .prod-hero{ aspect-ratio:3/2; }
}
</style>
</style>
@endpush

<script>
document.addEventListener('DOMContentLoaded', function () {
  // Sembunyikan tombol jika paragraf tidak overflow (tidak benar2 ter-clamp)
  document.querySelectorAll('.see-more[data-desc]').forEach(btn => {
    const p = document.querySelector(btn.getAttribute('data-desc'));
    if (!p) { btn.classList.add('d-none'); return; }
    // Beri waktu 1 frame untuk layout settle
    requestAnimationFrame(() => {
      if (p.scrollHeight <= p.clientHeight + 1) {
        btn.classList.add('d-none');
      }
    });
  });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>


{{-- @props([
//   'subtitle' => 'UNCOVER PLACE',
  'title'    => 'POPULAR DESTINATION',
  'text'     => 'Fusce hic augue velit wisi quibusdam pariatur, iusto primis, nec nemo, rutrum. Vestibulum cumque laudantium. Sit ornare mollitia tenetur, aptent.',

  /**
   * items: array destinasi.
   * Tiap item: [
   *   'image'   => 'assets/images/img1.jpg' | 'https://...',
   *   'cat'     => 'ITALY',
   *   'catUrl'  => 'destination.html',
   *   'title'   => 'SAN MIGUEL',
   *   'titleUrl'=> 'package-detail.html',
   *   'desc'    => 'teks deskripsi',
   *   'rating'  => 5 (0..5),
   * ]
   */

  'items'    => [],
  'ctaHref'  => 'destination.html',
//   'ctaText'  => 'More Product',
])

@php
  // Demo default jika belum ada items
  if (empty($items)) {
    $items = [
      ['image'=>'assets/images/img1.jpg','cat'=>'ITALY','catUrl'=>'destination.html','title'=>'SAN MIGUEL','titleUrl'=>'package-detail.html','desc'=>'Fusce hic augue velit wisi ips quibusdam pariatur, iusto.','rating'=>5],
      ['image'=>'assets/images/img2.jpg','cat'=>'Dubai','catUrl'=>'destination.html','title'=>'BURJ KHALIFA','titleUrl'=>'package-detail.html','desc'=>'Fusce hic augue velit wisi ips quibusdam pariatur, iusto.','rating'=>5],
      ['image'=>'assets/images/img3.jpg','cat'=>'Japan','catUrl'=>'destination.html','title'=>'KYOTO TEMPLE','titleUrl'=>'package-detail.html','desc'=>'Fusce hic augue velit wisi ips quibusdam pariatur, iusto.','rating'=>5],
    ];
  }

  $toUrl = function ($path) {
    $isAbs = \Illuminate\Support\Str::startsWith($path, ['http://','https://','//']);
    return $isAbs ? $path : asset($path);
  };
@endphp

<section {{ $attributes->merge(['class' => 'home-destination']) }}>
  <div class="container">
    <div class="row">
                     <div class="col-lg-8 offset-lg-2 text-sm-center">
                        <div class="section-heading">
                           <h2 class="section-title">BINTAN PRODUCTS</h2>
                           <p>Discover a wide range of local products that showcase creativity, quality, and the spirit of the community. From everyday essentials to specialty items, each product is carefully made to bring value and authenticity to your experience.</p>
                        </div>
                     </div>
                  </div>

      <div class="row">
        @foreach($items as $it)
          @php
            $img     = $toUrl(data_get($it,'image','assets/images/img1.jpg'));
            $cat     = data_get($it,'cat','-');
            $catUrl  = data_get($it,'catUrl','#');
            $ttl     = data_get($it,'title','Untitled');
            $ttlUrl  = data_get($it,'titleUrl','#');
            $desc    = data_get($it,'desc','');
            $rating  = (float) data_get($it,'rating',5);
            $rWidth  = max(0, min(100, ($rating/5)*100));
          @endphp

          <div class="col-lg-4 col-md-6">
            <article class="destination-item" style="background-image: url('{{ $img }}');">

              <div class="destination-content">
                <div class="rating-start-wrap">
                  <div class="rating-start">
                    <span style="width: {{ $rWidth }}%"></span>
                  </div>
                </div>

                <span class="cat-link">
                  <a href="{{ $catUrl }}">{{ $cat }}</a>
                </span>

                <h3>
                  <a href="{{ $ttlUrl }}">{{ $ttl }}</a>
                </h3>

                <p>{{ $desc }}</p>
              </div>
            </article>
          </div>
        @endforeach
      </div>

      <div class="section-btn-wrap text-center">
      </div>
  </div>
</section> --}}
