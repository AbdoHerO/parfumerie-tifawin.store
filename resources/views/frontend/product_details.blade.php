@extends('frontend.layouts.app')

@section('meta_title'){{ $detailedProduct->meta_title }}@stop

@section('meta_description'){{ $detailedProduct->meta_description }}@stop

@section('meta_keywords'){{ $detailedProduct->tags }}@stop

@section('meta')
<!-- Schema.org markup for Google+ -->
<meta itemprop="name" content="{{ $detailedProduct->meta_title }}">
<meta itemprop="description" content="{{ $detailedProduct->meta_description }}">
<meta itemprop="image" content="{{ uploaded_asset($detailedProduct->meta_img) }}">

<!-- Twitter Card data -->
<meta name="twitter:card" content="product">
<meta name="twitter:site" content="@publisher_handle">
<meta name="twitter:title" content="{{ $detailedProduct->meta_title }}">
<meta name="twitter:description" content="{{ $detailedProduct->meta_description }}">
<meta name="twitter:creator" content="@author_handle">
<meta name="twitter:image" content="{{ uploaded_asset($detailedProduct->meta_img) }}">
<meta name="twitter:data1" content="{{ single_price($detailedProduct->unit_price) }}">
<meta name="twitter:label1" content="Price">

<!-- Open Graph data -->
<meta property="og:title" content="{{ $detailedProduct->meta_title }}" />
<meta property="og:type" content="og:product" />
<meta property="og:url" content="{{ route('product', $detailedProduct->slug) }}" />
<meta property="og:image" content="{{ uploaded_asset($detailedProduct->meta_img) }}" />
<meta property="og:description" content="{{ $detailedProduct->meta_description }}" />
<meta property="og:site_name" content="{{ get_setting('meta_title') }}" />
<meta property="og:price:amount" content="{{ single_price($detailedProduct->unit_price) }}" />
<meta property="product:price:currency" content="{{ \App\Models\Currency::findOrFail(get_setting('system_default_currency'))->code }}" />
<meta property="fb:app_id" content="{{ env('FACEBOOK_PIXEL_ID') }}">
@endsection

@section('content')
<section class="mb-4 pt-3">
    <div class="container">
        <div class="bg-white py-3">
            <div class="row">
                <!-- Product Image Gallery -->
                <div class="col-xl-5 col-lg-6 mb-4">
                    @include('frontend.product_details.image_gallery')
                </div>

                <!-- Product Details -->
                <div class="col-xl-7 col-lg-6">
                    @include('frontend.product_details.details')
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mb-4">
    <div class="container">
        @if ($detailedProduct->auction_product)
        <!-- Reviews & Ratings -->
        @include('frontend.product_details.review_section')

        <!-- Description, Video, Downloads -->
        @include('frontend.product_details.description')

        <!-- Product Query -->
        @include('frontend.product_details.product_queries')
        @else
        <div class="row gutters-16">
            <!-- Left side -->
            <div class="col-lg-3">
                <!-- Seller Info -->
                @include('frontend.product_details.seller_info')

                <!-- Top Selling Products -->
                <div class="d-none d-lg-block">
                    @include('frontend.product_details.top_selling_products')
                </div>
            </div>

            <!-- Right side -->
            <div class="col-lg-9">

                <!-- Reviews & Ratings -->
                @include('frontend.product_details.review_section')

                <!-- Description, Video, Downloads -->
                @include('frontend.product_details.description')

                <!-- Related products -->
                @include('frontend.product_details.related_products')

                <!-- Product Query -->
                @include('frontend.product_details.product_queries')

                <!-- Top Selling Products -->
                <div class="d-lg-none">
                    @include('frontend.product_details.top_selling_products')
                </div>

            </div>
        </div>
        @endif
    </div>
</section>

@endsection

@section('modal')
<!-- Image Modal -->
<div class="modal fade" id="image_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
        <div class="modal-content position-relative">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="p-4">
                <div class="size-300px size-lg-450px">
                    <img class="img-fit h-100 lazyload" src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chat Modal -->
