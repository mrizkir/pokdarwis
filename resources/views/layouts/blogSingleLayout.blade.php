<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>@yield('title','Blog – Single')</title>

 <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap/css/bootstrap.min.css') }}" media="all">

    <!-- jquery-ui css -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/jquery-ui/jquery-ui.min.css') }}">

    <!-- fancybox box css -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/fancybox/dist/jquery.fancybox.min.css') }}">

    <!-- Fonts Awesome CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}">

    <!-- Elmentkit Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/elementskit-icon-pack/assets/css/ekiticons.css') }}">

    <!-- slick slider css -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/slick/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/slick/slick-theme.css') }}">

    <!-- google fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  @stack('styles')
</head>
<body>
  <div id="page" class="page">

    {{-- Header global (navbar situs) --}}
    <x-site-header active="pages" />

    <main id="content" class="site-main">

      {{-- Banner --}}
      @yield('banner')
        @php
            $pdwForBanner = $pokdarwis ?? ($post->pokdarwis ?? null);
        @endphp
        @if($pdwForBanner)
            <x-banner2 :pokdarwis="$pdwForBanner" />
        @endif

      <div class="single-post-section">
        <div class="single-post-inner">
          <div class="container">
            <div class="row">
              {{-- Kolom konten utama --}}
              <div class="col-lg-8 primary right-sidebar">
                {{-- Section konten utama diisi oleh view child --}}
                @yield('main')
              </div>

              {{-- Sidebar --}}
              <div class="col-lg-4 secondary">
                <div class="sidebar">
                  {{-- ABOUT AUTHOR (statik sederhana; silakan ganti bila perlu) --}}
                  <aside class="widget author_widget">
                    <h3 class="widget-title">ABOUT AUTHOR</h3>
                    <div class="widget-content text-center">
                        <div class="profile">
                        <figure class="avatar">
                            <a href="#">
                              <img src="{{ $post->pokdarwis->img ? asset('storage/'.$post->pokdarwis->img) : asset('assets/images/img20.jpg') }}"
                                  alt="{{ $post->pokdarwis->name_pokdarwis }}"
                                  style="width:100px;height:100px;object-fit:cover;border-radius:50%;">
                            </a>
                        </figure>
                        <div class="text-content">
                            <div class="name-title">
                            <h4>
                                <a href="#">{{ $post->pokdarwis->name_pokdarwis ?? 'Pokdarwis' }}</a>
                            </h4>
                            </div>
                            <p>{{ $post->pokdarwis->deskripsi ?? 'Profil singkat tentang Pokdarwis / creator.' }}</p>
                        </div>
                        <div class="socialgroup">
                            <ul>
                            <li><a target="_blank" href="{{ $post->pokdarwis->facebook }}"><i class="fab fa-facebook"></i></a></li>
                            <li><a target="_blank" href="{{ $post->pokdarwis->twitter }}"><i class="fab fa-twitter"></i></a></li>
                            <li><a target="_blank" href="{{ $post->pokdarwis->instagram }}"><i class="fab fa-instagram"></i></a></li>
                            </ul>
                        </div>
                        </div>
                    </div>
                    </aside>

                  {{-- RECENT POSTS (pakai $recentPosts jika dikirim controller) --}}
                  <aside class="widget widget_latest_post widget-post-thumb">
                    <h3 class="widget-title">Recent Post</h3>
                    <ul>
                      @foreach(($recentPosts ?? []) as $rp)
                        <li>
                          <figure class="post-thumb">
                            <a href="{{ route('posts.show',$rp->slug) }}">
                              <img src="{{ $rp->cover_url ? asset($rp->cover_url) : asset('assets/images/noimage.jpg') }}" alt="">
                            </a>
                          </figure>
                          <div class="post-content">
                            <h5><a href="{{ route('posts.show',$rp->slug) }}">{{ $rp->title }}</a></h5>
                            <div class="entry-meta">
                              <span class="posted-on">
                                {{ optional($rp->published_at instanceof \Carbon\Carbon ? $rp->published_at : \Carbon\Carbon::parse($rp->published_at))->format('M d, Y') }}
                              </span>
                              {{-- <span class="comments-link">{{ $rp->comments_count ?? 0 }} Comments</span> --}}
                            </div>
                          </div>
                        </li>
                      @endforeach
                    </ul>
                  </aside>

                  <aside class="widget widget_adds">
                    <figure><img src="{{ asset('assets/images/add-banner.jpg') }}" alt=""></figure>
                  </aside>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </main>
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

    <a id="backTotop" href="#" class="to-top-icon"><i class="fas fa-chevron-up"></i></a>
  </div>

  {{-- JS --}}
  <script src="{{ asset('assets/vendors/jquery/jquery.js') }}"></script>
  <script src="{{ asset('assets/vendors/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendors/jquery-ui/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('assets/vendors/slick/slick.min.js') }}"></script>
  <script src="{{ asset('assets/vendors/fancybox/dist/jquery.fancybox.min.js') }}"></script>
  <script src="{{ asset('assets/js/custom.min.js') }}"></script>
  @stack('scripts')
</body>
</html>
