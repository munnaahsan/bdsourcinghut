@extends('admin.layouts.app')
@section('title','Site Settings')
@section('content')<div class="page-heading"><div><span class="mini-label">COMPANY INFORMATION / GLOBAL FIELDS</span><h1>Site settings</h1><p>Manage contact information, header labels, social profiles, footer and search defaults.</p></div></div>
<div class="menu-tabs">@foreach($groups as $name)<a href="{{ route('admin.settings.index',['group'=>$name]) }}" class="tab @if($group===$name) selected @endif">{{ ucfirst($name) }}</a>@endforeach</div>
<section class="panel setting-panel"><div class="panel-head"><h2>{{ ucfirst($group) }} settings</h2><span class="count-pill">{{ $settings->count() }} fields</span></div><form class="admin-form" method="POST" action="{{ route('admin.settings.update') }}">@csrf @method('PUT')<input type="hidden" name="group" value="{{ $group }}">
@foreach($settings as $setting)<label>{{ ucwords(str_replace('_',' ',$setting->key)) }}
@if(str_ends_with($setting->key,'description') || str_contains($setting->key,'statement'))<textarea rows="3" name="settings[{{ $setting->key }}]">{{ old('settings.'.$setting->key,$setting->value) }}</textarea>
@elseif(in_array($setting->key,['logo_media_id','favicon_media_id']))<input type="number" name="settings[{{ $setting->key }}]" min="1" value="{{ old('settings.'.$setting->key,$setting->value) }}" placeholder="Image ID from Media Library"><small>Optional: use an existing image ID from <a href="{{ route('admin.media.index') }}">Media Library</a>.</small>
@else<input type="text" name="settings[{{ $setting->key }}]" value="{{ old('settings.'.$setting->key,$setting->value) }}">@endif</label>@endforeach
<button class="admin-button">Save {{ $group }} settings ↗</button></form></section>
@endsection
