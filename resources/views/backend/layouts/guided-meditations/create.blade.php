@extends('backend.app', ['title' => 'Create Guided Meditation'])

@section('content')
<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Create Guided Meditation</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.content.guided-meditations.index') }}">Guided Meditations</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    </ol>
                </div>
            </div>

            <div class="row" id="user-profile">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body border-0">
                            <form class="form-horizontal" method="post" action="{{ route('admin.content.guided-meditations.store') }}" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Title:</label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                           value="{{ old('title') }}" placeholder="Enter meditation title">
                                    @error('title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Content Type:</label>
                                    <select name="content_type_id" class="form-select shadow-sm border-primary @error('content_type_id') is-invalid @enderror" style="background: linear-gradient(90deg, #f0f4f8 0%, #d9e2ec 100%); border-radius: 0.5rem; font-weight: 500; color: #2d3748; min-height: 48px;" aria-label="Select Content Type">
                                        <option value="" selected disabled>Select Content Type</option>
                                        @foreach ($contentTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('content_type_id') == $type->id ? 'selected' : '' }}>{{ $type->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('content_type_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Type:</label>
                                    <select name="type" class="form-select form-select-lg shadow-sm border-primary @error('type') is-invalid @enderror" style="background: linear-gradient(90deg, #f8fafc 0%, #e0e7ef 100%); border-radius: 0.5rem; font-weight: 500; color: #2d3748; min-height: 48px;" aria-label="Select Type">
                                        <option value="" selected disabled>Select Type</option>
                                        <option value="Beginner" {{ old('type') == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                                        <option value="Intermediate" {{ old('type') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                                        <option value="Advanced" {{ old('type') == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                                    </select>
                                    @error('type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description:</label>
                                    <textarea name="description" class="summernote  form-control @error('description') is-invalid @enderror"
                                              placeholder="Enter meditation description">{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                               <div class="row mb-3">
                           <div class="mb-3">
                                    <label class="form-label">Image (optional):</label>
                                    <input type="file" name="image" class="dropify form-control @error('image') is-invalid @enderror" accept="image/*">
                                    @error('image')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Audio File:</label>
                                    <input type="file" name="audio" class=" dropify form-control @error('audio') is-invalid @enderror" accept="audio/*">
                                    @error('audio')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                </div>

                                <!-- Hidden field for JS-based audio length -->
                                <input type="hidden" name="audio_length" id="audioLength">

                                <div class="form-group mt-4">
                                    <button class="btn btn-primary" type="submit">Create</button>
                                    <a href="{{ route('admin.content.guided-meditations.index') }}" class="btn btn-danger">Cancel</a>
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

@push('scripts')
<script>
document.querySelector('input[name="audio"]').addEventListener('change', function (event) {
    const file = event.target.files[0];
    if (!file) return;

    const audio = document.createElement('audio');
    audio.preload = 'metadata';

    audio.onloadedmetadata = function () {
        window.URL.revokeObjectURL(audio.src);
        const duration = audio.duration;

        const hours = Math.floor(duration / 3600);
        const minutes = Math.floor((duration % 3600) / 60);
        const seconds = Math.floor(duration % 60);

        const formatted = [hours, minutes, seconds]
            .map(unit => String(unit).padStart(2, '0'))
            .join(':');

        document.getElementById('audioLength').value = formatted;
        console.log('Audio duration:', formatted);
    };

    audio.src = URL.createObjectURL(file);
});
</script>


<script>
    $(document).ready(function () {
        // customize Dropify for video only, increase box height, and show error for non-video files
        $('.dropify').dropify({
            messages: {
                'default': 'Drag and drop a audio here or click to select',
                'replace': 'Drag and drop or click to replace',
                'remove':  'Remove',
                'error':   'Oops, something wrong appended.'
            },
            error: {
                'fileSize': 'The file size is too big.',
                'fileExtension': 'Only video files (mp3,wav,m4a) are allowed.'
            }
        });
    });
</script>
@endpush
