@extends('backend.app', ['title' => 'Create Content'])

@section('content')


<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Create Content</h1>
                </div>
                <div class="ms-auto pageheader-btn">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.content.index') }}">Contents</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    </ol>
                </div>
            </div>

            <div class="row" id="user-profile">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body border-0">
                            <form class="form-horizontal" method="post" action="{{ route('admin.content.store') }}" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Title:</label>
                                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                           value="{{ old('title') }}" placeholder="Enter content title">
                                    @error('title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Category:</label>
                                    <select name="category_id" class="form-select shadow-sm border-primary @error('category_id') is-invalid @enderror" style="background: linear-gradient(90deg, #f0f4f8 0%, #d9e2ec 100%); border-radius: 0.5rem; font-weight: 500; color: #2d3748; min-height: 48px;" aria-label="Select Category">
                                        <option value="" selected disabled>Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                    <select name="content_type_id" class="form-select shadow-sm border-primary @error('content_type_id') is-invalid @enderror" style="background: linear-gradient(90deg, #f0f4f8 0%, #d9e2ec 100%); border-radius: 0.5rem; font-weight: 500; color: #2d3748; min-height: 48px;" aria-label="Select Content Type">
                                        <option value="" selected disabled>Select Content Type</option>
                                        @foreach ($contentTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('content_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('content_type_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>


                                <div class="mb-3">
                                    <label class="form-label">Type:</label>
                                    <select name="type" class="form-select form-select-lg shadow-sm border-primary @error('type') is-invalid @enderror" style="background: linear-gradient(90deg, #f8fafc 0%, #e0e7ef 100%); border-radius: 0.5rem; font-weight: 500; color: #2d3748; min-height: 48px;" aria-label="Select Type">
                                        <option value="" disabled {{ old('type') == null ? 'selected' : '' }}>Select Type</option>
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
                                              placeholder="Enter content description">{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Upload Image:</label>
                                        <input type="file" name="image" class="dropify form-control @error('image') is-invalid @enderror" data-allowed-file-extensions="jpg jpeg png gif" data-max-file-size="50M" style="height: 150px;" />
                                        @error('image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Upload Video:</label>
                                        <input type="file" id="video-file-input" name="video" class="dropify form-control @error('video') is-invalid @enderror" data-allowed-file-extensions="mp4 avi mov mkv wmv flv webm mpeg mpg 3gp 3g2 ogv mts m2ts ts f4v vob" style="height: 120px;" />
                                        <div class="d-flex align-items-center mt-2">
                                            <button type="button" id="content-upload-btn" class="btn btn-primary btn-sm me-2">Chunk Upload Video</button>
                                            <div id="content-upload-progress" style="display:none; width:200px; background:#f1f5f9; border-radius:6px; overflow:hidden;">
                                                <div id="content-upload-bar" style="width:0%; height:12px; background:#22c55e;"></div>
                                            </div>
                                        </div>
                                        @error('video')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        {{-- This hidden input will be set to the uploaded path returned by chunkUpload endpoint --}}
                                        <input type="hidden" name="video_path" id="video_path">
                                    </div>
                                </div>

                                <!-- Hidden field for JS-based video length -->
                                <input type="hidden" name="video_length" id="videoLength">

                                <div class="form-group mt-4 content-submit" style="display:none;">
                                    <button class="btn btn-primary" type="submit">Create</button>
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


@push('scripts')

<!-- JS: Auto calculate video duration -->
<script>
document.querySelector('input[name="video"]').addEventListener('change', function (event) {
    event.preventDefault(); // Prevent form auto-submit/reload on mobile
    const file = event.target.files[0];
    if (!file) return;

    const video = document.createElement('video');
    video.preload = 'metadata';

    video.onloadedmetadata = function () {
        window.URL.revokeObjectURL(video.src);
        const duration = video.duration;

        const hours = Math.floor(duration / 3600);
        const minutes = Math.floor((duration % 3600) / 60);
        const seconds = Math.floor(duration % 60);

        const formatted = [hours, minutes, seconds]
            .map(unit => String(unit).padStart(2, '0'))
            .join(':');

        document.getElementById('videoLength').value = formatted;
        console.log('Video duration:', formatted);
    };

    video.src = URL.createObjectURL(file);
});
// Also prevent default for image input
document.querySelector('input[name="image"]').addEventListener('change', function (event) {
    event.preventDefault();
});
</script>

<script>
    $(document).ready(function () {
        // customize Dropify for video only, increase box height, and show error for non-video files
        $('.dropify').dropify({
            messages: {
                'default': 'Drag and drop a video here or click to select',
                'replace': 'Drag and drop or click to replace',
                'remove':  'Remove',
                'error':   'Oops, something wrong appended.'
            },
            error: {
                'fileSize': 'The file size is too big.',
                'fileExtension': 'Only video files (mp4, mov, avi, wmv) are allowed.'
            }
        });
    });
</script>

<script>
    $(document).ready(function () {
        const chunkSize = 2 * 1024 * 1024; // 2MB
        const uploadUrl = "{{ route('admin.content.chunkUpload') }}";

        function showContentToast(msg, type = 'success') {
            const t = $('<div>').text(msg).css({position:'fixed', top:'30px', right:'30px', background: type==='success' ? '#22c55e' : '#ef4444', color:'#fff', padding:'10px 16px', 'z-index':99999, 'border-radius':'6px'});
            $('body').append(t);
            setTimeout(()=>t.fadeOut(300, ()=>t.remove()), 3000);
        }

        $('#content-upload-btn').on('click', function () {
            const fileInput = document.getElementById('video-file-input');
            const file = fileInput.files[0];
            if (!file) { showContentToast('Please select a video file first', 'error'); return; }

            const timestamp = Date.now();
            const random = Math.floor(Math.random() * 1000000000);
            const ext = file.name.split('.').pop();
            const fileName = `${timestamp}-${random}.${ext}`;
            const totalChunks = Math.ceil(file.size / chunkSize);
            let currentChunk = 0;

            $('#content-upload-progress').show();

            function uploadNext() {
                const start = currentChunk * chunkSize;
                const end = Math.min(file.size, start + chunkSize);
                const blob = file.slice(start, end);
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('file', blob);
                formData.append('fileName', fileName);
                formData.append('chunkIndex', currentChunk);
                formData.append('totalChunks', totalChunks);

                $.ajax({
                    url: uploadUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (resp) {
                        currentChunk++;
                        const progress = Math.floor((currentChunk / totalChunks) * 100);
                        $('#content-upload-bar').css('width', progress + '%').text(progress + '%').css({'color':'#fff','text-align':'center','font-size':'12px'});
                        if (currentChunk < totalChunks) {
                            uploadNext();
                        } else {
                            // If server returned path in response, set video_path and enable submit
                            if (resp && resp.path) {
                                $('#video_path').val(resp.path);
                                // prevent the full file being submitted with the form
                                try {
                                    const fi = document.getElementById('video-file-input');
                                    if (fi) {
                                        fi.removeAttribute('name');
                                        fi.disabled = true;
                                    }
                                } catch (e) { console.warn(e); }
                                $('#content-upload-btn').prop('disabled', true).text('Uploaded');
                                $('#content-upload-bar').css('width', '100%').text('100%');
                                showContentToast('Upload complete');
                                $('.content-submit').show();
                            } else {
                                // chunk upload finished but server did not return a path — do not show submit
                                $('#content-upload-bar').css('width', '100%').text('100%');
                                showContentToast('Upload complete');
                            }
                        }
                    },
                    error: function (err) {
                        console.error(err);
                        showContentToast('Upload failed', 'error');
                    }
                });
            }

            uploadNext();
        });
    });
</script>

<!-- Do not auto show submit on file selection. The submit will be shown only after successful chunk upload returns a path. -->

@endpush