<div class="modal fade" id="chat_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
        <div class="modal-content position-relative">
            <div class="modal-header">
                <h5 class="modal-title fw-600 h5">{{ translate('Any query about this product') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="" action="{{ route('conversations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="product_id" value="{{ $detailedProduct->id }}">
                <div class="modal-body gry-bg px-3 pt-3">
                    <div class="form-group">
                        <input type="text" class="form-control mb-3 rounded-0" name="title" value="{{ $detailedProduct->name }}" placeholder="{{ translate('Product Name') }}" required>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control rounded-0" rows="8" name="message" required placeholder="{{ translate('Your Question') }}">{{ route('product', $detailedProduct->slug) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary fw-600 rounded-0" data-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary fw-600 rounded-0 w-100px">{{ translate('Send') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bid Modal -->
@if($detailedProduct->auction_product == 1)
@php
$highest_bid = $detailedProduct->bids->max('amount');
$min_bid_amount = $highest_bid != null ? $highest_bid+1 : $detailedProduct->starting_bid;
@endphp
<div class="modal fade" id="bid_for_detail_product" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('Bid For Product') }} <small>({{ translate('Min Bid Amount: ').$min_bid_amount }})</small> </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" action="{{ route('auction_product_bids.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $detailedProduct->id }}">
                    <div class="form-group">
                        <label class="form-label">
                            {{translate('Place Bid Price')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="form-group">
                            <input type="number" step="0.01" class="form-control form-control-sm" name="amount" min="{{ $min_bid_amount }}" placeholder="{{ translate('Enter Amount') }}" required>
                        </div>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-sm btn-primary transition-3d-hover mr-1">{{ translate('Submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Product Review Modal -->
<div class="modal fade" id="product-review-modal">
    <div class="modal-dialog">
        <div class="modal-content" id="product-review-modal-content">

        </div>
    </div>
</div>

<!-- Modal Commande visiteur -->
<div class="modal fade" id="orderVisite" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Insérer vos Informations</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-default" id="formOrderGuest" role="form" action="{{ route('ordervisite.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="p-3">
                        <div class="row">
                            <div class="col-md-2">
                                <!-- <label>Votre Nom</label> -->
                                <label>Nom Complet</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control mb-3" placeholder="First Name" rows="2" name="first_name" id="first_name" autocomplete="off" required />
                            </div>
                        </div>
                        <!-- <div class="row">
                            <div class="col-md-2">
                                <label>Votre Prénom</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control mb-3" placeholder="Last Name" rows="2" name="last_name" id="last_name" autocomplete="off" required />
                            </div>
                        </div> -->
                        <div class="row">
                            <div class="col-md-2">
                                <label>Phone</label>
                            </div>
                            <div class="col-md-10">
                                <input type="tel" class="form-control mb-3" placeholder="ex: 06xxxxxxxx" rows="2" name="phone" id="phone" pattern="[0]{1}[0-9]{1}[0-9]{8}" autocomplete="off" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{translate('City')}}</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control mb-3" placeholder="ex: Casablanca" rows="2" name="city" id="selectCity" autocomplete="off" required />
                            </div>
                        </div>
                        <!-- <div class="form-group row">
                            <label class="col-md-2 col-from-label">{{translate('City')}}</label>
                            <div class="col-md-10">
                                <select class="form-control aiz-selectpicker" name="city" id="selectCity" data-selected="" data-live-search="true" required>
                                    <option value=""></option>
                                    <option value="Casablanca">Casablanca</option>
                                    <option value="El Kelaa des Srarhna">El Kelaa des Srarhna</option>
                                    <option value="Fès">Fes</option>
                                    <option value="Rabat">Rabat</option>
                                    <option value="Tifariti">Tifariti</option>
                                    <option value="Tangier">Tangier</option>
                                    <option value="Marrakech">Marrakech</option>
                                    <option value="Sale">Sale</option>
                                    <option value="Meknès">Meknes</option>
                                    <option value="Oujda-Angad">Oujda-Angad</option>
                                    <option value="Kenitra">Kenitra</option>
                                    <option value="Agadir">Agadir</option>
                                    <option value="Tétouan">Tetouan</option>
                                    <option value="Taourirt">Taourirt</option>
                                    <option value="Temara">Temara</option>
                                    <option value="Safi">Safi</option>
                                    <option value="Mohammedia">Mohammedia</option>
                                    <option value="El Jadid">El Jadid</option>
                                    <option value="Kouribga">Kouribga</option>
                                    <option value="Béni Mellal">Beni Mellal</option>
                                    <option value="Laâyoune">Laayoune</option>
                                    <option value="Ait Melloul">Ait Melloul</option>
                                    <option value="Nador">Nador</option>
                                    <option value="Taza">Taza</option>
                                    <option value="Barrechid">Barrechid</option>
                                    <option value="Settat">Settat</option>
                                    <option value="Inezgane">Inezgane</option>
                                    <option value="Al Khmissat">Al Khmissat</option>
                                    <option value="Ksar El Kebir">Ksar El Kebir</option>
                                    <option value="Mediouna">Mediouna</option>
                                    <option value="Larache">Larache</option>
                                    <option value="Khénifra">Khenifra</option>
                                    <option value="Guelmim">Guelmim</option>
                                    <option value="Berkane">Berkane</option>
                                    <option value="Al Fqih Ben Çalah">Al Fqih Ben Calah</option>
                                    <option value="Bouskoura">Bouskoura</option>
                                    <option value="Oued Zem">Oued Zem</option>
                                    <option value="Sidi Slimane">Sidi Slimane</option>
                                    <option value="Guercif">Guercif</option>
                                    <option value="Errachidia">Errachidia</option>
                                    <option value="Ben Guerir">Ben Guerir</option>
                                    <option value="Oulad Teïma">Oulad Teima</option>
                                    <option value="Fnidq">Fnidq</option>
                                    <option value="Sidi Qacem">Sidi Qacem</option>
                                    <option value="Moulay Abdallah">Moulay Abdallah</option>
                                    <option value="Warzat">Warzat</option>
                                    <option value="Youssoufia">Youssoufia</option>
                                    <option value="Aïn Harrouda">Ain Harrouda</option>
                                    <option value="Martil">Martil</option>
                                    <option value="Ouezzane">Ouezzane</option>
                                    <option value="Sidi Bennour">Sidi Bennour</option>
                                    <option value="Sidi Yahya Zaer">Sidi Yahya Zaer</option>
                                    <option value="Midalt">Midalt</option>
                                    <option value="Azrou">Azrou</option>
                                    <option value="Al Hoceïma">Al Hoceima</option>
                                    <option value="Boujad">Boujad</option>
                                    <option value="Ain El Aouda">Ain El Aouda</option>
                                    <option value="Qasbat Tadla">Qasbat Tadla</option>
                                    <option value="Beni Yakhlef">Beni Yakhlef</option>
                                    <option value="Azemmour">Azemmour</option>
                                    <option value="Mrirt">Mrirt</option>
                                    <option value="Jerada">Jerada</option>
                                    <option value="El Aïoun">El Aioun</option>
                                    <option value="Temsia">Temsia</option>
                                    <option value="Aziylal">Aziylal</option>
                                    <option value="Ait Ourir">Ait Ourir</option>
                                    <option value="Zagora">Zagora</option>
                                    <option value="Biougra">Biougra</option>
                                    <option value="Sidi Yahia El Gharb">Sidi Yahia El Gharb</option>
                                    <option value="El Hajeb">El Hajeb</option>
                                    <option value="Zaïo">Zaio</option>
                                    <option value="Zeghanghane">Zeghanghane</option>
                                    <option value="Tit Mellil">Tit Mellil</option>
                                    <option value="Mechraa Bel Ksiri">Mechraa Bel Ksiri</option>
                                    <option value="Sidi Smai’il">Sidi Smai'il</option>
                                    <option value="Arfoud">Arfoud</option>
                                    <option value="Demnat">Demnat</option>
                                    <option value="Bou Arfa">Bou Arfa</option>
                                    <option value="Mehdya">Mehdya</option>
                                    <option value="Aïn Taoujdat">Ain Taoujdat</option>
                                    <option value="Tahla">Tahla</option>
                                    <option value="Missour">Missour</option>
                                    <option value="Zawyat ech Cheïkh">Zawyat ech Cheikh</option>
                                    <option value="Oulad Tayeb">Oulad Tayeb</option>
                                    <option value="Sidi Lmokhtar">Sidi Lmokhtar</option>
                                    <option value="Douar Toulal">Douar Toulal</option>
                                    <option value="Oulad Fraj">Oulad Fraj</option>
                                    <option value="Ahfir">Ahfir</option>
                                    <option value="Bou Djeniba">Bou Djeniba</option>
                                    <option value="Goulmima">Goulmima</option>
                                    <option value="Sidi Zouine">Sidi Zouine</option>
                                    <option value="Ait Yaazem">Ait Yaazem</option>
                                    <option value="Oulad Hamdane">Oulad Hamdane</option>
                                    <option value="Laaouama">Laaouama</option>
                                    <option value="Targuist">Targuist</option>
                                    <option value="Bou Fekrane">Bou Fekrane</option>
                                    <option value="El Menzel">El Menzel</option>
                                    <option value="Oulad Ayyad">Oulad Ayyad</option>
                                    <option value="Ar Rommani">Ar Rommani</option>
                                    <option value="Boudenib">Boudenib</option>
                                    <option value="Ain Lehjer">Ain Lehjer</option>
                                    <option value="Oulad Ben Sebbah">Oulad Ben Sebbah</option>
                                    <option value="Beni Bou Yafroun">Beni Bou Yafroun</option>
                                    <option value="Ad Dakhla">Ad Dakhla</option>
                                    <option value="Wislane">Wislane</option>
                                    <option value="Tiflet">Tiflet</option>
                                    <option value="Lqoliaa">Lqoliaa</option>
                                    <option value="Sefrou">Sefrou</option>
                                    <option value="Taroudannt">Taroudannt</option>
                                    <option value="Essaouira">Essaouira</option>
                                    <option value="Ait Ali">Ait Ali</option>
                                    <option value="Tiznit">Tiznit</option>
                                    <option value="Tan-Tan">Tan-Tan</option>
                                    <option value="Sa’ada">Sa'ada</option>
                                    <option value="Skhirate">Skhirate</option>
                                    <option value="Benslimane">Benslimane</option>
                                    <option value="Beni Enzar">Beni Enzar</option>
                                    <option value="M’diq">M'diq</option>
                                    <option value="Ad Darwa">Ad Darwa</option>
                                    <option value="Al Aaroui">Al Aaroui</option>
                                    <option value="Semara">Semara</option>
                                    <option value="Chefchaouene">Chefchaouene</option>
                                    <option value="Sidi Mohamed Lahmar">Sidi Mohamed Lahmar</option>
                                    <option value="Tineghir">Tineghir</option>
                                    <option value="Zoumi">Zoumi</option>
                                    <option value="Douar Laouamra">Douar Laouamra</option>
                                    <option value="Sidi Bibi">Sidi Bibi</option>
                                    <option value="Taounate">Taounate</option>
                                    <option value="Bouznika">Bouznika</option>
                                    <option value="Aguelmous">Aguelmous</option>
                                    <option value="Aourir">Aourir</option>
                                    <option value="Imzouren">Imzouren</option>
                                    <option value="Mnasra">Mnasra</option>
                                    <option value="Oulad Zemam">Oulad Zemam</option>
                                    <option value="Ben Ahmed">Ben Ahmed</option>
                                    <option value="Arbaoua">Arbaoua</option>
                                    <option value="Douar Oulad Hssine">Douar Oulad Hssine</option>
                                    <option value="Dar Ould Zidouh">Dar Ould Zidouh</option>
                                    <option value="Bahharet Oulad Ayyad">Bahharet Oulad Ayyad</option>
                                    <option value="Asilah">Asilah</option>
                                    <option value="Mograne">Mograne</option>
                                    <option value="Lalla Mimouna">Lalla Mimouna</option>
                                    <option value="Souk et Tnine Jorf el Mellah">Souk et Tnine Jorf el Mellah</option>
                                    <option value="Tameslouht">Tameslouht</option>
                                    <option value="Chichaoua">Chichaoua</option>
                                    <option value="Fritissa">Fritissa</option>
                                    <option value="Oulad Yaïch">Oulad Yaich</option>
                                    <option value="Taza">Taza</option>
                                    <option value="Douar Oulad Aj-jabri">Douar Oulad Aj-jabri</option>
                                    <option value="Oulad Hammou">Oulad Hammou</option>
                                    <option value="Bellaa">Bellaa</option>
                                    <option value="Dar Bel Hamri">Dar Bel Hamri</option>
                                    <option value="Moulay Bousselham">Moulay Bousselham</option>
                                    <option value="Ksebia">Ksebia</option>
                                    <option value="Sabaa Aiyoun">Sabaa Aiyoun</option>
                                    <option value="Tamorot">Tamorot</option>
                                    <option value="Bouknadel">Bouknadel</option>
                                    <option value="Aït Faska">Ait Faska</option>
                                    <option value="Bourdoud">Bourdoud</option>
                                    <option value="Boureït">Boureit</option>
                                    <option value="Oulad Barhil">Oulad Barhil</option>
                                    <option value="Oulad Said">Oulad Said</option>
                                    <option value="Lamzoudia">Lamzoudia</option>
                                    <option value="Ain Aicha">Ain Aicha</option>
                                    <option value="El Ghiate">El Ghiate</option>
                                    <option value="Safsaf">Safsaf</option>
                                    <option value="Echemmaia Est">Echemmaia Est</option>
                                    <option value="Ouaoula">Ouaoula</option>
                                    <option value="Douar Olad. Salem">Douar Olad. Salem</option>
                                    <option value="Douar ’Ayn Dfali">Douar 'Ayn Dfali</option>
                                    <option value="Skoura">Skoura</option>
                                    <option value="Setti Fatma">Setti Fatma</option>
                                    <option value="Gueznaia">Gueznaia</option>
                                    <option value="Zawyat an Nwaçer">Zawyat an Nwacer</option>
                                    <option value="Khenichet-sur Ouerrha">Khenichet-sur Ouerrha</option>
                                    <option value="Douar Ouled Ayad">Douar Ouled Ayad</option>
                                    <option value="Oulad Hassoune">Oulad Hassoune</option>
                                    <option value="Ayt Mohamed">Ayt Mohamed</option>
                                    <option value="Bni Frassen">Bni Frassen</option>
                                    <option value="Tighedouine">Tighedouine</option>
                                    <option value="Sidi Ifni">Sidi Ifni</option>
                                    <option value="Alnif">Alnif</option>
                                    <option value="Souk Tlet El Gharb">Souk Tlet El Gharb</option>
                                    <option value="Afourar">Afourar</option>
                                    <option value="Selouane">Selouane</option>
                                    <option value="Imi-n-Tanout">Imi-n-Tanout</option>
                                    <option value="El Ksiba">El Ksiba</option>
                                    <option value="Tidili Masfiywat">Tidili Masfiywat</option>
                                    <option value="Amizmiz">Amizmiz</option>
                                    <option value="Tamgrout">Tamgrout</option>
                                    <option value="Sidi Rahal">Sidi Rahal</option>
                                    <option value="Asni">Asni</option>
                                    <option value="Oulad Embarek">Oulad Embarek</option>
                                    <option value="Al Brouj">Al Brouj</option>
                                    <option value="Imi n’Oulaoun">Imi n'Oulaoun</option>
                                    <option value="Saka">Saka</option>
                                    <option value="Bni Rzine">Bni Rzine</option>
                                    <option value="Sidi Chiker">Sidi Chiker</option>
                                    <option value="Douar Lamrabih">Douar Lamrabih</option>
                                    <option value="Sidi Jaber">Sidi Jaber</option>
                                    <option value="Station des Essais M.V.A">Station des Essais M.V.A</option>
                                    <option value="Aïn Cheggag">Ain Cheggag</option>
                                    <option value="Jdour">Jdour</option>
                                    <option value="Imouzzer Kandar">Imouzzer Kandar</option>
                                    <option value="’Ali Ben Sliman">Ali Ben Sliman</option>
                                    <option value="El Mansouria">El Mansouria</option>
                                    <option value="Tarhzirt">Tarhzirt</option>
                                    <option value="Had Zraqtane">Had Zraqtane</option>
                                    <option value="Aït Tamlil">Ait Tamlil</option>
                                    <option value="Zaouïa Aït Ishak">Zaouia Ait Ishak</option>
                                    <option value="Jnane Bouih">Jnane Bouih</option>
                                    <option value="Oulad Salmane">Oulad Salmane</option>
                                    <option value="Ait Bousarane">Ait Bousarane</option>
                                    <option value="Sebt Gzoula">Sebt Gzoula</option>
                                    <option value="Sidi Redouane">Sidi Redouane</option>
                                    <option value="Karia Ba Mohamed">Karia Ba Mohamed</option>
                                    <option value="Ait Ben Daoudi">Ait Ben Daoudi</option>
                                    <option value="Beni Zouli">Beni Zouli</option>
                                    <option value="Oulmes">Oulmes</option>
                                    <option value="Jbabra">Jbabra</option>
                                    <option value="Sidi Allal Tazi">Sidi Allal Tazi</option>
                                    <option value="Tamri">Tamri</option>
                                    <option value="Tata">Tata</option>
                                    <option value="Chouafaa">Chouafaa</option>
                                    <option value="Foum el Anser">Foum el Anser</option>
                                    <option value="Lamrasla">Lamrasla</option>
                                    <option value="Aït Bouchta">Ait Bouchta</option>
                                    <option value="Ribat Al Khayr">Ribat Al Khayr</option>
                                    <option value="Bouarouss">Bouarouss</option>
                                    <option value="Ikniwn">Ikniwn</option>
                                    <option value="Ghouazi">Ghouazi</option>
                                    <option value="Outat Oulad Al Haj">Outat Oulad Al Haj</option>
                                    <option value="Al Qbab">Al Qbab</option>
                                    <option value="Douar Oulad Mbarek">Douar Oulad Mbarek</option>
                                    <option value="Qal’at Mgouna">Qal'at Mgouna</option>
                                    <option value="Laatatra">Laatatra</option>
                                    <option value="Aït Majdane">Ait Majdane</option>
                                    <option value="Agourai">Agourai</option>
                                    <option value="Awlouz">Awlouz</option>
                                    <option value="Sahel">Sahel</option>
                                    <option value="Ketama">Ketama</option>
                                    <option value="Dar Chaifat">Dar Chaifat</option>
                                    <option value="Galaz">Galaz</option>
                                    <option value="Milla’ab">Milla'ab</option>
                                    <option value="Talsint">Talsint</option>
                                    <option value="Tamallalt">Tamallalt</option>
                                    <option value="Sidi Yakoub">Sidi Yakoub</option>
                                    <option value="Tagounite">Tagounite</option>
                                    <option value="Knemis Dades">Knemis Dades</option>
                                    <option value="Oulad Amrane">Oulad Amrane</option>
                                    <option value="Ratba">Ratba</option>
                                    <option value="Ouaouzgane">Ouaouzgane</option>
                                    <option value="Sidi Lamine">Sidi Lamine</option>
                                    <option value="Douar Tabouda">Douar Tabouda</option>
                                    <option value="Sidi Ettiji">Sidi Ettiji</option>
                                    <option value="Zirara">Zirara</option>
                                    <option value="Tirhassaline">Tirhassaline</option>
                                    <option value="Douar Azla">Douar Azla</option>
                                    <option value="Timezgana">Timezgana</option>
                                    <option value="’Ayn Bni Mathar">Ayn Bni Mathar</option>
                                    <option value="Zegzel">Zegzel</option>
                                    <option value="Bouchabel">Bouchabel</option>
                                    <option value="Masmouda">Masmouda</option>
                                    <option value="Skhour Rehamna">Skhour Rehamna</option>
                                    <option value="Bni Tajjit">Bni Tajjit</option>
                                    <option value="Bni Quolla">Bni Quolla</option>
                                    <option value="Khat Azakane">Khat Azakane</option>
                                    <option value="L’Oulja">L'Oulja</option>
                                    <option value="Haddada">Haddada</option>
                                    <option value="Aïn Mediouna">Ain Mediouna</option>
                                    <option value="Ezzhiliga">Ezzhiliga</option>
                                    <option value="Tamazouzt">Tamazouzt</option>
                                    <option value="Sidi Allal el Bahraoui">Sidi Allal el Bahraoui</option>
                                    <option value="Ait Yazza">Ait Yazza</option>
                                    <option value="Ras el Oued">Ras el Oued</option>
                                    <option value="Aç-çahrij">Ac-cahrij</option>
                                    <option value="Wawizaght">Wawizaght</option>
                                    <option value="Ifrane">Ifrane</option>
                                    <option value="Madagh">Madagh</option>
                                    <option value="Anazzou">Anazzou</option>
                                    <option value="Moul El Bergui">Moul El Bergui</option>
                                    <option value="Tendrara">Tendrara</option>
                                    <option value="Oulad Bou Rahmoun">Oulad Bou Rahmoun</option>
                                    <option value="Driouch">Driouch</option>
                                    <option value="Tazert">Tazert</option>
                                    <option value="Aïn Jemaa">Ain Jemaa</option>
                                    <option value="Sabbah">Sabbah</option>
                                    <option value="Ben Taieb">Ben Taieb</option>
                                    <option value="Tazzarine">Tazzarine</option>
                                    <option value="Midar">Midar</option>
                                    <option value="Oued Jdida">Oued Jdida</option>
                                    <option value="Esbiaat">Esbiaat</option>
                                    <option value="Douar Souk L‘qolla">Douar Souk L`qolla</option>
                                    <option value="Aghbal">Aghbal</option>
                                    <option value="Tabant">Tabant</option>
                                    <option value="Bni Darkoul">Bni Darkoul</option>
                                    <option value="Gourrama">Gourrama</option>
                                    <option value="Bhalil">Bhalil</option>
                                    <option value="Nzalat Laadam">Nzalat Laadam</option>
                                    <option value="Ighrem n’Ougdal">Ighrem n'Ougdal</option>
                                    <option value="Oulad Driss">Oulad Driss</option>
                                    <option value="Zemamra">Zemamra</option>
                                    <option value="Ayt ’Attou ou L’Arbi">Ayt 'Attou ou L'Arbi</option>
                                    <option value="Boula’wane">Boula'wane</option>
                                    <option value="Bezou">Bezou</option>
                                    <option value="Sidi Azzouz">Sidi Azzouz</option>
                                    <option value="Ourtzagh">Ourtzagh</option>
                                    <option value="Zemrane">Zemrane</option>
                                    <option value="Tagalft">Tagalft</option>
                                    <option value="Temsamane">Temsamane</option>
                                    <option value="Tounfit">Tounfit</option>
                                    <option value="Ihaddadene">Ihaddadene</option>
                                    <option value="Zaouiat Moulay Bouchta El Khammar">Zaouiat Moulay Bouchta El Khammar</option>
                                    <option value="Tafrant">Tafrant</option>
                                    <option value="Douar Hammadi">Douar Hammadi</option>
                                    <option value="Bou Izakarn">Bou Izakarn</option>
                                    <option value="Zayda">Zayda</option>
                                    <option value="Sidi Abdelkarim">Sidi Abdelkarim</option>
                                    <option value="Talwat">Talwat</option>
                                    <option value="Oulad Chikh">Oulad Chikh</option>
                                    <option value="Khmis Sidi al ’Aydi">Khmis Sidi al 'Aydi</option>
                                    <option value="Douar Lehgagcha">Douar Lehgagcha</option>
                                    <option value="Tamsaout">Tamsaout</option>
                                    <option value="Aghbala">Aghbala</option>
                                    <option value="Sidi Yahia">Sidi Yahia</option>
                                    <option value="Mqam at Tolba">Mqam at Tolba</option>
                                    <option value="Kissane Ltouqi">Kissane Ltouqi</option>
                                    <option value="Tahannawt">Tahannawt</option>
                                    <option value="Reggada">Reggada</option>
                                    <option value="El Kansera">El Kansera</option>
                                    <option value="Asjen">Asjen</option>
                                    <option value="Ksar Sghir">Ksar Sghir</option>
                                    <option value="Sebt Bni Garfett">Sebt Bni Garfett</option>
                                    <option value="Oulad Rahmoun">Oulad Rahmoun</option>
                                    <option value="Bni Khloug">Bni Khloug</option>
                                    <option value="Bou Adel">Bou Adel</option>
                                    <option value="Guisser">Guisser</option>
                                    <option value="Tizgane">Tizgane</option>
                                    <option value="Kasba Tanora">Kasba Tanora</option>
                                    <option value="Souakene">Souakene</option>
                                    <option value="Teroual">Teroual</option>
                                    <option value="Oulad Ouchchih">Oulad Ouchchih</option>
                                    <option value="Laamarna">Laamarna</option>
                                    <option value="Zag">Zag</option>
                                    <option value="Ounagha">Ounagha</option>
                                    <option value="Aït Youssef Ou Ali">Ait Youssef Ou Ali</option>
                                    <option value="Zawiat Moulay Brahim">Zawiat Moulay Brahim</option>
                                    <option value="Bni Drar">Bni Drar</option>
                                    <option value="Jaidte Lbatma">Jaidte Lbatma</option>
                                    <option value="Boumalne">Boumalne</option>
                                    <option value="Oulad Aïssa">Oulad Aissa</option>
                                    <option value="Oulad Fares">Oulad Fares</option>
                                    <option value="Oulad Amrane el Mekki">Oulad Amrane el Mekki</option>
                                    <option value="Gharbia">Gharbia</option>
                                    <option value="Nkheila">Nkheila</option>
                                    <option value="Tissa">Tissa</option>
                                    <option value="Ain Kansara">Ain Kansara</option>
                                    <option value="Malloussa">Malloussa</option>
                                    <option value="Aj Jourf">Aj Jourf</option>
                                    <option value="Steha">Steha</option>
                                    <option value="Mayate">Mayate</option>
                                    <option value="Oulad Daoud">Oulad Daoud</option>
                                    <option value="Souq Jamaa Fdalate">Souq Jamaa Fdalate</option>
                                    <option value="Al Fayd">Al Fayd</option>
                                    <option value="Ain Beida">Ain Beida</option>
                                    <option value="El Arba Des Bir Lenni">El Arba Des Bir Lenni</option>
                                    <option value="Matmata">Matmata</option>
                                    <option value="Aït I’yach">Ait I'yach</option>
                                    <option value="Tizi Nisly">Tizi Nisly</option>
                                    <option value="Sidi Amer El Hadi">Sidi Amer El Hadi</option>
                                    <option value="Moulay Driss Zerhoun">Moulay Driss Zerhoun</option>
                                    <option value="Tifni">Tifni</option>
                                    <option value="Al M’aziz">Al M'aziz</option>
                                    <option value="Tamezmout">Tamezmout</option>
                                    <option value="Oulad Friha">Oulad Friha</option>
                                    <option value="Sidi Moussa Ben Ali">Sidi Moussa Ben Ali</option>
                                    <option value="Jamaat Shaim">Jamaat Shaim</option>
                                    <option value="Sidi Kasem">Sidi Kasem</option>
                                    <option value="Derdara">Derdara</option>
                                    <option value="Dzouz">Dzouz</option>
                                    <option value="Timahdit">Timahdit</option>
                                    <option value="Tawnza">Tawnza</option>
                                    <option value="Bouabout">Bouabout</option>
                                    <option value="Douar Trougout">Douar Trougout</option>
                                    <option value="El Khemis des Beni Chegdal">El Khemis des Beni Chegdal</option>
                                    <option value="Lahfayr">Lahfayr</option>
                                    <option value="Ain Legdah">Ain Legdah</option>
                                    <option value="Ahlaf">Ahlaf</option>
                                    <option value="Amdel">Amdel</option>
                                    <option value="Douar Oulad Naoual">Douar Oulad Naoual</option>
                                    <option value="Laqraqra">Laqraqra</option>
                                    <option value="Douar Sgarta">Douar Sgarta</option>
                                    <option value="Lamsabih">Lamsabih</option>
                                    <option value="Tilmi">Tilmi</option>
                                    <option value="El Ghourdane">El Ghourdane</option>
                                    <option value="Ouaklim Oukider">Ouaklim Oukider</option>
                                    <option value="Sidi Abdellah Ben Taazizt">Sidi Abdellah Ben Taazizt</option>
                                    <option value="Touama">Touama</option>
                                    <option value="Iazizatene">Iazizatene</option>
                                    <option value="Zaouiet Says">Zaouiet Says</option>
                                    <option value="Douar Jwalla">Douar Jwalla</option>
                                    <option value="Boujediane">Boujediane</option>
                                    <option value="Iygli">Iygli</option>
                                    <option value="Takad Sahel">Takad Sahel</option>
                                    <option value="Kariat Ben Aouda">Kariat Ben Aouda</option>
                                    <option value="Oued Amlil">Oued Amlil</option>
                                    <option value="Itzer">Itzer</option>
                                    <option value="Jafra">Jafra</option>
                                    <option value="Figuig">Figuig</option>
                                    <option value="Imi Mokorn">Imi Mokorn</option>
                                    <option value="Foum Jam’a">Foum Jam'a</option>
                                    <option value="Douar Bouchfaa">Douar Bouchfaa</option>
                                    <option value="Tanant">Tanant</option>
                                    <option value="Taouloukoult">Taouloukoult</option>
                                    <option value="Arbaa Sahel">Arbaa Sahel</option>
                                    <option value="Tamanar">Tamanar</option>
                                    <option value="Abadou">Abadou</option>
                                    <option value="Sidi Bousber">Sidi Bousber</option>
                                    <option value="Agdz">Agdz</option>
                                    <option value="Had Laaounate">Had Laaounate</option>
                                    <option value="Amtar">Amtar</option>
                                    <option value="Hetane">Hetane</option>
                                    <option value="Zawyat Ahançal">Zawyat Ahancal</option>
                                    <option value="Aïn Zora">Ain Zora</option>
                                    <option value="Souq Sebt Says">Souq Sebt Says</option>
                                    <option value="Toundout">Toundout</option>
                                    <option value="Mokrisset">Mokrisset</option>
                                    <option value="Tourza">Tourza</option>
                                    <option value="Aït Hani">Ait Hani</option>
                                    <option value="Tnine Sidi Lyamani">Tnine Sidi Lyamani</option>
                                    <option value="Tiztoutine">Tiztoutine</option>
                                    <option value="Tilougguit">Tilougguit</option>
                                    <option value="Sidi Abdallah">Sidi Abdallah</option>
                                    <option value="Dar El Kebdani">Dar El Kebdani</option>
                                    <option value="Douar Echbanat">Douar Echbanat</option>
                                    <option value="Brikcha">Brikcha</option>
                                    <option value="Oulad Slim">Oulad Slim</option>
                                    <option value="Sidi Rahhal">Sidi Rahhal</option>
                                    <option value="Awfouss">Awfouss</option>
                                    <option value="Tiddas">Tiddas</option>
                                    <option value="Beni Oulid">Beni Oulid</option>
                                    <option value="Jaqma">Jaqma</option>
                                    <option value="Bounaamane">Bounaamane</option>
                                    <option value="Ksar Lmajaz">Ksar Lmajaz</option>
                                    <option value="Aghbalou n’Kerdous">Aghbalou n'Kerdous</option>
                                    <option value="Sgamna">Sgamna</option>
                                    <option value="Kenafif">Kenafif</option>
                                    <option value="La’tamna">La'tamna</option>
                                    <option value="Jemaat Oulad Mhamed">Jemaat Oulad Mhamed</option>
                                    <option value="Tissaf">Tissaf</option>
                                    <option value="Za’roura">Za'roura</option>
                                    <option value="Ech Chaïbat">Ech Chaibat</option>
                                    <option value="Zaggota">Zaggota</option>
                                    <option value="Taghbalt">Taghbalt</option>
                                    <option value="’Aïn Leuh">Ain Leuh</option>
                                    <option value="Tarhjicht">Tarhjicht</option>
                                    <option value="Oued Laou">Oued Laou</option>
                                    <option value="Boudinar">Boudinar</option>
                                    <option value="Kourimat">Kourimat</option>
                                    <option value="Outa Bouabane">Outa Bouabane</option>
                                    <option value="Tafersit">Tafersit</option>
                                    <option value="Saidia">Saidia</option>
                                    <option value="Tadla">Tadla</option>
                                    <option value="Aklim">Aklim</option>
                                    <option value="Aghbalou Aqourar">Aghbalou Aqourar</option>
                                    <option value="Sidi Ahmed El Khadir">Sidi Ahmed El Khadir</option>
                                    <option value="Douar Lehouifrat">Douar Lehouifrat</option>
                                    <option value="Bni Boufrah">Bni Boufrah</option>
                                    <option value="Douar Messassa">Douar Messassa</option>
                                    <option value="Oulad Imloul">Oulad Imloul</option>
                                    <option value="Sidi Bou Othmane">Sidi Bou Othmane</option>
                                    <option value="Tatoufet">Tatoufet</option>
                                    <option value="Bni Gmil">Bni Gmil</option>
                                    <option value="Zawyat Sidi Ben Hamdoun">Zawyat Sidi Ben Hamdoun</option>
                                    <option value="El Amim">El Amim</option>
                                    <option value="Mhâjâr">Mhajar</option>
                                    <option value="Sidi El Hattab">Sidi El Hattab</option>
                                    <option value="Tissint">Tissint</option>
                                    <option value="Gammasa">Gammasa</option>
                                    <option value="Laghzawna">Laghzawna</option>
                                    <option value="Ameskroud">Ameskroud</option>
                                    <option value="Douar Ezzerarda">Douar Ezzerarda</option>
                                    <option value="Tanakoub">Tanakoub</option>
                                    <option value="Oulad Cherif">Oulad Cherif</option>
                                    <option value="Sidi Lahsene">Sidi Lahsene</option>
                                    <option value="Douar Snada">Douar Snada</option>
                                    <option value="Chtiba">Chtiba</option>
                                    <option value="Sidi Ouassay">Sidi Ouassay</option>
                                    <option value="Bir Tam Tam">Bir Tam Tam</option>
                                    <option value="Smimou">Smimou</option>
                                    <option value="Mwaline al Oued">Mwaline al Oued</option>
                                    <option value="Gtarna">Gtarna</option>
                                    <option value="Iguidiy">Iguidiy</option>
                                    <option value="Bni Sidel">Bni Sidel</option>
                                    <option value="Had Dra">Had Dra</option>
                                    <option value="Foum Zguid">Foum Zguid</option>
                                    <option value="Zawyat Sidi al Mekki">Zawyat Sidi al Mekki</option>
                                    <option value="Iskourane">Iskourane</option>
                                    <option value="Msemrir">Msemrir</option>
                                    <option value="Ait Ikkou">Ait Ikkou</option>
                                    <option value="Imilchil">Imilchil</option>
                                    <option value="Aït Ouaoumana">Ait Ouaoumana</option>
                                    <option value="Bouhlou">Bouhlou</option>
                                    <option value="Agadir Melloul">Agadir Melloul</option>
                                    <option value="Iaboutene">Iaboutene</option>
                                    <option value="Amarzgane">Amarzgane</option>
                                    <option value="El Marmouta">El Marmouta</option>
                                    <option value="Oualidia">Oualidia</option>
                                    <option value="Sidi Dahbi">Sidi Dahbi</option>
                                    <option value="Sidi el Mokhfi">Sidi el Mokhfi</option>
                                    <option value="Hassi Berkane">Hassi Berkane</option>
                                    <option value="Tiqqi">Tiqqi</option>
                                    <option value="Tleta Taghramt">Tleta Taghramt</option>
                                    <option value="Ben Qarrich">Ben Qarrich</option>
                                    <option value="Mirleft">Mirleft</option>
                                    <option value="Lakhzazra">Lakhzazra</option>
                                    <option value="Lambarkiyine">Lambarkiyine</option>
                                    <option value="Oulad Khallouf">Oulad Khallouf</option>
                                    <option value="Iksane">Iksane</option>
                                    <option value="Talambote">Talambote</option>
                                    <option value="Laanoussar">Laanoussar</option>
                                    <option value="Tizoual">Tizoual</option>
                                    <option value="Ait Ali Mimoune">Ait Ali Mimoune</option>
                                    <option value="Moulay Bou ’azza">Moulay Bou 'azza</option>
                                    <option value="Boured">Boured</option>
                                    <option value="Kerrouchen">Kerrouchen</option>
                                    <option value="Ghassat">Ghassat</option>
                                    <option value="Nzalat Bni Amar">Nzalat Bni Amar</option>
                                    <option value="Douar Mezaoura">Douar Mezaoura</option>
                                    <option value="Imoulas">Imoulas</option>
                                    <option value="Mrizig">Mrizig</option>
                                    <option value="Aït Tagalla">Ait Tagalla</option>
                                    <option value="Tarfaya">Tarfaya</option>
                                    <option value="Souk Khmis Bni Arouss">Souk Khmis Bni Arouss</option>
                                    <option value="Oulad Chbana">Oulad Chbana</option>
                                    <option value="Meghraoua">Meghraoua</option>
                                    <option value="Melqa el Ouidane">Melqa el Ouidane</option>
                                    <option value="Fifi">Fifi</option>
                                    <option value="Kef el Rhar">Kef el Rhar</option>
                                    <option value="Imi n-Tlit">Imi n-Tlit</option>
                                    <option value="Sidi ’Ali Bou Aqba">Sidi 'Ali Bou Aqba</option>
                                    <option value="El Meghassine">El Meghassine</option>
                                    <option value="Mezguitem">Mezguitem</option>
                                    <option value="Tafraoutane">Tafraoutane</option>
                                    <option value="Arazane">Arazane</option>
                                    <option value="Ida Ou Azza">Ida Ou Azza</option>
                                    <option value="Moulay Abdelkader">Moulay Abdelkader</option>
                                    <option value="Had Kourt">Had Kourt</option>
                                    <option value="Talat-n-Ya’qoub">Talat-n-Ya'qoub</option>
                                    <option value="Lalla Takerkoust">Lalla Takerkoust</option>
                                    <option value="Sidi Ahmed Ben Aissa">Sidi Ahmed Ben Aissa</option>
                                    <option value="Timezgadiouine">Timezgadiouine</option>
                                    <option value="Jouamaa">Jouamaa</option>
                                    <option value="Ouirgane">Ouirgane</option>
                                    <option value="Al Orjane">Al Orjane</option>
                                    <option value="Zinat">Zinat</option>
                                    <option value="Anzi">Anzi</option>
                                    <option value="Oulad Hassoune">Oulad Hassoune</option>
                                    <option value="Aït Hadi">Ait Hadi</option>
                                    <option value="Mhamid el Rhozlane">Mhamid el Rhozlane</option>
                                    <option value="Beni Oual Sehira">Beni Oual Sehira</option>
                                    <option value="Ras Kebdana">Ras Kebdana</option>
                                    <option value="Ait Hammou">Ait Hammou</option>
                                    <option value="Adassil">Adassil</option>
                                    <option value="Tafetachte">Tafetachte</option>
                                    <option value="Douar Brarba">Douar Brarba</option>
                                    <option value="Taznakht">Taznakht</option>
                                    <option value="Tazoult">Tazoult</option>
                                    <option value="Askawn">Askawn</option>
                                    <option value="Douar Oulad Boussaken">Douar Oulad Boussaken</option>
                                    <option value="Lahouarta">Lahouarta</option>
                                    <option value="Douar Tassift">Douar Tassift</option>
                                    <option value="Sidi al Ghandour">Sidi al Ghandour</option>
                                    <option value="Oued El Makhazine">Oued El Makhazine</option>
                                    <option value="Sidi ’Allal al Mçader">Sidi 'Allal al Mcader</option>
                                    <option value="Oulad Cherki">Oulad Cherki</option>
                                    <option value="Boulemane">Boulemane</option>
                                    <option value="Bou Iferda">Bou Iferda</option>
                                    <option value="Arbaa Ayacha">Arbaa Ayacha</option>
                                    <option value="Douar Drissiine">Douar Drissiine</option>
                                    <option value="Ouardana">Ouardana</option>
                                    <option value="Nkob">Nkob</option>
                                    <option value="Assoul">Assoul</option>
                                    <option value="Rouadi">Rouadi</option>
                                    <option value="Douar Oulad Amer">Douar Oulad Amer</option>
                                    <option value="Timlilt">Timlilt</option>
                                    <option value="Oued Naanaa">Oued Naanaa</option>
                                    <option value="Assebbab">Assebbab</option>
                                    <option value="Sidi Yahia Sawad">Sidi Yahia Sawad</option>
                                    <option value="Ichemrarn">Ichemrarn</option>
                                    <option value="Mzefroune">Mzefroune</option>
                                    <option value="Zaouiat Sidi Hammou Ben Hmida">Zaouiat Sidi Hammou Ben Hmida</option>
                                    <option value="Taliwine">Taliwine</option>
                                    <option value="Oulad Sbih">Oulad Sbih</option>
                                    <option value="Assais">Assais</option>
                                    <option value="Azgour">Azgour</option>
                                    <option value="Tizi Ouzli">Tizi Ouzli</option>
                                    <option value="El Maader El Kabir">El Maader El Kabir</option>
                                    <option value="Ait Said">Ait Said</option>
                                    <option value="Akka">Akka</option>
                                    <option value="Assays">Assays</option>
                                    <option value="Ghafsaï">Ghafsai</option>
                                    <option value="Mejji">Mejji</option>
                                    <option value="Znada">Znada</option>
                                    <option value="Douar el Caïd el Gueddari">Douar el Caid el Gueddari</option>
                                    <option value="Zerkat">Zerkat</option>
                                    <option value="Timoulilt">Timoulilt</option>
                                    <option value="Khmis Sidi Yahia">Khmis Sidi Yahia</option>
                                    <option value="Ijoukak">Ijoukak</option>
                                    <option value="Douar Oulad Mkoudou">Douar Oulad Mkoudou</option>
                                    <option value="Et Tnine des Beni Ammart">Et Tnine des Beni Ammart</option>
                                    <option value="Tafingoult">Tafingoult</option>
                                    <option value="Douar El Gouzal">Douar El Gouzal</option>
                                    <option value="Ras Ijerri">Ras Ijerri</option>
                                    <option value="Douar Tassila Imassouane">Douar Tassila Imassouane</option>
                                    <option value="Mestigmer">Mestigmer</option>
                                    <option value="Ida Ou Gaïlal">Ida Ou Gailal</option>
                                    <option value="Ras Tabouda">Ras Tabouda</option>
                                    <option value="Saïdat">Saidat</option>
                                    <option value="Tancherfi">Tancherfi</option>
                                    <option value="Tafraout">Tafraout</option>
                                    <option value="Tiyghmi">Tiyghmi</option>
                                    <option value="Beni Sidal Louta">Beni Sidal Louta</option>
                                    <option value="Douar Oulad Jaber">Douar Oulad Jaber</option>
                                    <option value="Souq at Tlata des Loulad">Souq at Tlata des Loulad</option>
                                    <option value="Tazouta">Tazouta</option>
                                    <option value="Amrharas">Amrharas</option>
                                    <option value="Bir Anzarane">Bir Anzarane</option>
                                    <option value="Ida Ou Moumene">Ida Ou Moumene</option>
                                    <option value="Kechoulah">Kechoulah</option>
                                    <option value="Sidi Mbark">Sidi Mbark</option>
                                    <option value="Moulay Bouzarqtoune">Moulay Bouzarqtoune</option>
                                    <option value="Tazemmourt">Tazemmourt</option>
                                    <option value="Oulad Amghar">Oulad Amghar</option>
                                    <option value="Bni Abdellah">Bni Abdellah</option>
                                    <option value="Douar Tazrout">Douar Tazrout</option>
                                    <option value="Douar Oulad Bou Krae El Fouqani">Douar Oulad Bou Krae El Fouqani</option>
                                    <option value="Tinzart">Tinzart</option>
                                    <option value="El Arba Bouzemmour">El Arba Bouzemmour</option>
                                    <option value="Aït Ouakrim">Ait Ouakrim</option>
                                    <option value="Amersid">Amersid</option>
                                    <option value="Ighil">Ighil</option>
                                    <option value="Sidi Harazem">Sidi Harazem</option>
                                    <option value="Bni Hadifa">Bni Hadifa</option>
                                    <option value="Aït el Farsi">Ait el Farsi</option>
                                    <option value="Oulad Zarrad">Oulad Zarrad</option>
                                    <option value="El Aargub">El Aargub</option>
                                    <option value="Tichla">Tichla</option>
                                    <option value="Dhar Souk">Dhar Souk</option>
                                    <option value="Ait Ban">Ait Ban</option>
                                    <option value="Tafadna">Tafadna</option>
                                    <option value="Mechra-Hommadi">Mechra-Hommadi</option>
                                    <option value="Jemaat Moul Blad">Jemaat Moul Blad</option>
                                    <option value="Ajdir">Ajdir</option>
                                    <option value="Guenfouda">Guenfouda</option>
                                    <option value="Imigdal">Imigdal</option>
                                    <option value="Bine Al Widane">Bine Al Widane</option>
                                    <option value="Imouzzer des Ida ou Tanane">Imouzzer des Ida ou Tanane</option>
                                    <option value="Taghazout">Taghazout</option>
                                    <option value="Taliouîne">Taliouine</option>
                                    <option value="Tizagzawine">Tizagzawine</option>
                                    <option value="Timzguida Ouftas">Timzguida Ouftas</option>
                                    <option value="Izmorene">Izmorene</option>
                                    <option value="Tiouli">Tiouli</option>
                                    <option value="Akarma">Akarma</option>
                                    <option value="Douar Oulad Amer Leqliaa">Douar Oulad Amer Leqliaa</option>
                                    <option value="Aghbar">Aghbar</option>
                                    <option value="Bigoudine">Bigoudine</option>
                                    <option value="Oulad Messaoud">Oulad Messaoud</option>
                                    <option value="Fezouane">Fezouane</option>
                                    <option value="Bab Boudir">Bab Boudir</option>
                                    <option value="Reçani">Recani</option>
                                    <option value="Moulay Driss Aghbal">Moulay Driss Aghbal</option>
                                    <option value="Azrar">Azrar</option>
                                    <option value="Uad Damran">Uad Damran</option>
                                    <option value="Oulad ’Azzouz">Oulad 'Azzouz</option>
                                    <option value="Bou Zemou">Bou Zemou</option>
                                    <option value="Douar Oulad Bouziane">Douar Oulad Bouziane</option>
                                    <option value="Douar El Mellaliyine">Douar El Mellaliyine</option>
                                    <option value="Alougoum">Alougoum</option>
                                    <option value="Matarka">Matarka</option>
                                    <option value="Ain Bida">Ain Bida</option>
                                    <option value="Mzizal">Mzizal</option>
                                    <option value="Bghaghza">Bghaghza</option>
                                    <option value="Sidi Bettach">Sidi Bettach</option>
                                    <option value="Outerbat">Outerbat</option>
                                    <option value="Taouz">Taouz</option>
                                    <option value="Beni Khaled">Beni Khaled</option>
                                    <option value="Saddina">Saddina</option>
                                    <option value="Adis">Adis</option>
                                    <option value="Akka Irene">Akka Irene</option>
                                    <option value="Tadighoust">Tadighoust</option>
                                    <option value="Oum Azza">Oum Azza</option>
                                    <option value="Oulad Aïssa">Oulad Aissa</option>
                                    <option value="Zawyat Sidi Hamza">Zawyat Sidi Hamza</option>
                                    <option value="Aït Athmane">Ait Athmane</option>
                                    <option value="Timoulay Izder">Timoulay Izder</option>
                                    <option value="Tafetchna">Tafetchna</option>
                                    <option value="Ez Zinat">Ez Zinat</option>
                                    <option value="Souk el Had-des Beni-Batao">Souk el Had-des Beni-Batao</option>
                                    <option value="Lemsid">Lemsid</option>
                                </select>
                            </div>
                        </div> -->
                        <div class="row">
                            <div class="col-md-2">
                                <label>Adresse</label>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control mb-3" placeholder="Adresse" rows="2" name="adresse" id="adresse" autocomplete="off" required />
                            </div>
                        </div>
                        <input type="hidden" name="id_product" id="id_product" value="{{$detailedProduct->id}}">
                        <input type="hidden" value="{{$detailedProduct->unit_price}}" name="unit_price" id="unit_price">
                        <input type="hidden" value="{{$detailedProduct->name}}" name="name_product" id="name_product">
                        <input type="hidden" value="1" name="quantite" id="quantiteModal">
                        <input type="hidden" value="" name="variant" id="variantVisitor">



                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-sm btn-primary" id="save_guest_order" onclick="ToggleButtonWhenSubmit(true)">
                                <span id="span_save">{{translate('Save')}}</span>
                                <span style="display: none;" id="span_loading"> <i class="fa fa-spinner fa-spin"></i>Loading</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
@if (session('success'))
<script>
    window.addEventListener('load', function() {
        Swal.fire({
            title: 'Commande enregistrée avec succès!',
            icon: 'success'
        });
    });

    ToggleButtonWhenSubmit(false);
</script>
@endif
<script>
    function updateValueQtyforGuest(val) {
        $("#quantiteModal").val(val);
    }

   $("input.varianteRadio").change(function (){
       updateVariantforOrderVisitor();
   })

    function updateVariantforOrderVisitor() {
        var variant = "";
        if ($("input[name='color']:checked").val() != null) {
            variant += $("input[name='color']:checked").val();
            variant = variant.replace(/\s/g,'');
        }
        if ($("input.varianteRadio:checked").val() != null) {
            // for attribute size, name is : attribute_id_1
            // variant += "-" + $("input.varianteRadio:checked").val();
            variant += $("input.varianteRadio:checked").val();
            variant = variant.replace(/\s/g,'');
        }
        $("#variantVisitor").val(variant);
    }
</script>

<script>
    function orderVisite() {
        updateVariantforOrderVisitor();
        $('#orderVisite').modal();

    }

    // Search select ... START

    var select = document.getElementById("selectCity");
    var input = document.getElementById("searchCity");
    input.addEventListener("keyup", function(event) {
        var options = select.options;
        for (var i = 0; i < options.length; i++) {
            if (options[i].text.toUpperCase().indexOf(input.value.toUpperCase()) > -1) {
                options[i].style.display = "block";
            } else {
                options[i].style.display = "none";
            }
        }
    });


    // Search select ... END

    function ToggleButtonWhenSubmit(flag) {
        var form = document.getElementById("formOrderGuest");
        if (form.checkValidity()) {
            // form is valid, show loading icon and submit form
            $('#save_guest_order').prop("disabled", flag);
            $('#span_loading').toggle();
            $('#span_save').toggle();
            $('#formOrderGuest').submit();
        }
    }


    function buyLikeVisitor() {
        let first_name = $("#first_name").val();
        let last_name = $("#last_name").val();
        let phone = $("#phone").val();
        let city = $("#city").val();
        let adresse = $("#adresse").val();
        let id_product = $("#id_product").val();
        let quantite = $("#quantiteModal").val();
        let name_product = $("#name_product").val();
        let unit_price = $("#unit_price").val();
        // alert(first_name +"  " +last_name+"  " +phone+"  " +city+"  " +adresse+"  " +id_product+"  " +quantite +"  "+ name_product+"  "+ unit_price);
    }
</script>


<script type="text/javascript">
    $(document).ready(function() {
        getVariantPrice();
    });

    function CopyToClipboard(e) {
        var url = $(e).data('url');
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(url).select();
        try {
            document.execCommand("copy");
            AIZ.plugins.notify('success', '{{ translate('
                Link copied to clipboard ') }}');
        } catch (err) {
            AIZ.plugins.notify('danger', '{{ translate('
                Oops, unable to copy ') }}');
        }
        $temp.remove();
        // if (document.selection) {
        //     var range = document.body.createTextRange();
        //     range.moveToElementText(document.getElementById(containerid));
        //     range.select().createTextRange();
        //     document.execCommand("Copy");

        // } else if (window.getSelection) {
        //     var range = document.createRange();
        //     document.getElementById(containerid).style.display = "block";
        //     range.selectNode(document.getElementById(containerid));
        //     window.getSelection().addRange(range);
        //     document.execCommand("Copy");
        //     document.getElementById(containerid).style.display = "none";

        // }
        // AIZ.plugins.notify('success', 'Copied');
    }

    function show_chat_modal() {
        @if(Auth::check())
        $('#chat_modal').modal('show');
        @else
        $('#login_modal').modal('show');
        @endif
    }

    // Pagination using ajax
    $(window).on('hashchange', function() {
        if (window.history.pushState) {
            window.history.pushState('', '/', window.location.pathname);
        } else {
            window.location.hash = '';
        }
    });

    $(document).ready(function() {
        $(document).on('click', '.product-queries-pagination .pagination a', function(e) {
            getPaginateData($(this).attr('href').split('page=')[1], 'query', 'queries-area');
            e.preventDefault();
        });
    });

    $(document).ready(function() {
        $(document).on('click', '.product-reviews-pagination .pagination a', function(e) {
            getPaginateData($(this).attr('href').split('page=')[1], 'review', 'reviews-area');
            e.preventDefault();
        });
    });

    function getPaginateData(page, type, section) {
        $.ajax({
            url: '?page=' + page,
            dataType: 'json',
            data: {
                type: type
            },
        }).done(function(data) {
            $('.' + section).html(data);
            location.hash = page;
        }).fail(function() {
            alert('Something went worng! Data could not be loaded.');
        });
    }
    // Pagination end

    function showImage(photo) {
        $('#image_modal img').attr('src', photo);
        $('#image_modal img').attr('data-src', photo);
        $('#image_modal').modal('show');
    }

    function bid_modal() {
        @if(Auth::check() && (isCustomer() || isSeller()))
        $('#bid_for_detail_product').modal('show');
        @elseif(Auth::check() && isAdmin())
        AIZ.plugins.notify('warning', '{{ translate("Sorry, Only customers & Sellers can Bid.") }}');
        @else
        $('#login_modal').modal('show');
        @endif
    }

    function product_review(product_id) {
        @if(Auth::check() && isCustomer())
        @if($review_status == 1)
        $.post('{{ route('
            product_review_modal ') }}', {
                _token: '{{ @csrf_token() }}',
                product_id: product_id
            },
            function(data) {
                $('#product-review-modal-content').html(data);
                $('#product-review-modal').modal('show', {
                    backdrop: 'static'
                });
                AIZ.extra.inputRating();
            });
        @else
        AIZ.plugins.notify('warning', '{{ translate("Sorry, You need to buy this product to give review.") }}');
        @endif
        @elseif(Auth::check() && !isCustomer())
        AIZ.plugins.notify('warning', '{{ translate("Sorry, Only customers can give review.") }}');
        @else
        $('#login_modal').modal('show');
        @endif
    }
</script>
@endsection
