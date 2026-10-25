@extends('admin.layouts.app')
@section('title', ucfirst($type))

@section('content')
<div class="page-heading">
    <div>
        <span class="mini-label">CONTENT MANAGEMENT</span>
        <h1>{{ ucfirst($type) }}</h1>
        <p>Create, publish and organize your {{ $type }}. Changes go live on save.</p>
    </div>
    <a href="{{ route('admin.content.create', ['type' => $type]) }}" class="admin-button">
        + Add {{ rtrim(ucfirst($type), 's') }}
    </a>
</div>

<section class="panel">
    <div class="panel-head">
        <form method="GET" action="{{ route('admin.content.index', ['type' => $type]) }}" class="toolbar">
            <input type="search" name="q" value="{{ $q }}" placeholder="Search title or slug">
            <button class="admin-button small">Search</button>
        </form>
        <span class="count-pill">{{ $items->total() }} total</span>
    </div>

    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>URL slug</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    @php
                        $viewUrl = null;

                        if ($item->is_published) {
                            if ($type === 'services') {
                                $viewUrl = route('services.show', ['slug' => $item->slug]);
                            } elseif ($type === 'collections') {
                                $viewUrl = route('collections.show', ['slug' => $item->slug]);
                            } else {
                                $pageRoutes = [
                                    'home' => 'home',
                                    'about' => 'about',
                                    'contact' => 'contact',
                                    'services-overview' => 'services.index',
                                    'collections-overview' => 'collections.index',
                                ];

                                $viewUrl = isset($pageRoutes[$item->slug])
                                    ? route($pageRoutes[$item->slug])
                                    : route('pages.show', ['slug' => $item->slug]);
                            }
                        }
                    @endphp

                    <tr>
                        <td><strong>{{ $item->title }}</strong></td>
                        <td><code>{{ $item->slug }}</code></td>
                        <td>{{ $item->sort_order }}</td>
                        <td>
                            <span class="status-badge {{ $item->is_published ? 'published' : 'draft' }}">
                                {{ $item->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td>{{ $item->updated_at->format('d M Y') }}</td>
                        <td class="table-actions">
                            <a href="{{ route('admin.content.edit', ['type' => $type, 'id' => $item->id]) }}">Edit</a>

                            @if($type === 'pages' && in_array($item->slug, ['home', 'about', 'contact'], true))
                                <a href="{{ route('admin.sections.index', ['slug' => $item->slug]) }}">Sections</a>
                            @endif

                            @if($viewUrl !== null)
                                <a href="{{ $viewUrl }}" target="_blank" rel="noopener noreferrer">View &nearr;</a>
                            @endif

                            <form method="POST"
                                  action="{{ route('admin.content.destroy', ['type' => $type, 'id' => $item->id]) }}"
                                  data-confirm="Remove this item?">
                                @csrf
                                @method('DELETE')
                                <button class="text-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">No items found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pager">{{ $items->links() }}</div>
</section>
@endsection
