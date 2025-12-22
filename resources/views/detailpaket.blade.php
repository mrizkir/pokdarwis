@extends('layouts.detailPaketLayout')
@section('title', $paket->nama_paket)


@section('banner')
    <x-banner2 :pokdarwis="$paket->pokdarwis"> 
    
    </x-banner2>
@endsection

@section('main')
<div class="inner-package-detail-wrap">
  <div class="container">
    <div class="row">
      <div class="col-lg-8 primary right-sidebar">

        <div class="single-packge-wrap">
          {{-- Head: judul + harga --}}
          <div class="single-package-head d-flex align-items-center">
            <div class="package-title">
              <h2>{{ $paket->nama_paket }}</h2>
              <div class="rating-start-wrap">
                <div class="rating-start">
                  {{-- kalau belum ada rating, pakai 80% sebagai placeholder --}}
                  <span style="width: 80%"></span>
                </div>
              </div>
            </div>
            <div class="package-price">
              <h6 class="price-list">
                <span>{{ $paket->currency }} {{ number_format((float)$paket->harga, 0, ',', '.') }}</span>
                {{-- / per person --}}
              </h6>
            </div>
          </div>

          {{-- Meta: durasi, pax, kategori(?), lokasi --}}
          <div class="package-meta">
            <ul>
              <li><i class="fas fa-clock"></i> {{ $paket->waktu_penginapan }}</li>
              <li><i class="fas fa-user-friends"></i> pax: {{ $paket->pax }}</li>
              {{-- <li><i class="fas fa-swimmer"></i> Category : {{ $paket->kategori ?? '—' }}</li> --}}
              <li><i class="fas fa-map-marker-alt"></i> {{ $paket->lokasi }}</li>
            </ul>
          </div>

          {{-- Gambar utama --}}
          <figure class="single-package-images">
            <img src="{{ $paket->cover_url }}" alt="{{ $paket->nama_paket }}">
          </figure>
