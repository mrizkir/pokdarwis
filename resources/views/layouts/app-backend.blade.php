<!DOCTYPE html>
<html lang="en">
<head>
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
    <link rel="stylesheet" href="{{ asset('assets/css/custom-backend.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    
    @yield('page-styles')

    <title>@yield('page-title')</title>
    @stack('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body url-current-page="{!! Helper::getCurrentPageURL() !!}" base-url="{!! url('/') !!}">

    {{-- HEADER (tetap, reusable) --}}
    <div>
        <header id="masthead" class="site-header">
            <div class="top-header">
                <div class="container">
                    <div class="top-header-inner">
                        <div class="header-contact-admin text-left">
                            <a href="tel:01977259912">
                                <i aria-hidden="true" class="icon icon-phone-call2"></i>
                                <div class="header-contact-admin-details">
                                    <span class="contact-label">For Further Inquires :</span>
                                    <h5 class="header-contact-admin-no">(+62) 770 692-505</h5>
                                </div>
                            </a>
                        </div>
    
                        <div class="site-logo text-center">
                            <h1 class="site-title">
                                <a href="{{ url('/') }}">
                                    {{-- <img src="{{ asset('assets/images/site-logo.png') }}" alt="Logo"> --}}
                                </a>
                            </h1>
                        </div>
    
                        @if (auth()->user()->role === 'pokdarwis')
                        <div class="header-icon text-right">
                            {{-- <div class="header-search-icon d-inline-block">
                            <a href="#"><i aria-hidden="true" class="fas fa-search"></i></a>
                            </div> --}}
                            <button type="button" 
                                    class="btn btn-link text-secondary ms-2 p-0" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#videoGuideModal" 
                                    title="Lihat panduan upload">
                            <i class="fa-regular fa-circle-question fa-lg"></i>
                            </button>
                            <div class="offcanvas-menu d-inline-block">
                            <a href="#">
                                <i aria-hidden="true" class="icon icon-burger-menu" style="color: grey"></i>
                            </a>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
    
                <div class="container">
                    <div class="bottom-header-inner d-flex justify-content-between align-items-center">
                        <div></div>
                        
    
                        {{-- Navigation Bar --}}
                        <x-navbar-admin active="home">
    
                        </x-navbar-admin>
    
                        <div class="header-btn">
                            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="round-btn" style="all:unset; cursor:pointer; display:inline-block; font-weight: bold;">
                                    LOGOUT
                                </button>
                            </form>
                        </div>

                </div>
    
            <div class="mobile-menu-container"></div>
        </header>
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
          <source src="{{ asset('assets/video/panduan-edit-profile.mp4') }}" type="video/mp4">
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


    {{-- MAIN (isi dari child) --}}
    <main id="content" class="container site-main">
        {{-- Banner --}}
        @hasSection('banner')
            @yield('banner')
        @endif
        
        <div class="page-header admin-header">
            @yield("page-header")
        </div>

        <div class="page-breadcrumb">
            @yield("page-breadcrumb")
        </div>

        <div class="container">
            @yield('main')
        </div>
    </main>
    

    {{-- FOOTER (tetap, reusable) --}}
    {{-- <footer id="colophon" class="site-footer footer-primary">
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
                                    Urna ratione ante harum provident, eleifend, vulputate molestiae proin fringilla, praesentium magna conubia at perferendis, pretium, aenean aut ultrices.
                                </div>
                            </aside>
                        </div>

                        
                    <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Rizky</td>
                        <td>Email</td>
                        <td>Rizky</td>
                    </tr>
                </tbody>
            </table>
        </div>
                        <div class="col-lg-3 col-sm-6">
                            <aside class="widget widget_latest_post widget-post-thumb">
                                <h3 class="widget-title">RECENT POST</h3>
                                <ul>
                                    <li>
                                        <figure class="post-thumb">
                                            <a href="#"><img src="{{ asset('assets/images/img21.jpg') }}" alt=""></a>
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
                                            <a href="#"><img src="{{ asset('assets/images/img22.jpg') }}" alt=""></a>
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
                                            <a href="tel:+01988256203"><i class="icon icon-phone1"></i> +01(988) 256 203</a>
                                        </li>
                                        <li>
                                            <a href="mailto:info@domain.com"><i class="icon icon-envelope1"></i> info@domain.com</a>
                                        </li>
                                        <li>
                                            <i class="icon icon-map-marker1"></i> 3146 Koontz, California
                                        </li>
                                    </ul>
                                </div>
                            </aside>
                        </div>

                        <div class="col-lg-3 col-sm-6">
                            <aside class="widget">
                                <h3 class="widget-title">Gallery</h3>
                                <div class="gallery gallery-colum-3">
                                    @for ($i = 21; $i <= 26; $i++)
                                        <figure class="gallery-item">
                                            <a href="{{ asset("assets/images/img$i.jpg") }}" data-fancybox="gallery-1">
                                                <img src="{{ asset("assets/images/img$i.jpg") }}" alt="">
                                            </a>
                                        </figure>
                                    @endfor
                                </div>
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
                                    <li><a href="https://www.facebook.com/" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="https://www.twitter.com/"  target="_blank"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="https://www.youtube.com/"  target="_blank"><i class="fab fa-youtube"></i></a></li>
                                    <li><a href="https://www.instagram.com/" target="_blank"><i class="fab fa-instagram"></i></a></li>
                                    <li><a href="https://www.linkedin.com/"  target="_blank"><i class="fab fa-linkedin"></i></a></li>
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
                <div class="copy-right text-center">Copyright &copy; 2022 Traveler. All rights reserved.</div>
            </div>
        </div>
    </footer> --}}

    <!-- back to top -->
    <a id="backTotop" href="#" class="to-top-icon"><i class="fas fa-chevron-up"></i></a>

    <!-- offcanvas -->
    <x-profile-form-pokdarwis> </x-profile-form-pokdarwis>
    {{-- <div id="offCanvas" class="offcanvas-container">
        <div class="offcanvas-inner">
            <div class="offcanvas-sidebar">
                <aside class="widget author_widget">
                    <h3 class="widget-title">OUR PROPRIETOR</h3>
                    <div class="widget-content text-center">
                        <div class="profile">
                            <figure class="avatar"><img src="{{ asset('assets/images/guruntelagabiru.jpg') }}" alt=""></figure>
                            <div class="text-content">
                                <div class="name-title"><h4>James Watson</h4></div>
                                <p>Accumsan? Aliquet nobis doloremque, aliqua? Inceptos voluptatem…</p>
                            </div>
                            <div class="socialgroup">
                                <ul>
                                    <li><a target="_blank" href="#"><i class="fab fa-facebook"></i></a></li>
                                    <li><a target="_blank" href="#"><i class="fab fa-google"></i></a></li>
                                    <li><a target="_blank" href="#"><i class="fab fa-twitter"></i></a></li>
                                    <li><a target="_blank" href="#"><i class="fab fa-instagram"></i></a></li>
                                    <li><a target="_blank" href="#"><i class="fab fa-pinterest"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </aside>

                <aside class="widget widget_text text-center">
                    <h3 class="widget-title">CONTACT US</h3>
                    <div class="textwidget widget-text">
                        <p>Feel free to contact and<br/> reach us !!</p>
                        <ul>
                            <li><a href="tel:+01988256203"><i class="icon icon-phone1"></i> +01(988) 256 203</a></li>
                            <li><a href="mailto:info@domain.com"><i class="icon icon-envelope1"></i> info@domain.com</a></li>
                            <li><i class="icon icon-map-marker1"></i> 3146 Koontz, California</li>
                        </ul>
                    </div>
                </aside>

                <a href="#" class="offcanvas-close"><i class="fas fa-times"></i></a>
            </div>
        </div>
        <div class="overlay"></div>
    </div> --}}

    {{-- JS Scripts --}}
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
    <script src="{{ asset('assets/vendors/jquery/jquery.js') }}" defer></script>
    <script src="{{ asset('assets/vendors/waypoint/waypoints.js') }}" defer></script>
    <script src="{{ asset('assets/vendors/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>
    <script src="{{ asset('assets/vendors/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery-ui/jquery-ui.min.js') }}" defer></script>
    <script src="{{ asset('assets/vendors/countdown-date-loop-counter/loopcounter.js') }}" defer></script>
    <script src="{{ asset('assets/vendors/counterup/jquery.counterup.min.js') }}" defer></script>
    <script src="https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js" defer></script>
    <script src="{{ asset('assets/vendors/masonry/masonry.pkgd.min.js') }}" defer></script>
    <script src="{{ asset('assets/vendors/slick/slick.min.js') }}" defer></script>
    <script src="{{ asset('assets/vendors/fancybox/dist/jquery.fancybox.min.js') }}" defer></script>
    <script src="{{ asset('assets/vendors/slick-nav/jquery.slicknav.js') }}" defer></script>
    <script src="{{ asset('assets/js/custom.min.js') }}" defer></script>
    @stack('scripts')

    @yield('page-scripts')
</body>
</html>
