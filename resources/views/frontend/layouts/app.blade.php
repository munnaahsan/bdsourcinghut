<!doctype html>
<html lang="en">

@php
    // Provide safe defaults for shared layout data.
    // Existing values supplied by controllers are preserved.
    $site = $site ?? [];
    $theme = $theme ?? [];

    $logoMedia = $logoMedia ?? null;
    $faviconMedia = $faviconMedia ?? null;

    $mainMenu = $mainMenu ?? null;
    $footerMenu = $footerMenu ?? null;
@endphp


<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="theme-color" content="{{ $theme['background'] ?? '#10111A' }}">
  @php
    $seoTitle=trim($__env->yieldContent('seo_title', $site['default_seo_title'] ?? 'BD SOURCING HUT'));
    $seoDescription=trim($__env->yieldContent('seo_description', $site['default_seo_description'] ?? ''));
    $seoRobots=trim($__env->yieldContent('robots','index,follow'));
    $canonicalUrl=trim($__env->yieldContent('canonical',url()->current()));
    $ogImage=trim($__env->yieldContent('og_image',asset('images/hero/hero-main.jpg')));
  @endphp
  <title>{{ $seoTitle }}</title>
  <meta name="description" content="{{ $seoDescription }}">
  <meta name="robots" content="{{ $seoRobots }}">
  <link rel="canonical" href="{{ $canonicalUrl }}">
  <meta property="og:type" content="website"><meta property="og:title" content="{{ $seoTitle }}"><meta property="og:description" content="{{ $seoDescription }}">
  <meta property="og:image" content="{{ $ogImage }}">
  @if($faviconMedia)<link rel="icon" href="{{ $faviconMedia->url }}">@endif
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&family=Inter:wght@400;500;600;700&family=Manrope:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
  <style>:root{
     --c-primary:{{ $theme['primary'] ?? '#172B68' }};--c-secondary:{{ $theme['secondary'] ?? '#10111A' }};--c-accent:{{ $theme['accent'] ?? '#D9BC83' }};
     --c-bg:{{ $theme['background'] ?? '#10111A' }};--c-surface:{{ $theme['surface'] ?? '#17202D' }};--c-light:{{ $theme['light'] ?? '#F7F2E9' }};
     --c-text:{{ $theme['text'] ?? '#F7F2E9' }};--c-muted:{{ $theme['muted'] ?? '#C9CED8' }};--c-button:{{ $theme['button'] ?? '#D9BC83' }};
     --font-display:'{{ $theme['heading_font'] ?? 'Cormorant Garamond' }}',Georgia,serif;
     --font-body:'{{ $theme['body_font'] ?? 'Manrope' }}',Arial,sans-serif;--card-radius:{{ $theme['radius'] ?? '12' }}px;
     --shadow-strength:{{ ($theme['shadow_intensity'] ?? 50)/100 }};--motion-strength:{{ ($theme['animation_intensity'] ?? 75)/100 }};
  }</style>
  <link rel="stylesheet" href="{{ asset('assets/site.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/premium-cursor.css') }}?v=1">
  <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.14.2/dist/gsap.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.14.2/dist/ScrollTrigger.min.js"></script>
  <script defer src="{{ asset('assets/site.js') }}"></script>
  <script defer src="{{ asset('assets/premium-cursor.js') }}?v=1"></script>
  @stack('head')
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<div class="site-loader" aria-hidden="true"><span>BD<span class="loader-line"></span></span></div>
<header class="site-header" id="site-header">
 <div class="header-inner">
  <a href="{{ route('home') }}" class="brand" aria-label="BD SOURCING HUT home">
    @if($logoMedia)<img src="{{ $logoMedia->url }}" alt="{{ $site['site_name'] ?? 'BD SOURCING HUT' }} logo">@else<span class="brand-symbol">BD</span><span class="brand-word">SOURCING<br>HUT</span>@endif
  </a>
  <nav class="desktop-nav" aria-label="Primary navigation">
  @foreach($mainMenu?->items ?? [] as $nav)
   <div class="nav-item @if($nav->children->isNotEmpty()) has-dropdown @endif">
    <a class="nav-link" href="{{ $nav->url }}" @if($nav->target==='_blank')target="_blank" rel="noopener noreferrer"@endif>{{ $nav->label }}</a>
    @if($nav->children->isNotEmpty())
     <button class="nav-toggle" aria-label="Open {{ $nav->label }} submenu" aria-expanded="false" type="button"><span aria-hidden="true">⌄</span></button>
     <div class="submenu" role="group" aria-label="{{ $nav->label }} submenu">
      <span class="submenu-kicker">EXPLORE {{ $nav->label }}</span>
      @foreach($nav->children as $child)<a href="{{ $child->url }}" @if($child->target==='_blank')target="_blank" rel="noopener noreferrer"@endif>{{ $child->label }}<span aria-hidden="true">↗</span></a>@endforeach
     </div>
    @endif
   </div>
  @endforeach
  </nav>
  <div class="header-actions"><a class="btn btn-gold btn-small" href="{{ $site['header_cta_url'] ?? '/contact' }}">{{ $site['header_cta_label'] ?? 'START A PROJECT' }} <span>↗</span></a></div>
  <button class="mobile-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-nav"><span></span><span></span></button>
 </div>