<hr>
          <div class="package-content-detail">
            {{-- Overview / Deskripsi --}}
            <article class="package-overview">
              <h3>OVERVIEW :</h3>
              @if(filled($paket->deskripsi))
                <p>{!! nl2br(e($paket->deskripsi)) !!}</p>
              @else
                <p class="text-muted">Belum ada deskripsi.</p>
              @endif
            </article>

            {{-- Include & Exclude dari tabel paket_fasilitas --}}
            {{-- @if($paket->fasilitasInclude->isNotEmpty() || $paket->fasilitasExclude->isNotEmpty()) --}}
            @if($paket->fasilitasInclude->count() > 0 || $paket->fasilitasExclude->count() > 0)
            <article class="package-include bg-light-grey">
                <h3>INCLUDE & EXCLUDE :</h3>
                <ul>
                    @php
                        $max = max($paket->fasilitasInclude->count(), $paket->fasilitasExclude->count());
                    @endphp

                    @for($i = 0; $i < $max; $i++)
                        @if(isset($paket->fasilitasInclude[$i]))
                            <li><i class="fas fa-check"></i> {{ $paket->fasilitasInclude[$i]->nama_item }}</li>
                        @endif

                        @if(isset($paket->fasilitasExclude[$i]))
                            <li><i class="fas fa-times"></i> {{ $paket->fasilitasExclude[$i]->nama_item }}</li>
                        @endif
                    @endfor
                </ul>
            </article>
            @endif

            {{-- @endif --}}
          </div>
        </div>

      </div>

      {{-- Sidebar kanan --}}
      <div class="col-lg-4">
        <div class="sidebar">

          {{-- Related Images (fallback: pakai gambar cover berulang jika belum ada data galeri) --}}
          <div class="related-package">
              <h3>BOOK HERE</h3>
              <p>Please Contact Us!</p>

              @php
                $pdw = $paket->pokdarwis;
                $field = 'name_pokdarwis';
                $pokdarwisName = $pdw?->{$field} ;

                // Pesan WA
                $waMessage =
                "Halo Admin {$pokdarwisName},\n".
                "\n".
                "Saya ingin menanyakan ketersediaan paket berikut:\n".
                "- Nama Paket : {$paket->nama_paket}\n".
                // "- Durasi     : ".($paket->waktu_penginapan ?? '-')."\n".
                // "- Lokasi     : ".($paket->lokasi ?? '-')."\n".
                "- Perkiraan Tanggal: ...\n".
                "- Jumlah Orang     : ...\n".
                "- Catatan Tambahan : \n\n".
                "Mohon info harga terbaru, jadwal tersedia, dan cara pembayarannya. Terima kasih!\n".
                "".request()->fullUrl();

                // --- Sanitasi nomor: ambil digit, normalisasi ke format 62XXXXXXXXXX ---
                $rawKontak = preg_replace('/\D+/', '', (string)($pdw->kontak ?? '')); // hanya digit
                if ($rawKontak) {
                  if (str_starts_with($rawKontak, '0')) {
                    $rawKontak = '62' . substr($rawKontak, 1);
                  } elseif (str_starts_with($rawKontak, '62')) {
                    // sudah benar
                  } elseif (str_starts_with($rawKontak, '8')) {
                    $rawKontak = '62' . $rawKontak;
                  }
                }

                // --- Build URL WhatsApp dengan prefilled text ---
                $waUrl = $rawKontak ? ('https://wa.me/'.$rawKontak.'?text='.rawurlencode($waMessage)) : null;

                // --- Normalisasi Instagram: handle / URL penuh sama-sama diterima ---
                $ig = $pdw->instagram ?? null;
                if (!empty($ig)) {
                  $ig = trim(ltrim($ig, '@'));
                  if (!preg_match('~^https?://~i', $ig)) {
                    $ig = 'https://instagram.com/' . $ig;
                  }
                }
              @endphp

              @if($waUrl || $ig)
                <div class="socialgroup bookhere-socials">
                  <ul>
                    @if($ig)
                      <li>
                        <a href="{{ $ig }}" target="_blank" rel="noopener">
                          <i class="fab fa-instagram"></i>
                        </a>
                      </li>
                    @endif
                    @if($waUrl)
                      <li>
                        <a href="{{ $waUrl }}" target="_blank" rel="noopener">
                          <i class="fab fa-whatsapp"></i>
                        </a>
                      </li>
                    @endif
                  </ul>
                </div>
              @endif
            </div>


        <x-package-map
          :lat="$paket->map_lat"
          :lng="$paket->map_lng"
          :address="$paket->map_address"
          height="320"
          zoom="15"
          class="mb-3"
          :showLink="true"
        />



        {{-- More Package --}}
        <x-more-packages
            :items="$others"
            :limit="5"
            :bg="$paket->cover_url ?? $paket->img"  
            class="mt-4"
        />

      </div>
    </div>
  </div>
</div>
</div>
@endsection

