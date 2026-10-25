<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>@yield('title','Dashboard') — BD SOURCING HUT Admin</title><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="{{ asset('assets/admin.css') }}"><script defer src="{{ asset('assets/admin.js') }}"></script></head>
<body class="admin-body"><a class="skip-admin" href="#admin-main">Skip to content</a><div class="admin-shell">
<aside class="admin-sidebar" id="admin-sidebar"><a class="admin-brand" href="{{ route('admin.dashboard') }}"><span class="admin-brand-mark">BD</span><span>SOURCING<br><small>ADMIN WORKSPACE</small></span></a><div class="admin-sidebar-label">WORKSPACE</div>
<nav aria-label="Administrator navigation">
 @php
$nav=[
  ['Dashboard','admin.dashboard','/admin','▦'],['Homepage Builder','admin.homepage','/admin/homepage','◈'],
  ['Pages','admin.content.index','/admin/pages','▤'],['Services','admin.content.index','/admin/services','⌘'],
  ['Collections','admin.content.index','/admin/collections','◇'],['Media Library','admin.media.index','/admin/media','▧'],
  ['Process Timeline','admin.process.index','/admin/process','≡'],['Inquiries','admin.inquiries.index','/admin/inquiries','✉'],
  ['Menus','admin.menus.index','/admin/menus','☷'],['Visual Theme','admin.theme.index','/admin/theme','◉'],
  ['Site Settings','admin.settings.index','/admin/settings','⚙'],['Footer','admin.footer','/admin/footer','◫'],
  ['SEO Defaults','admin.seo','/admin/seo','↗'],['Administrators','admin.users.index','/admin/users','♙']
 ];
@endphp
 @foreach($nav as [$label,$routeName,$href,$symbol])<a href="{{ url($href) }}" class="admin-nav-link @if(request()->is(ltrim($href,'/')) || ($href==='/admin/pages'&&request()->is('admin/pages/*')) || ($href==='/admin/services'&&request()->is('admin/services/*')) || ($href==='/admin/collections'&&request()->is('admin/collections/*'))) active @endif"><span class="admin-nav-icon">{{ $symbol }}</span>{{ $label }}</a>@endforeach
 </nav><div class="admin-sidebar-bottom"><a href="{{ route('home') }}" target="_blank" rel="noopener">View public website ↗</a><form method="POST" action="{{ route('admin.logout') }}">@csrf<button>Log out →</button></form></div></aside>
<div class="admin-content"><div class="admin-topbar"><button class="admin-burger" aria-label="Toggle sidebar" type="button">☰</button><span class="topbar-crumb">BD SOURCING HUT / <b>@yield('title','Dashboard')</b></span><span class="topbar-user">{{ auth()->user()->name ?? 'Administrator' }} <span class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A',0,1)) }}</span></span></div><main id="admin-main" class="admin-main">
@if(session('status'))<div class="flash success" role="status">✓ {{ session('status') }}</div>@endif
@if($errors->any())<div class="flash error" role="alert"><b>Please review:</b><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')
</main><div class="admin-bottom">BD SOURCING HUT / CONTENT MANAGEMENT · {{ date('Y') }}</div></div></div></body></html>
