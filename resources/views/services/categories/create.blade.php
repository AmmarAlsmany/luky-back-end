@extends('layouts.vertical', ['title' => 'Add Category'])

@section('css')
    @vite(['node_modules/dropzone/dist/dropzone.css'])
@endsection

@section('content')
    {{-- Page Header --}}
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="mb-1 fw-bold">Create New Category</h4>
                    <p class="text-muted mb-0">Add a new service category to organize your services</p>
                </div>
                <a href="{{ route('categories.index') }}" class="btn btn-soft-secondary">
                    <i class="mdi mdi-arrow-left me-1"></i> Back to Categories
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-xl-8">
                {{-- Category Information --}}
                <div class="card border shadow-sm">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i class="mdi mdi-information-outline text-primary me-2"></i>Category Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name_en" class="form-label fw-semibold">Category Name (English) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name_en') is-invalid @enderror"
                                    id="name_en" name="name_en" value="{{ old('name_en') }}"
                                    placeholder="e.g., Hair & Beauty" required>
                                @error('name_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="name_ar" class="form-label fw-semibold">Category Name (Arabic) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name_ar') is-invalid @enderror"
                                    id="name_ar" name="name_ar" value="{{ old('name_ar') }}"
                                    placeholder="مثال: الشعر والجمال" required dir="rtl">
                                @error('name_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description_en" class="form-label fw-semibold">Description (English)</label>
                                <textarea class="form-control @error('description_en') is-invalid @enderror"
                                    id="description_en" name="description_en" rows="4"
                                    placeholder="Describe this category in English...">{{ old('description_en') }}</textarea>
                                @error('description_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description_ar" class="form-label fw-semibold">Description (Arabic)</label>
                                <textarea class="form-control @error('description_ar') is-invalid @enderror"
                                    id="description_ar" name="description_ar" rows="4"
                                    placeholder="صف هذه الفئة بالعربية..." dir="rtl">{{ old('description_ar') }}</textarea>
                                @error('description_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Category Image --}}
                <div class="card border shadow-sm">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i class="mdi mdi-image-outline text-info me-2"></i>Category Image
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="image" class="form-label fw-semibold">Upload Image</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror"
                                id="image" name="image" accept="image/*" onchange="previewImage(event)">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="mdi mdi-information-outline me-1"></i>
                                Recommended: 400x400px, Max: 2MB (JPG, PNG)
                            </div>
                        </div>

                        <div id="image-preview" class="mt-3 text-center" style="display: none;">
                            <div class="border rounded-3 p-3 bg-light">
                                <p class="text-muted mb-2 small">Image Preview</p>
                                <img id="preview-img" src="" alt="Preview" class="img-thumbnail" style="max-height: 250px; max-width: 100%;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                {{-- Status --}}
                <div class="card border shadow-sm">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i class="mdi mdi-toggle-switch text-success me-2"></i>Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="p-3 bg-light rounded">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    <i class="mdi mdi-eye text-success me-1"></i>Active
                                </label>
                            </div>
                            <p class="text-muted fs-13 mb-0">Category will be visible to users and can be assigned to services</p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="card border shadow-sm">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="mdi mdi-check-circle me-1"></i> Create Category
                            </button>
                            <a href="{{ route('categories.index') }}" class="btn btn-soft-secondary">
                                <i class="mdi mdi-close-circle me-1"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Help Card --}}
                <div class="card border-0 bg-info-subtle">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <i class="mdi mdi-lightbulb-outline fs-24 text-info me-2"></i>
                            <div>
                                <h6 class="fw-semibold text-info">Category Tips</h6>
                                <ul class="small text-muted mb-0 ps-3">
                                    <li>Use clear, descriptive names</li>
                                    <li>Add high-quality images</li>
                                    <li>Keep descriptions concise</li>
                                    <li>Organize related services together</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-img').src = e.target.result;
                    document.getElementById('image-preview').style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection
