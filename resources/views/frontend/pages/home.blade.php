@extends('frontend.layouts.app')
@section('seo_title',$page->seo_title ?: $page->title)
@section('seo_description',$page->seo_description ?: $page->excerpt)
@section('og_image',$page->heroImage?->url ?? asset('images/hero/hero-main.jpg'))
@section('content')
@foreach($page->visibleSections as $section)
 @include('frontend.partials.section',['section'=>$section])
@endforeach
@endsection
