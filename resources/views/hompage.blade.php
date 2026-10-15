@extends('layouts.app')

@section('title', 'Homepage')

@php
    $slides = [
        [
            'title' => 'Morning Compass',
            'text' => 'A simple starting point for your project.',
            'image' => '/images/banners/morning-compass.png',
        ],
        [
            'title' => 'Blue Mountain Mist',
            'text' => 'Built on Laravel with SQLite, so it runs anywhere with no setup.',
            'image' => '/images/banners/blue-mountain-mist.png',
        ],
        [
            'title' => 'Johor Jackfruit Gold',
            'text' => 'Add Vue or React to any page when you need more interactivity.',
            'image' => '/images/banners/johor-jackfruit-gold.png',
        ],
    ];
@endphp

@section('content')
<section>
    <div id="homeCarousel" class="carousel slide overflow-hidden shadow" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            @foreach ($slides as $i => $slide)
                <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="{{ $i }}"
                        @class(['active' => $i === 0]) @if ($i === 0) aria-current="true" @endif
                        aria-label="Slide {{ $i + 1 }}"></button>
            @endforeach
        </div>

        <div class="carousel-inner">
            @foreach ($slides as $i => $slide)
                <div @class(['carousel-item', 'active' => $i === 0])>
                    <img src="{{ $slide['image'] }}" class="d-block w-100 object-fit-cover" style="height: 600px" alt="{{ $slide['title'] }}">
                    <!-- <div class="carousel-caption">
                        <h2 class="fw-bold">{{ $slide['title'] }}</h2>
                        <p>{{ $slide['text'] }}</p>
                    </div> -->
                </div>
            @endforeach
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>

<section class="container py-5">
    <div class="product-title d-flex align-items-center justify-content-between gap-3 mb-4">
        <h1 class="h3 mb-0">Our Coffee Beans</h1>
        <a href="{{ route('products.index') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3">
            View more <span aria-hidden="true">&rarr;</span>
        </a>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        @foreach ($products as $product)
            <div class="col">
                @include('products.partials.card', ['product' => $product])
            </div>
        @endforeach
    </div>
</section>
@endsection
