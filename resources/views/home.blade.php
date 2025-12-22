@extends('layouts.app')

@section('title','Home')

@section('banner')
  <x-banner :slides="
  [

      ['image'=>'assets/images/bintantourism.jpg',
      'title'=>'JOURNEY TO EXPLORE BINTAN',
      'text'=>'WELCOME',
      'primaryHref'=>route('pokdarwis'),
    //   'primaryText'=>'EXPLORE'
    ],
      
    //   ['image'=>'assets/images/img7.jpg',
    //   'title'=>'BAJAKAH',
    //   'text'=>'',
    //   'primaryHref'=>route('pokdarwis'),
    //   // 'primaryText'=>'EXPLORE'
    //   ],

    //   ['image'=>'assets/images/img7.jpg',
    //   'title'=>'GUDEM BEE FARM',
    //   'text'=>'',
    //   'primaryHref'=>route('pokdarwis'),
    //   // 'primaryText'=>'EXPLORE'
    //   ],

    //   ['image'=>'assets/images/guruntelagabiru.jpg',
    //   'title'=>'GURUN TELAGA BIRU',
    //   'text'=>'',
    //   'primaryHref'=>route('pokdarwis'),
    //   // 'primaryText'=>'EXPLORE'
    //   ],

    //   ['image'=>'assets/images/img7.jpg',
    //   'title'=>'LESUNG EMAS',
    //   'text'=>'',
    //   'primaryHref'=>route('pokdarwis'),
    //   // 'primaryText'=>'EXPLORE'
    //   ],

    //   ['image'=>'assets/images/img7.jpg',
    //   'title'=>'MANGGAR ABADI',
    //   'text'=>'',
    //   'primaryHref'=>route('pokdarwis'),
    //   // 'primaryText'=>'EXPLORE'
    //   ],

    //   ['image'=>'assets/images/img7.jpg',
    //   'title'=>'PEMANCINGAN WONG DHESO',
    //   'text'=>'',
    //   'primaryHref'=>route('pokdarwis'),
    //   // 'primaryText'=>'EXPLORE'
    //   ],

    //   ['image'=>'assets/images/img7.jpg',
    //   'title'=>'SUMAT',
    //   'text'=>'',
    //   'primaryHref'=>route('pokdarwis'),
    //   // 'primaryText'=>'EXPLORE'
    //   ],
      
    //   ['image'=>'assets/images/img7.jpg',
    //   'title'=>'TEKAD TANI',
    //   'text'=>'',
    //   'primaryHref'=>route('pokdarwis'),
    //   // 'primaryText'=>'EXPLORE'
    //   ],
  ]" class="mb-5"/>
  

@endsection

@section('main')


                  <x-product-card2
                    subtitle="UNCOVER PLACE"
                    title="POPULAR PRODUCT"
                    text="Produk-produk pilihan dari berbagai Pokdarwis."
                    :items="$items"
                    ctaHref="{{ url('/destination') }}"
                    ctaText="More Destination"
                />


<section class="inner-about-wrap py-2">
    <div class="container">
            {{-- Produk Card --}}
    </div>
</section>

@endsection

        <!-- ***home banner html end here*** -->
        <!-- ***Home search field html start from here*** -->
        {{-- <div class="home-trip-search primary-bg">
            <div class="container">
                <form method="get" class="trip-search-inner d-flex">
                    <div class="group-input">
                        <label> Search Destination* </label>
                        <input type="text" name="q" placeholder="Enter Destination">
                    </div>
                    <div class="group-input">
                        <label> Pax Number* </label>
                        <input type="number" min="1" name="pax" placeholder="No.of People">
                    </div>
                    <div class="group-input width-col-3">
                        <label> Checkin Date* </label>
                        <i class="far fa-calendar"></i>
                        <input class="input-date-picker" type="text" name="checkin" placeholder="MM / DD / YY" autocomplete="off" readonly>
                    </div>
                    <div class="group-input width-col-3">
                        <label> Checkout Date* </label>
                        <i class="far fa-calendar"></i>
                        <input class="input-date-picker" type="text" name="checkout" placeholder="MM / DD / YY" autocomplete="off" readonly>
                    </div>
                    <div class="group-input width-col-3">
                        <input type="submit" value="INQUIRE NOW">
                    </div>
                </form>
            </div>
        </div> --}}
        <!-- ***search field html end here*** -->