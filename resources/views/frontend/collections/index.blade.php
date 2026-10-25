@extends('frontend.layouts.app')
@section('seo_title',$page->seo_title ?: 'Collections | BD SOURCING HUT')
@section('seo_description',$page->seo_description ?: $page->excerpt)
@section('content')
@include('frontend.partials.page-hero',['title'=>$page->body ?: $page->title,'eyebrow'=>$page->eyebrow,'description'=>$page->excerpt,'imageUrl'=>$page->heroImage?->url])
<section class="collections-index editorial-pad"><div class="container"><div class="section-heading-row"><div><span class="eyebrow">DISCOVER OUR WORLD</span><h2 class="section-title">EXPLORE BY CATEGORY.</h2></div><p class="heading-aside">From every stitch to every finish, start with a category and shape your collection.</p></div><div class="collection-mosaic">@forelse($collections as $collection)<a href="{{ route('collections.show',$collection->slug) }}" class="collection-tile tilt-card"><div class="collection-tile-photo"><img loading="lazy" src="{{ $collection->mainImage?->url ?? asset('images/hero/hero-garment.jpg') }}" alt="{{ $collection->title }}"></div><div class="collection-tile-label"><span>{{ sprintf('%02d',$loop->iteration) }} / CATEGORY</span><h3>{{ $collection->title }}</h3><span class="round-arrow">↗</span></div></a>@empty<p>No published collections yet.</p>@endforelse</div></div></section>
@include('frontend.partials.mini-cta')
@foreach($page->visibleSections as $section)@include('frontend.partials.section')@endforeach
@endsection
