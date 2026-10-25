@extends('frontend.layouts.app')
@section('seo_title',$page->seo_title ?: $page->title)
@section('seo_description',$page->seo_description ?: $page->excerpt)
@section('content')
@include('frontend.partials.page-hero',['title'=>strtoupper($page->title),'eyebrow'=>$page->eyebrow,'description'=>$page->excerpt,'imageUrl'=>$page->heroImage?->url,'imageAlt'=>$page->heroImage?->alt_text])
@if($page->body)<section class="generic-body editorial-pad"><div class="container"><p>{{ $page->body }}</p></div></section>@endif
@foreach($page->visibleSections as $section)@include('frontend.partials.section')@endforeach
@include('frontend.partials.mini-cta')
@endsection
