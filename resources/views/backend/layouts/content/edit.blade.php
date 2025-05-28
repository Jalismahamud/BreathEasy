@extends('backend.app', ['title' => 'Edit Content'])

@section('content')

<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Edit Content</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.content.index') }}">Contents</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </div>
            </div>

            <div class="row" id="user-profile">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body border-0">
                            <form class="form-horizontal" method="post" action="{{ route('admin.content.update', $data->id) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label">Title:</label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                           value="{{ old('title', $data->title) }}" placeholder="Enter content title">
                                    @error('title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Category:</label>
                                    <select name="category_id" class="form-control @error('category_id') is-invalid @enderror">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $data->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Content Type:</label>
                                    <select name="content_type_id" class="form-control @error('content_type_id') is-invalid @enderror">
                                        <option value="">Select Content Type</option>
                                        @foreach ($contentTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('content_type_id', $data->content_type_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('content_type_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Content Duration:</label>
                                    <select name="content_duration_id" class="form-control @error('content_duration_id') is-invalid @enderror">
                                        <option value="">Select Duration</option>
                                        @foreach ($durations as $duration)
                                            <option value="{{ $duration->id }}" {{ old('content_duration_id', $data->content_duration_id) == $duration->id ? 'selected' : '' }}>
                                                {{ $duration->length }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('content_duration_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Type:</label>
                                    <input type="text" name="type" class="form-control @error('type') is-invalid @enderror"
                                           value="{{ old('type', $data->type) }}" placeholder="Enter type">
                                    @error('type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description:</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                              placeholder="Enter content description">{{ old('description', $data->description) }}</textarea>
                                    @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Upload Video (optional):</label>
                                    <input type="file" name="video" class="form-control @error('video') is-invalid @enderror">
                                    @if ($data->video)
                                        <div class="mt-2">
                                            <label>Current Video:</label>
                                            <video width="320" height="240" controls>
                                                <source src="{{ asset($data->video) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    @endif
                                    @error('video')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mt-4">
                                    <button class="btn btn-primary" type="submit">Update</button>
                                    <a href="{{ route('admin.content.index') }}" class="btn btn-danger">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
