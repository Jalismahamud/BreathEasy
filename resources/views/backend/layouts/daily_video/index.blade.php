@extends('backend.app', ['title' => 'Daily Video Upload'])

@section('content')
    <div class="container mt-4">
        <h4>Daily Video Upload</h4>

        @if (session('t-success'))
            <div class="alert alert-success auto-dismiss">{{ session('t-success') }}</div>
        @elseif (session('t-error'))
            <div class="alert alert-danger auto-dismiss">{{ session('t-error') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.daily-video.createOrUpdate') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="video" class="form-label">Video:</label>
                <input type="file" name="video" id="video"
                       class="dropify form-control @error('video') is-invalid @enderror"
                       data-allowed-file-extensions="mp4"
                       data-default-file="{{ isset($latestVideo) && file_exists(public_path($latestVideo->video)) ? asset($latestVideo->video) : asset('default/video.png') }}">
                @error('video')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary mt-2">Save Video</button>
        </form>

        <hr>

        <h5 class="mt-4">Latest Videos</h5>
        <div class="row">
            @forelse ($videos as $video)
                <div class="col-md-4 mb-3">
                    <video width="100%" height="auto" controls>
                        <source src="{{ asset($video->video) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <p class="mt-1 text-muted">
                        Uploaded: {{ $video->created_at->timezone('UTC')->toDayDateTimeString() }}
                    </p>
                </div>
            @empty
                <p>No videos uploaded yet.</p>
            @endforelse
        </div>

        <div class="mt-3">
            {{ $videos->links() }}
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Initialize Dropify
    $(document).ready(function () {
        $('.dropify').dropify();

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function () {
            $('.auto-dismiss').fadeOut('slow');
        }, 5000);
    });
</script>
@endpush