</header>
<div class="mobile-menu" id="mobile-nav" aria-hidden="true"><div class="mobile-menu-inside">
 @foreach($mainMenu?->items ?? [] as $nav)
  <div class="mobile-nav-block"><a class="mobile-nav-heading" href="{{ $nav->url }}">{{ $nav->label }} <span>↗</span></a>
   @if($nav->children->isNotEmpty())<div class="mobile-subnav">@foreach($nav->children as $child)<a href="{{ $child->url }}">{{ $child->label }}</a>@endforeach</div>@endif
  </div>
 @endforeach
 <a class="btn btn-gold" href="{{ $site['header_cta_url'] ?? '/contact' }}">{{ $site['header_cta_label'] ?? 'START A PROJECT' }} ↗</a>
 </div></div>
<main id="main">@yield('content')</main>
<footer class="footer" id="footer"><div class="footer-top">
  <div><span class="eyebrow gold">BD SOURCING HUT · BANGLADESH</span><h2>LET'S MAKE<br><em>IT HAPPEN.</em></h2><p>{{ $site['footer_statement'] ?? '' }}</p><a class="text-link" href="{{ route('contact') }}">START A CONVERSATION <span>↗</span></a></div>
  <div class="footer-details"><span class="eyebrow">EXPLORE</span><nav aria-label="Footer navigation">@foreach($footerMenu?->items ?? [] as $nav)<a href="{{ $nav->url }}">{{ $nav->label }}</a>@endforeach</nav></div>
  <div class="footer-details"><span class="eyebrow">CONNECT</span>
    @if(!empty($site['contact_email']))<a href="mailto:{{ $site['contact_email'] }}">{{ $site['contact_email'] }}</a>@endif
    @if(!empty($site['contact_phone']))<a href="tel:{{ $site['contact_phone'] }}">{{ $site['contact_phone'] }}</a>@endif
    @if(!empty($site['contact_address']))<p>{{ $site['contact_address'] }}</p>@endif
    <div class="footer-social">@foreach(['instagram'=>'Instagram','linkedin'=>'LinkedIn','facebook'=>'Facebook'] as $key=>$label)@if(!empty($site['social_'.$key]))<a href="{{ $site['social_'.$key] }}" target="_blank" rel="noopener noreferrer">{{ $label }} ↗</a>@endif @endforeach</div>
  </div>
 </div><div class="footer-bottom"><span>© {{ date('Y') }} {{ $site['footer_copyright'] ?? 'BD SOURCING HUT' }}</span><span>GLOBAL APPAREL SOURCING / BANGLADESH</span><a href="#main">BACK TO TOP ↑</a></div>
</footer>
</body></html>
