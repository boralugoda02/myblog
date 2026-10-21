@extends('layouts.app')
@section('title', 'Edit - ' . $post->title)
@section('content')
<div class="container-fluid px-0">

    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0 text-dark">Edit Blog Post</h4>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    
                    <div class="col-md-8">
                        <label for="title" class="form-label fw-semibold">Post Title</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $post->title) }}" required>
                    </div>

                    
                    <div class="col-md-4">
                        <label for="category_id" class="form-label fw-semibold">Category</label>
                        <select name="category_id" id="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (old('category_id', $post->category_id) == $category->id) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    
                    <div class="col-12">
                        <label for="image" class="form-label fw-semibold">Featured Image</label>
                        @if($post->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $post->image) }}" alt="Current Image" class="rounded border" style="height: 100px; object-fit: cover;">
                                <small class="text-muted d-block mt-1">Current Image</small>
                            </div>
                        @endif
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        <small class="text-muted">Leave empty if you don't want to change the image.</small>
                    </div>

                    
                    <div class="col-12">
                        @include('posts.partials.tags-input', ['value' => old('tags', $post->tags->pluck('name')->implode(', '))])
                    </div>

                    
                    <div class="col-12">
                        <label for="editor" class="form-label fw-semibold">Content</label>
                        <textarea name="content" id="editor">{{ old('content', $post->content === strip_tags($post->content) ? nl2br(e($post->content)) : $post->content) }}</textarea>
                    </div>

                    
                    
                    <div class="col-md-6">
                        <label for="scheduled_at" class="form-label fw-semibold">Schedule publish <small class="text-muted">(optional)</small></label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="form-control"
                               value="{{ old('scheduled_at', $post->scheduled_at?->format('Y-m-d\TH:i')) }}">
                    </div>

                   
                    <div class="col-12">
                        <div class="p-3 rounded-3" style="background: #1e293b; color: #fff;">
                            <h6 class="fw-bold mb-3"><i class="bi bi-search me-2"></i> SEO &amp; Meta Details</h6>

                            <label class="form-label small text-light">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control mb-3" value="{{ old('meta_title', $post->meta_title) }}"
                                   style="background: #0f172a; border: 1px solid #334155; color: #fff;">

                            <label class="form-label small text-light">Meta Description</label>
                            <textarea name="meta_description" rows="2" class="form-control mb-3"
                                      style="background: #0f172a; border: 1px solid #334155; color: #fff;">{{ old('meta_description', $post->meta_description) }}</textarea>

                            <label class="form-label small text-light">Meta Keywords</label>
                            <input type="text" name="meta_keywords" class="form-control mb-3" value="{{ old('meta_keywords', $post->meta_keywords) }}"
                                   style="background: #0f172a; border: 1px solid #334155; color: #fff;">

                            <label class="form-label small text-light">Meta Image</label>
                            @if($post->meta_image)
                                <div class="mb-2"><img src="{{ asset('storage/' . $post->meta_image) }}" alt="Meta image" class="rounded" style="height: 70px; object-fit: cover;"></div>
                            @endif
                            <input type="file" name="meta_image" class="form-control" accept="image/*"
                                   style="background: #0f172a; border: 1px solid #334155; color: #fff;">
                            <small class="text-secondary">Leave empty to keep the current image.</small>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-light border px-4">Cancel</a>
                        @if(in_array($post->status, ['draft', 'rejected']))
                            <button type="submit" name="action" value="draft" formnovalidate class="btn btn-outline-secondary px-4">
                                <i class="bi bi-file-earmark-text me-1"></i> Save as Draft
                            </button>
                            <button type="submit" name="action" value="submit" class="btn btn-primary px-4">
                                <i class="bi bi-send me-1"></i> {{ $post->status === 'rejected' ? 'Resubmit for approval' : (auth()->user()->isAdmin() ? 'Publish' : 'Submit for approval') }}
                            </button>
                        @else
                            <button type="submit" class="btn btn-primary px-4">Update Post</button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @include('posts.partials.editor')
@endsection