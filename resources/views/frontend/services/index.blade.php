@extends('frontend.layouts.app')
@section('seo_title',$page->seo_title ?: 'Services | BD SOURCING HUT')
@section('seo_description',$page->seo_description ?: $page->excerpt)
@section('content')
@include('frontend.partials.page-hero',['title'=>$page->body ?: $page->title,'eyebrow'=>$page->eyebrow,'description'=>$page->excerpt,'imageUrl'=>$page->heroImage?->url])
<section class="services-index editorial-pad"><div class="container"><div class="eyebrow">OUR EXPERTISE</div>@forelse($services as $service)
 <a class="index-service reveal" href="{{ route('services.show',$service->slug) }}"><span class="index-service-number">{{ sprintf('%02d',$loop->iteration) }}</span><div><h2>{{ $service->title }}</h2><p>{{ $service->short_description }}</p></div><div class="index-service-image"><img loading="lazy" src="{{ $service->mainImage?->url ?? asset('images/hero/hero-detail.jpg') }}" alt="{{ $service->title }}"></div><span class="index-service-arrow">↗</span></a>
 @empty<p>No published services yet.</p>@endforelse</div></section>
@include('frontend.partials.mini-cta')
@foreach($page->visibleSections as $section)@include('frontend.partials.section')@endforeach
@endsection
