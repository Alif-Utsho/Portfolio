@extends('admin.layout')

@section('title', 'Media library')

@section('content')
    <div class="page-heading"><div><span class="sidebar-label">ASSETS</span><h1>Media library</h1><p>Upload images for project cards and personal sections. JPEG, PNG, and WebP up to 5 MB.</p></div></div>
    <form class="admin-panel media-upload admin-form" method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data">@csrf<div><label for="image">Choose image</label><input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" required>@error('image')<span class="field-error">{{ $message }}</span>@enderror</div><div><label for="alt_text">Alternative text</label><input id="alt_text" name="alt_text" placeholder="Describe the image for screen readers"></div><button class="admin-button admin-button-primary" type="submit">Upload image <span>↑</span></button></form>
    <section class="media-grid">@forelse ($media as $asset)<article class="media-card"><img src="{{ Storage::disk('public')->url($asset->path) }}" alt="{{ $asset->alt_text ?? '' }}" loading="lazy"><div><strong>{{ $asset->original_name }}</strong><small>{{ $asset->alt_text ?: 'No alternative text' }}</small><form method="POST" action="{{ route('admin.media.destroy', $asset) }}" data-confirm="Remove this image from the media library?">@csrf @method('DELETE')<button class="danger-link" type="submit">Delete</button></form></div></article>@empty<div class="admin-panel empty-panel"><span>▧</span><p>No media uploaded yet.</p></div>@endforelse</section><div class="simple-pagination">{{ $media->links('pagination::simple-tailwind') }}</div>
@endsection
