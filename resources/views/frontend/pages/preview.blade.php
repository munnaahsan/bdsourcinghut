@extends('frontend.layouts.app')
@section('robots','noindex,nofollow')
@section('seo_title','Section Preview - BD SOURCING HUT')
@section('content')
<div class="preview-banner">ADMIN PREVIEW · {{ strtoupper($page->title) }} / {{ $section->section_key }} · <a href="{{ route('admin.sections.edit',$section) }}">BACK TO EDITOR ↗</a></div>
@include('frontend.partials.section')
@endsection