@section('footer')
  <footer id="colophon" class="site-footer footer-primary">
        <div class="top-footer">
            <div class="container">
                <div class="upper-footer">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <aside class="widget widget_text">
                                <div class="footer-logo">
                                    <a href="{{ url('/') }}"><img src="{{ asset('assets/images/site-logo.png') }}" alt=""></a>
                                </div>
                                <div class="textwidget widget-text">
                                    Kantor Dinas Kebudayaan dan Pariwisata Bintan
                                </div>
                            </aside>
                        </div>

                        
                    
                        <div class="col-lg-3 col-sm-6">
                            <aside class="widget widget_latest_post widget-post-thumb">
                                <h3 class="widget-title">RECENT POST</h3>
                                <ul>
                                    <li>
                                        <figure class="post-thumb">
                                            <a href="#"><img src="{{ asset('assets/images/bintantourism.jpg') }}" alt=""></a>
                                        </figure>
                                        <div class="post-content">
                                            <h6><a href="#">BEST JOURNEY TO PEACEFUL PLACES</a></h6>
                                            <div class="entry-meta">
                                                <span class="posted-on"><a href="#">February 17, 2022</a></span>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <figure class="post-thumb">
                                            <a href="#"><img src="{{ asset('assets/images/bintantourism8.jpg') }}" alt=""></a>
                                        </figure>
                                        <div class="post-content">
                                            <h6><a href="#">TRAVEL WITH FRIENDS IS BEST</a></h6>
                                            <div class="entry-meta">
                                                <span class="posted-on"><a href="#">February 17, 2022</a></span>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </aside>
                        </div>

                        <div class="col-lg-3 col-sm-6">
                            <aside class="widget widget_text">
                                <h3 class="widget-title">CONTACT US</h3>
                                <div class="textwidget widget-text">
                                    <p>Feel free to contact and<br/>reach us !!</p>
                                    <ul>
                                        <li>
                                            <a href="tel:+01988256203"><i class="icon icon-phone1"></i> (+62) 770 692-505</a>
                                        </li>
                                        <li>
                                            <a href="https://bintantourism.com/"><i class="fas fa-globe"></i> Bintan Tourism</a>
                                        </li>
                                        <li>
                                            <i class="icon icon-map-marker1"></i> Jl. Trikora Km.36, Teluk Bakau, Kecamatan Gunung , Kabupaten Bintan, Kepulauan Riau 29151
                                        </li>
                                    </ul>
                                </div>
                            </aside>
                        </div>

                        <div class="col-lg-3 col-sm-6">
                            <aside class="widget">
                                <h3 class="widget-title">Gallery</h3>
                                @php
                                $images = [];
                                for ($i = 1; $i <= 8; $i++) {
                                    $path = "assets/images/bintantourism{$i}.jpg";
                                    if (file_exists(public_path($path))) $images[] = $path;
                                }
                                @endphp

                                <div class="gallery gallery-colum-3">
                                @foreach($images as $idx => $img)
                                    <figure class="gallery-item">
                                    <a href="{{ asset($img) }}" data-fancybox="gallery-1">
                                        <img src="{{ asset($img) }}" alt="Bintan Tourism {{ $idx+1 }}" loading="lazy"
                                            onerror="this.onerror=null;this.src='{{ asset('assets/images/default.png') }}'">
                                    </a>
                                    </figure>
                                @endforeach
                                </div>
                                {{-- <div class="gallery gallery-colum-3">
                                    @for ($i = 21; $i <= 26; $i++)
                                        <figure class="gallery-item">
                                            <a href="{{ asset("assets/images/img$i.jpg") }}" data-fancybox="gallery-1">
                                                <img src="{{ asset("assets/images/img$i.jpg") }}" alt="">
                                            </a>
                                        </figure>
                                    @endfor
                                </div> --}}
                            </aside>
                        </div>
                        
                    </div>

                    
                </div>
                

                <div class="lower-footer">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="footer-newsletter">
                                <p>Subscribe our newsletter for more update & news !!</p>
                                <form class="newsletter">
                                    <input type="email" name="email" placeholder="Enter Your Email">
                                    <button type="submit" class="outline-btn outline-btn-white">Subscribe</button>
                                </form>
                            </div>
                        </div>
                        <div class="col-lg-6 text-right">
                            <div class="social-icon">
                                <ul>
                                    <li><a href="https://www.facebook.com/disbudparbintan?_rdc=2&_rdr#" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="https://x.com/disbudparbintan"  target="_blank"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="https://www.youtube.com/channel/UCJIYZxQ_PtFe2-Ck77qXZDg"  target="_blank"><i class="fab fa-youtube"></i></a></li>
                                    <li><a href="https://www.instagram.com/bintantourism/" target="_blank"><i class="fab fa-instagram"></i></a></li>
                                    {{-- <li><a href="https://www.linkedin.com/"  target="_blank"><i class="fab fa-linkedin"></i></a></li> --}}
                                </ul>
                            </div>
                            <div class="footer-menu">
                                <ul>
                                    <li><a href="#">Privacy Policy</a></li>
                                    <li><a href="#">Term & Condition</a></li>
                                    <li><a href="#">FAQ</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="bottom-footer">
            <div class="container">
                <div class="copy-right text-center">Copyright &copy; 2025 Bintan Tourism. All rights reserved.</div>
            </div>
        </div>
    </footer>
@endsection
