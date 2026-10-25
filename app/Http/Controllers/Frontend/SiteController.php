<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\{Collection,Page,ProcessStep,Service};
use Illuminate\Http\Request;
class SiteController extends Controller {
 public function home(){
  $page=Page::with(['visibleSections.image','visibleSections.secondaryImage','heroImage'])->where('slug','home')->where('is_published',true)->firstOrFail();
  $services=Service::with('mainImage')->where('is_published',true)->orderBy('sort_order')->get();
  $collections=Collection::with('mainImage')->where('is_published',true)->orderBy('sort_order')->get();
  $steps=ProcessStep::where('is_visible',true)->orderBy('sort_order')->get();
  $featured=Collection::with(['mainImage','images'])->where('is_published',true)->where('is_featured',true)->first() ?? $collections->first();
  return view('frontend.pages.home',compact('page','services','collections','steps','featured'));
 }
 public function about(){
  $page=Page::with(['visibleSections.image','visibleSections.secondaryImage','heroImage'])->where('slug','about')->where('is_published',true)->firstOrFail();
  $services=Service::with('mainImage')->where('is_published',true)->orderBy('sort_order')->get();
  $collections=Collection::with('mainImage')->where('is_published',true)->orderBy('sort_order')->get();
  $steps=ProcessStep::where('is_visible',true)->orderBy('sort_order')->get();$featured=$collections->first();
  return view('frontend.pages.about',compact('page','services','collections','steps','featured'));
 }
 public function services(){
  $page=Page::with(['heroImage','visibleSections.image','visibleSections.secondaryImage'])->where('slug','services-overview')->where('is_published',true)->firstOrFail();
  $services=Service::with('mainImage')->where('is_published',true)->orderBy('sort_order')->get();
  $collections=Collection::with('mainImage')->where('is_published',true)->orderBy('sort_order')->get();$featured=$collections->first();
  $steps=ProcessStep::where('is_visible',true)->orderBy('sort_order')->get();
  return view('frontend.services.index',compact('services','page','collections','featured','steps'));
 }
 public function service(string $slug){
  $service=Service::with(['heroImage','mainImage'])->where('slug',$slug)->where('is_published',true)->firstOrFail();
  $related=Service::with('mainImage')->where('is_published',true)->where('id','!=',$service->id)->orderBy('sort_order')->limit(3)->get();
  return view('frontend.services.show',compact('service','related'));
 }
 public function collections(){
  $page=Page::with(['heroImage','visibleSections.image','visibleSections.secondaryImage'])->where('slug','collections-overview')->where('is_published',true)->firstOrFail();
  $collections=Collection::with('mainImage')->where('is_published',true)->orderBy('sort_order')->get();
  $services=Service::with('mainImage')->where('is_published',true)->orderBy('sort_order')->get();$featured=$collections->first();
  $steps=ProcessStep::where('is_visible',true)->orderBy('sort_order')->get();
  return view('frontend.collections.index',compact('collections','page','services','featured','steps'));
 }
 public function collection(string $slug){
  $collection=Collection::with(['mainImage','images'])->where('slug',$slug)->where('is_published',true)->firstOrFail();
  $related=Collection::with('mainImage')->where('is_published',true)->where('id','!=',$collection->id)->orderBy('sort_order')->limit(3)->get();
  return view('frontend.collections.show',compact('collection','related'));
 }
 public function contact(){
  $page=Page::with(['visibleSections.image','visibleSections.secondaryImage','heroImage'])->where('slug','contact')->where('is_published',true)->firstOrFail();
  $services=Service::with('mainImage')->where('is_published',true)->orderBy('sort_order')->get();
  $collections=Collection::with('mainImage')->where('is_published',true)->orderBy('sort_order')->get();
  $steps=ProcessStep::where('is_visible',true)->orderBy('sort_order')->get();$featured=$collections->first();
  return view('frontend.pages.contact',compact('page','services','collections','steps','featured'));
 }
 public function generic(string $slug){
  abort_if(in_array($slug,['home','about','contact','services-overview','collections-overview'],true),404);
  $page=Page::with(['visibleSections.image','visibleSections.secondaryImage','heroImage'])->where('slug',$slug)->where('is_published',true)->firstOrFail();
  $services=Service::with('mainImage')->where('is_published',true)->orderBy('sort_order')->get();
  $collections=Collection::with('mainImage')->where('is_published',true)->orderBy('sort_order')->get();
  $steps=ProcessStep::where('is_visible',true)->orderBy('sort_order')->get();$featured=$collections->first();
  return view('frontend.pages.generic',compact('page','services','collections','steps','featured'));
 }
 public function robots(){return response("User-agent: *\nAllow: /\nDisallow: /admin\nSitemap: ".route('sitemap')."\n",200,['Content-Type'=>'text/plain']);}
 public function sitemap(){
  $urls=[url('/')=>now(),url('/about')=>now(),url('/contact')=>now(),url('/services')=>now(),url('/collections')=>now()];
  foreach(Page::where('is_published',true)->whereNotIn('slug',['home','about','contact','services-overview','collections-overview'])->get() as $p) $urls[route('pages.show',$p->slug)]=$p->updated_at;
  foreach(Service::where('is_published',true)->get() as $s) $urls[route('services.show',$s->slug)]=$s->updated_at;
  foreach(Collection::where('is_published',true)->get() as $c) $urls[route('collections.show',$c->slug)]=$c->updated_at;
  $xml='<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
  foreach($urls as $loc=>$modified) $xml.='<url><loc>'.e($loc).'</loc><lastmod>'.$modified->toDateString().'</lastmod></url>';
  return response($xml.'</urlset>',200,['Content-Type'=>'application/xml; charset=UTF-8']);
 }
}
