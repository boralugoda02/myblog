@extends('layouts.app')
@section('title', 'Create Post')

@section('content')
<div style="max-width: 800px; margin: 0 auto; padding: 20px;">
    
    <div style="background: #ffffff; padding: 32px; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        
        <h2 style="font-size: 22px; font-weight: 700; color: #111827; margin-bottom: 24px;">Create New Blog Post</h2>

        
        @if ($errors->any())
            <div class="alert alert-danger" style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
                <ul class="mb-0" style="margin: 0; padding-left: 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        
        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 20px;">
                
                
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">Post Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Enter post title" 
                           style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box;">
                </div>

                
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label style="font-size: 14px; font-weight: 600; color: #374151;">Category Dropdown</label>
                        <button type="button" onclick="toggleCategoryModal(true)" 
                                style="background: none; border: none; font-size: 13px; font-weight: 600; color: #2563eb; cursor: pointer;">
                            + Add Category
                        </button>
                    </div>
                    <select name="category_id" id="category_id" class="form-select" required 
                            style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background-color: #fff; outline: none; box-sizing: border-box;">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">Post Featured Image</label>
                
                
                <input type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)" style="display: none;">

                
                <div id="upload-box" onclick="document.getElementById('image').click()" 
                     style="border: 2px dashed #d1d5db; background: #f9fafb; padding: 24px; text-align: center; border-radius: 8px; cursor: pointer;">
                    <svg style="width: 32px; height: 32px; color: #9ca3af; margin: 0 auto 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span style="font-size: 14px; font-weight: 500; color: #4b5563;">Click to upload image</span>
                </div>

                
                <div id="image-preview-container" style="display: none; margin-top: 12px; position: relative; width: fit-content;">
                    <img id="image-preview" style="max-width: 220px; max-height: 150px; border-radius: 8px; border: 1px solid #e5e7eb; object-fit: cover; display: block;">
                    
                    
                    <button type="button" onclick="removeImage()" 
                            style="position: absolute; top: -8px; right: -8px; background: #ef4444; color: #ffffff; border: none; border-radius: 50%; width: 24px; height: 24px; font-size: 12px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);" 
                            title="Remove image">
                        ✕
                    </button>
                </div>
            </div>

            
            @include('posts.partials.tags-input', ['value' => old('tags')])

            {{-- Content Editor --}}
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">Content</label>
                <textarea name="content" id="editor" placeholder="Write your blog content here...">{{ old('content') }}</textarea>
            </div>

            
            <div style="background: #1e293b; color: #ffffff; padding: 20px; border-radius: 8px; margin-bottom: 24px;">
                <h5 style="font-weight: 700; font-size: 16px; margin-bottom: 16px; color: #f8fafc; border-bottom: 1px solid #334155; padding-bottom: 8px;">
                    <i class="bi bi-search me-2"></i> SEO & Meta Details
                </h5>
                
                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px; color: #cbd5e1;">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title') }}" placeholder="SEO Title" class="form-control" style="background: #0f172a; border: 1px solid #334155; color: #fff;">
                </div>

                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px; color: #cbd5e1;">Meta Description</label>
                    <textarea name="meta_description" class="form-control" rows="2" placeholder="Brief meta description for search engines" style="background: #0f172a; border: 1px solid #334155; color: #fff;">{{ old('meta_description') }}</textarea>
                </div>

                <div style="margin-bottom: 14px;">
                    <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px; color: #cbd5e1;">Meta Keywords </label>
                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}" class="form-control" placeholder="news, tech, laravel" style="background: #0f172a; border: 1px solid #334155; color: #fff;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px; color: #cbd5e1;">Meta Image</label>
                    <input type="file" name="meta_image" class="form-control" accept="image/*" style="background: #0f172a; border: 1px solid #334155; color: #fff;">
                </div>
            </div>

            
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                    Schedule publish <span style="font-weight: 400; color: #6b7280;">(optional)</span>
                </label>
                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}"
                       style="width: 100%; max-width: 320px; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                <small style="display: block; color: #6b7280; margin-top: 4px;">Approve unath passe me welawe idan public ta penenawa. Empty nam approve unama dan thamai.</small>
            </div>

            
            <div style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 16px; border-top: 1px solid #f3f4f6;">
                <a href="{{ route('dashboard') }}" 
                   style="padding: 10px 20px; background: #f3f4f6; color: #374151; text-decoration: none; border-radius: 8px; font-size: 14px; font-weight: 500;">
                    Cancel
                </a>
                <button type="submit" name="action" value="draft" formnovalidate
                        style="padding: 10px 20px; background: #ffffff; color: #374151; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">
                    Save as Draft
                </button>
                <button type="submit" name="action" value="submit"
                        style="padding: 10px 20px; background: #2563eb; color: #ffffff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">
                    {{ auth()->user()->isAdmin() ? 'Publish Post' : 'Submit for approval' }}
                </button>
            </div>

        </form>
    </div>

</div>


<div id="categoryModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: #ffffff; padding: 24px; border-radius: 12px; width: 100%; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
        <h3 style="font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 16px;">Add New Category</h3>
        
        <input type="text" id="new_category_name" placeholder="Category Name" 
               style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; margin-bottom: 20px; outline: none; box-sizing: border-box;">
        
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" onclick="toggleCategoryModal(false)" 
                    style="padding: 8px 16px; background: #f3f4f6; color: #374151; border: none; border-radius: 6px; font-size: 14px; cursor: pointer;">
                Cancel
            </button>
            <button type="button" onclick="submitCategory()" 
                    style="padding: 8px 16px; background: #2563eb; color: #ffffff; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer;">
                Save Category
            </button>
        </div>
    </div>
</div>


@include('posts.partials.editor')

<script>
    function previewImage(event) {
        const input = event.target;
        const container = document.getElementById('image-preview-container');
        const preview = document.getElementById('image-preview');
        const uploadBox = document.getElementById('upload-box');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.style.display = 'block';
                uploadBox.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeImage() {
        const input = document.getElementById('image');
        const container = document.getElementById('image-preview-container');
        const uploadBox = document.getElementById('upload-box');

        input.value = '';
        container.style.display = 'none';
        uploadBox.style.display = 'block';
    }

    function toggleCategoryModal(show) {
        const modal = document.getElementById('categoryModal');
        modal.style.display = show ? 'flex' : 'none';
        if(!show) document.getElementById('new_category_name').value = '';
    }

    function submitCategory() {
        const nameInput = document.getElementById('new_category_name');
        const categoryName = nameInput.value.trim();

        if (!categoryName) {
            alert('Please enter a category name');
            return;
        }

        fetch("{{ route('categories.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ name: categoryName })
        })
        .then(res => res.json())
        .then(data => {
            if (data.id) {
                const select = document.getElementById('category_id');
                const newOption = new Option(data.name, data.id, true, true);
                select.add(newOption);
                toggleCategoryModal(false);
            } else {
                alert(data.message || 'Error creating category');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Something went wrong!');
        });
    }
</script>
@endsection