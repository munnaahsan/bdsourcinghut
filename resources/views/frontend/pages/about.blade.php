@extends('frontend.layouts.app')
@section('seo_title',$page->seo_title ?: $page->title)
@section('seo_description',$page->seo_description ?: $page->excerpt)
@section('og_image',$page->heroImage?->url ?? asset('images/about/about-main.jpg'))
@section('content')
@include('frontend.partials.page-hero',['title'=>$page->body ?: $page->title,'eyebrow'=>$page->eyebrow,'description'=>$page->excerpt,'imageUrl'=>$page->heroImage?->url,'imageAlt'=>$page->heroImage?->alt_text])
@foreach($page->visibleSections as $section)@include('frontend.partials.section')@endforeach
<section class="about-capability-panel editorial-pad"><div class="container"><span class="eyebrow gold">WHAT WE CAN DO</span><h2 class="section-title">CONNECTED CAPABILITIES.</h2><div class="capability-grid">@foreach($services as $service)<a href="{{ route('services.show',$service->slug) }}"><span>{{ sprintf('%02d',$loop->iteration) }}</span><h3>{{ $service->title }}</h3><span>↗</span></a>@endforeach</div></div></section>
@include('frontend.partials.mini-cta')
@endsection
