@extends('admin.layout')

@section('title', $item->exists ? 'Edit '.$label : 'Add '.$label)

@section('content')
    <div class="page-heading">
        <div>
            <a class="admin-text-link" href="{{ route('admin.content.index', $type) }}">← {{ $label }}</a>
            <h1>{{ $item->exists ? 'Edit content' : 'Add content' }}</h1>
            <p>Save as a draft while you work, then publish when it is ready.</p>
        </div>
    </div>

    <form class="admin-form content-editor" method="POST" action="{{ $item->exists ? route('admin.content.update', $item) : route('admin.content.store', $type) }}">
        @csrf
        @if ($item->exists)
            @method('PUT')
        @endif
        <input type="hidden" name="type" value="{{ $type }}">

        <div class="editor-columns">
            <div class="editor-main">
                <label for="title">Title <span>*</span></label>
                <input id="title" name="title" value="{{ old('title', $item->title) }}" required>
                @error('title')<span class="field-error">{{ $message }}</span>@enderror

                @if ($type === 'project')
                    <label for="slug">Page slug <small>Leave blank to generate from the title</small></label>
                    <input id="slug" name="slug" value="{{ old('slug', $item->slug) }}" placeholder="project-name">
                    @error('slug')<span class="field-error">{{ $message }}</span>@enderror
                @endif

                <label for="summary">Short summary</label>
                <textarea id="summary" name="summary" rows="3">{{ old('summary', $item->summary) }}</textarea>
                @error('summary')<span class="field-error">{{ $message }}</span>@enderror

                <label for="body">Description</label>
                <textarea id="body" name="body" rows="9">{{ old('body', $item->body) }}</textarea>
                @error('body')<span class="field-error">{{ $message }}</span>@enderror

                @if ($type === 'project')
                    <label for="challenge">Problem or context</label>
                    <textarea id="challenge" name="challenge" rows="3">{{ old('challenge', $item->metadata['challenge'] ?? '') }}</textarea>
                    <label for="features">Project notes <small>One item per line</small></label>
                    <textarea id="features" name="features" rows="4">{{ old('features', implode("\n", $item->metadata['features'] ?? [])) }}</textarea>
                    <label for="technologies">Technologies <small>Comma separated</small></label>
                    <input id="technologies" name="technologies" value="{{ old('technologies', implode(', ', $item->technologies ?? [])) }}">
                @elseif ($type === 'experience')
                    <div class="form-grid">
                        <div>
                            <label for="organization">Company / organization</label>
                            <input id="organization" name="organization" value="{{ old('organization', $item->metadata['organization'] ?? $item->subtitle) }}">
                        </div>
                        <div>
                            <label for="period">Dates / period</label>
                            <input id="period" name="period" value="{{ old('period', $item->metadata['period'] ?? '') }}">
                        </div>
                    </div>
                    <label for="technologies">Technologies <small>Comma separated</small></label>
                    <input id="technologies" name="technologies" value="{{ old('technologies', implode(', ', $item->technologies ?? [])) }}">
                @elseif ($type === 'skill')
                    <label for="group">Skill category</label>
                    <input id="group" name="group" value="{{ old('group', $item->metadata['group'] ?? $item->subtitle) }}">
                    <label for="technologies">Related technologies <small>Comma separated</small></label>
                    <input id="technologies" name="technologies" value="{{ old('technologies', implode(', ', $item->technologies ?? [])) }}">
                @endif
            </div>

            <aside class="editor-aside">
                <div class="admin-panel">
                    <span class="sidebar-label">PUBLISHING</span>
                    <label class="check-row"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $item->is_published))> Published</label>
                    @if ($type === 'project')
                        <label class="check-row"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $item->is_featured))> Feature on homepage</label>
                    @endif
                    <label for="sort_order">Display order</label>
                    <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $item->sort_order) }}" required>
                </div>

                <div class="admin-panel">
                    <span class="sidebar-label">PRESENTATION</span>
                    <label for="eyebrow">Label / eyebrow</label>
                    <input id="eyebrow" name="eyebrow" value="{{ old('eyebrow', $item->eyebrow) }}">
                    <label for="subtitle">Organization / context</label>
                    <input id="subtitle" name="subtitle" value="{{ old('subtitle', $item->subtitle) }}">

                    @if ($type === 'project')
                        <label for="category">Project filter</label>
                        <select id="category" name="category">
                            <option value="other">Other</option>
                            @foreach (['php' => 'PHP', 'csharp' => 'ASP.NET / C#', 'javascript' => 'JavaScript'] as $value => $option)
                                <option value="{{ $value }}" @selected(old('category', $item->metadata['category'] ?? 'other') === $value)>{{ $option }}</option>
                            @endforeach
                        </select>

                        <label for="image_path">Cover image</label>
                        <select id="image_path" name="image_path">
                            <option value="">No image</option>
                            @foreach ($media as $asset)
                                <option value="{{ $asset->path }}" @selected(old('image_path', $item->image_path) === $asset->path)>{{ $asset->original_name }}</option>
                            @endforeach
                        </select>

                        <label for="url">Repository / primary URL</label>
                        <input id="url" name="url" type="url" value="{{ old('url', $item->url) }}">
                        @error('url')<span class="field-error">{{ $message }}</span>@enderror

                        <label for="secondary_url">Live demo / secondary URL</label>
                        <input id="secondary_url" name="secondary_url" type="url" value="{{ old('secondary_url', $item->secondary_url) }}">
                        @error('secondary_url')<span class="field-error">{{ $message }}</span>@enderror
                    @elseif ($type === 'personal')
                        <label for="image_path">Photo</label>
                        <select id="image_path" name="image_path">
                            <option value="">Illustrated fallback</option>
                            @foreach ($media as $asset)
                                <option value="{{ $asset->path }}" @selected(old('image_path', $item->image_path) === $asset->path)>{{ $asset->original_name }}</option>
                            @endforeach
                        </select>
                    @elseif (in_array($type, ['social', 'navigation'], true))
                        <label for="url">{{ $type === 'social' ? 'Profile URL' : 'Link URL' }}</label>
                        <input id="url" name="url" value="{{ old('url', $item->url) }}" placeholder="https://... or /#section">
                        @error('url')<span class="field-error">{{ $message }}</span>@enderror
                    @endif
                </div>

                <button class="admin-button admin-button-primary full-width" type="submit">{{ $item->exists ? 'Save changes' : 'Create content' }} <span>↗</span></button>
            </aside>
        </div>

        @foreach (['subtitle', 'summary', 'body', 'url', 'secondary_url', 'organization', 'period', 'group', 'challenge', 'features', 'technologies', 'image_path', 'category', 'is_published', 'is_featured', 'sort_order', 'eyebrow', 'title', 'slug', 'type'] as $field)
            @error($field)<span class="field-error">{{ $message }}</span>@enderror
        @endforeach
    </form>
@endsection
