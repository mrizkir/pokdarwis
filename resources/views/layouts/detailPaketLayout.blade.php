{{-- resources/views/layouts/pokdarwisLayout.blade.php --}}
<!doctype html>
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
      <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/jquery-ui/jquery-ui.min.css') }}">

      <!-- fancybox box css -->
      <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fancybox/dist/jquery.fancybox.min.css') }}">

      <!-- Fonts Awesome CSS -->
      <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}">

      <!-- Elmentkit Icon CSS -->
      <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/elementskit-icon-pack/assets/css/ekiticons.css') }}">

      <!-- slick slider css -->
      <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/slick/slick.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/slick/slick-theme.css') }}">

      <!-- google fonts -->
      <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">

      <!-- Custom CSS -->
      <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">

      <title>@yield('title','Pokdarwis | Traveler')</title>

      @stack('head')
      @stack('styles')
   </head>
  {{-- Preloader --}}
  {{-- <x-preloader /> --}}

  <div id="page" class="page">
    {{-- Header , Tombol Booking --}}
    @php
         use Illuminate\Support\Facades\Route;
         $ctxPokdarwis = $pokdarwis ?? ($paket->pokdarwis ?? null);

         // default: booking umum
         $bookHref = Route::has('booking') ? route('booking') : url('/booking');

         // jika ada paket + pokdarwis → arahkan ke route paket
         if (isset($paket, $ctxPokdarwis) && Route::has('booking.package')) {
            $bookHref = route('booking.package', [$ctxPokdarwis, $paket]);
         }
         // kalau hanya ada pokdarwis → route pokdarwis
         elseif ($ctxPokdarwis && Route::has('booking.pokdarwis')) {
            $bookHref = route('booking.pokdarwis', $ctxPokdarwis);
         }
      @endphp

      <div class="bottom-header">
  <header id="masthead" class="site-header">
        <div class="top-header">
            <div class="container">
                <div class="top-header-inner">
                    <div class="header-contact text-left">
                        <a href="tel:01977259912">
                            <i aria-hidden="true" class="icon icon-phone-call2"></i>
                            <div class="header-contact-details">
                                <span class="contact-label">For Further Inquires :</span>
                                <h5 class="header-contact-no">(+62) 770 692-505</h5>
                            </div>
                        </a>
                    </div>

                    <div class="site-logo text-center">
                        <h1 class="site-title">
                            <a href="{{ url('/') }}">
                                <img src="{{ asset('assets/images/site-logo.png') }}" alt="Logo">
                            </a>
                        </h1>
                    </div>

                    {{-- <div class="header-icon text-right">
                        <div class="header-search-icon d-inline-block">
                            <a href="#"><i aria-hidden="true" class="fas fa-search"></i></a>
                        </div>
                        <div class="offcanvas-menu d-inline-block">
                            <a href="#"><i aria-hidden="true" class="icon icon-burger-menu"></i></a>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>

<div class="bottom-header">
  <div class="container">
    <div class="bottom-header-inner d-flex justify-content-between align-items-center">

      @php
        // --- Build WhatsApp URL dari $pokdarwis->kontak ---
        // Ambil digit saja, handle format 08xxxx / +62xxxx / 62xxxx / 8xxxx
        $waUrl = null;
        $raw   = $pokdarwis->kontak ?? null;
        if ($raw) {
          $digits = preg_replace('/\D+/', '', $raw);       // keep digits only
          if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
          } elseif (str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
          } // jika sudah 62… atau 628… biarkan
          if ($digits) $waUrl = 'https://wa.me/'.$digits;
        }
      @endphp

      <div class="header-social social-icon">
        <ul>
          @if(!empty($pokdarwis?->facebook))
            <li><a href="{{ $pokdarwis->facebook }}" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a></li>
          @endif

          @if($waUrl)
            <li><a href="{{ $waUrl }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i></a></li>
          @endif

          @if(!empty($pokdarwis?->instagram))
            <li><a href="{{ $pokdarwis->instagram }}" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a></li>
          @endif
        </ul>
      </div>

      {{-- Navigation Bar --}}
      <x-navbar active="Tour" :pokdarwis="$pokdarwis ?? null" />

      <div class="bottom-header-inner d-flex justify-content-between align-items-center">
                    <div class="header-btn">
                        @guest
                        <a href="{{ url('/login') }}" class="round-btn" style="all:unset; color:white; cursor:pointer; display:inline-block;">
                            LOG IN
                        </a>
                        @endguest
                    </div>
                    {{-- <div class="header-btn">
                        <a href="{{ url('/register') }}" class="round-btn" style="all:unset; color:white; cursor:pointer; display:inline-block;">REGISTER</a>
                    </div> --}}
                </div>

    </div>
  </div>
</div>


        <div class="mobile-menu-container"></div>
    </header>
</div>
    

    <main id="content" class="site-main">
      @yield('banner')
      @yield('main')
      <aside class="col-lg-4 mb-5">  </aside>
      @yield('footer')
   </main>
   
   
   <x-back-to-top />
   
</div>

<!-- JavaScript -->
      <script src="{{ asset('assets/vendors/jquery/jquery.js') }}"></script>
      <script src="{{ asset('assets/vendors/waypoint/waypoints.js') }}"></script>
      {{-- <script src="{{ asset('assets/vendors/bootstrap/js/bootstrap.min.js') }}"></script> --}}
      <script src="{{ asset('assets/vendors/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
      <script src="{{ asset('assets/vendors/jquery-ui/jquery-ui.min.js') }}"></script>
      <script src="{{ asset('assets/vendors/countdown-date-loop-counter/loopcounter.js') }}"></script>
      <script src="{{ asset('assets/vendors/counterup/jquery.counterup.min.js') }}"></script>
      <script src="https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js"></script>
      <script src="{{ asset('assets/vendors/masonry/masonry.pkgd.min.js') }}"></script>
      <script src="{{ asset('assets/vendors/slick/slick.min.js') }}"></script>
      <script src="{{ asset('assets/vendors/fancybox/dist/jquery.fancybox.min.js') }}"></script>
      <script src="{{ asset('assets/vendors/slick-nav/jquery.slicknav.js') }}"></script>
      <script src="{{ asset('assets/js/custom.min.js') }}"></script>

      @stack('scripts')
   </body>
</html>