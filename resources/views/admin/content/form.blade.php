@extends('admin.layouts.app')
@section('title',($item?'Edit':'New').' '.ucfirst(rtrim($type,'s')))
@section('content')
<div class="page-heading"><div><span class="mini-label">{{ strtoupper($type) }} / {{ $item?'EDIT':'CREATE' }}</span><h1>{{ $item?'Edit':'New' }} {{ ucfirst(rtrim($type,'s')) }}</h1><p>Save changes to update the public website.</p></div><a class="admin-button outline" href="{{ route('admin.content.index',$type) }}">← Back to {{ $type }}</a></div>
<form method="POST" action="{{ $item?route('admin.content.update',['type'=>$type,'id'=>$item->id]):route('admin.content.store',$type) }}" class="content-form">@csrf @if($item)@method('PUT')@endif
<div class="form-main"><section class="panel"><div class="panel-head"><h2>Content</h2></div><div class="form-grid">
 <div class="field"><label>Title *</label><input type="text" name="title" maxlength="180" required value="{{ old('title',$item->title??'') }}"></div>
 <div class="field"><label>URL slug *</label><input type="text" name="slug" maxlength="180" required value="{{ old('slug',$item->slug??'') }}" @if($type==='pages' && $item && in_array($item->slug,['home','about','contact']))readonly @endif><small>Lowercase letters, numbers and hyphens.</small></div>
 @if($type==='pages')
 <div class="field"><label>Eyebrow</label><input name="eyebrow" value="{{ old('eyebrow',$item->eyebrow??'') }}"></div>
 <div class="field"><label>Intro / excerpt</label><textarea name="excerpt" rows="3">{{ old('excerpt',$item->excerpt??'') }}</textarea></div>
 <div class="field"><label>Page body</label><textarea name="body" rows="9">{{ old('body',$item->body??'') }}</textarea><small>Plain text is rendered safely. Homepage and About sections are managed separately.</small></div>
 @include('admin.shared.media-select',['field'=>'hero_image_id','value'=>$item->hero_image_id??'','label'=>'Hero image'])
 @elseif($type==='services')
 <div class="field"><label>Short description</label><textarea name="short_description" rows="3">{{ old('short_description',$item->short_description??'') }}</textarea></div>
 <div class="field"><label>Full service description</label><textarea name="body" rows="8">{{ old('body',$item->body??'') }}</textarea></div>
 <div class="field"><label>Capabilities (one per line)</label><textarea name="capabilities_text" rows="6">{{ old('capabilities_text',implode("\n",$item?->capabilities??[])) }}</textarea></div>
 <div class="field"><label>Process steps (Title | Description on each line)</label><textarea name="steps_text" rows="6">{{ old('steps_text',collect($item?->steps??[])->map(fn($step)=>($step['title']??'').' | '.($step['description']??''))->implode("\n")) }}</textarea></div>
 @include('admin.shared.media-select',['field'=>'hero_image_id','value'=>$item->hero_image_id??'','label'=>'Hero photograph'])
 @include('admin.shared.media-select',['field'=>'main_image_id','value'=>$item->main_image_id??'','label'=>'Feature photograph'])
 @else
 <div class="field"><label>Short description</label><textarea name="short_description" rows="3">{{ old('short_description',$item->short_description??'') }}</textarea></div>
 <div class="field"><label>Collection overview</label><textarea name="body" rows="6">{{ old('body',$item->body??'') }}</textarea></div>
 <div class="field"><label>Fabric & material information (optional)</label><textarea name="fabric_information" rows="3">{{ old('fabric_information',$item->fabric_information??'') }}</textarea></div>
 <div class="field"><label>Design & fit information (optional)</label><textarea name="fit_information" rows="3">{{ old('fit_information',$item->fit_information??'') }}</textarea></div>
 <div class="field"><label>Production information (optional)</label><textarea name="production_information" rows="3">{{ old('production_information',$item->production_information??'') }}</textarea></div>
 @include('admin.shared.media-select',['field'=>'main_image_id','value'=>$item->main_image_id??'','label'=>'Main collection photograph'])
 <div class="field"><label>Gallery images (check, then drag to arrange)</label><div class="gallery-editor" data-gallery-editor>@foreach($media as $medium)<label class="gallery-editor-item" draggable="true"><img src="{{ $medium->url }}" alt="{{ $medium->alt_text }}"><span><input type="checkbox" name="gallery_ids[]" value="{{ $medium->id }}" @checked(in_array($medium->id,old('gallery_ids',$item?->images?->pluck('id')->all()??[])))> #{{ $medium->id }}</span></label>@endforeach</div><input type="hidden" name="gallery_order" data-gallery-order><small>Drag selected images to change their display order, then save.</small></div>
 @endif
 </div></section>
 <section class="panel"><div class="panel-head"><h2>Search appearance</h2></div><div class="form-grid"><div class="field"><label>SEO page title</label><input name="seo_title" maxlength="180" value="{{ old('seo_title',$item->seo_title??'') }}"></div><div class="field"><label>Meta description</label><textarea name="seo_description" rows="3" maxlength="500">{{ old('seo_description',$item->seo_description??'') }}</textarea></div></div></section></div>
<div class="form-side"><section class="panel sticky"><h2>Publish settings</h2><div class="field"><label>Display order</label><input type="number" min="0" name="sort_order" value="{{ old('sort_order',$item->sort_order??0) }}"></div><label class="checkline"><input type="checkbox" name="is_published" value="1" @checked(old('is_published',$item->is_published??false))> Published</label>@if($type!=='pages')<label class="checkline"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured',$item->is_featured??false))> Featured</label>@endif
<button class="admin-button block">{{ $item?'Save changes':'Create item' }} ↗</button><p class="field-help">Only published items appear on the public website.</p></section>
@if($type==='pages' && $item)<section class="panel"><h2>Section builder</h2><p>Control section content, visibility and order.</p><a class="admin-button outline" href="{{ route('admin.sections.index',$item->slug) }}">Manage page sections ↗</a></section>@endif</div></form>
@endsection
