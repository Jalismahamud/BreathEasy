@extends('backend.app', ['title' => 'Daily Video Upload'])

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css">
    <style>
        .fc .fc-daygrid-day.fc-day-today {
            background: linear-gradient(90deg, #e6fffa 0%, #bbf7d0 100%) !important;
            border: 2px solid #22c55e;
        }
        .fc-event {
            background: #22c55e !important;
            border: none !important;
            color: #fff !important;
            border-radius: 0.5rem !important;
            font-weight: 500;
            cursor: pointer;
        }
        .fc-daygrid-event-dot {
            border-color: #22c55e !important;
        }
        .fc-toolbar-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #166534;
        }
        .fc-button-primary {
            background: #22c55e !important;
            border: none !important;
            color: #fff !important;
            border-radius: 0.5rem !important;
        }
        .fc-popover {
            z-index: 9999 !important;
        }
        .fc-popover .fc-popover-body {
            padding: 0.5rem;
        }
        .fc-popover video {
            width: 220px;
            border-radius: 0.5rem;
        }
        .calendar-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0.5rem;
            margin-top: 2rem;
        }
        .calendar-table th,
        .calendar-table td {
            text-align: center;
            vertical-align: middle;
            background: linear-gradient(90deg, #f8fafc 0%, #e0e7ef 100%);
            border-radius: 0.5rem;
            min-width: 90px;
            min-height: 90px;
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.07);
            position: relative;
            transition: box-shadow 0.2s;
        }
        .calendar-table td.has-video {
            border: 2px solid #3b82f6 !important; /* primary border */
        }
        .calendar-table td.has-video:hover {
            box-shadow: 0 4px 16px rgba(34, 197, 94, 0.25);
            background: linear-gradient(90deg, #e6fffa 0%, #bbf7d0 100%);
            cursor: pointer;
        }
        .calendar-table th {
            background: #e0e7ef;
            color: #2d3748;
            font-weight: 600;
        }
        .calendar-table td.today {
            border: 2px solid #22c55e;
        }
        .calendar-table td {
            height: 100px;
        }
        .calendar-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            background: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.25);
            padding: 1.5rem 2rem;
            text-align: center;
        }
        .calendar-popup video {
            width: 400px;
            max-width: 90vw;
            border-radius: 0.5rem;
        }
        .calendar-popup .close-hint {
            color: #888;
            font-size: 0.95rem;
            margin-top: 0.5rem;
        }
        .calendar-hover-card {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            width: 420px;
            max-width: 95vw;
            background: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.25);
            padding: 1.5rem 2rem;
            text-align: center;
        }
        .calendar-hover-card video {
            width: 400px;
            max-width: 90vw;
            border-radius: 0.5rem;
        }
        .calendar-hover-card .close-btn {
            position: absolute;
            top: 10px;
            right: 18px;
            background: transparent;
            border: none;
            font-size: 1.5rem;
            color: #888;
            cursor: pointer;
        }
        .calendar-hover-card .play-btn {
            display: inline-block;
            margin-top: 0.5rem;
            background: #22c55e;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            padding: 0.25rem 0.75rem;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .calendar-hover-card .play-btn:hover {
            background: #16a34a;
        }
        .calendar-year-scroll {
            max-height: 200px;
            overflow-y: auto;
            min-width: 80px;
            border: 1px solid #e0e7ef;
            border-radius: 0.5rem;
            background: #f8fafc;
        }
        .calendar-hover-card-list {
            display: none;
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translate(-50%, 10px);
            z-index: 10;
            background: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 4px 16px rgba(44, 62, 80, 0.15);
            padding: 0.5rem 1rem;
            min-width: 240px;
            text-align: center;
            white-space: nowrap;
            display: flex;
            flex-direction: row;
            gap: 1rem;
            max-width: 90vw;
            overflow-x: auto;
        }
        .calendar-table td.has-video:hover .calendar-hover-card-list {
            display: flex;
        }
        .calendar-hover-card-video-thumb {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0.5rem 0.5rem 0.5rem 0;
            cursor: pointer;
            vertical-align: top;
            min-width: 110px;
        }
        .calendar-hover-card-video-thumb video {
            border: 2px solid #e0e7ef;
            transition: border 0.2s;
        }
        .calendar-hover-card-video-thumb:hover video {
            border: 2px solid #3b82f6;
        }
    </style>
@endpush

@section('content')
<div class="app-content main-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Daily Video</h1>
                </div>
            </div>
            @if (session('t-success'))
                <div class="alert alert-success auto-dismiss">{{ session('t-success') }}</div>
            @elseif (session('t-error'))
                <div class="alert alert-danger auto-dismiss">{{ session('t-error') }}</div>
            @endif
            <form method="POST" action="{{ route('admin.daily-video.createOrUpdate') }}" enctype="multipart/form-data" class="mb-4">
                @csrf
                <div class="mb-3">
                    <label for="video" class="form-label">Video:</label>
                    <div id="video-preview-container" style="display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 0.5rem;"></div>
                    <input type="file" name="video[]" id="video" multiple
                        class="form-control @error('video') is-invalid @enderror" data-allowed-file-extensions="mp4" accept="video/mp4" style="padding: 0.5rem; border-radius: 0.5rem; border: 1.5px solid #3b82f6; background: #f8fafc;" />
                    <div id="video-error" class="text-danger mt-1"></div>
                    @error('video')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary mt-2">Save Video</button>
            </form>
            <hr>
            <h5 class="mt-4">Calendar View</h5>
            @php
                $today = \Carbon\Carbon::today();
                $selectedMonth = (int) request('month', $today->month);
                $selectedYear = (int) request('year', $today->year);
                $monthStart = \Carbon\Carbon::create($selectedYear, $selectedMonth, 1);
                $monthEnd = $monthStart->copy()->endOfMonth();
                $calendar = [];
                foreach ($videos as $video) {
                    $date = $video->created_at->toDateString();
                    $calendar[$date][] = $video;
                }
            @endphp
            <table class="calendar-table">
                <thead>
                    <tr>
                        <th>Sun</th>
                        <th>Mon</th>
                        <th>Tue</th>
                        <th>Wed</th>
                        <th>Thu</th>
                        <th>Fri</th>
                        <th>Sat</th>
                    </tr>
                </thead>
                <tbody>
                @php $date = $monthStart->copy()->startOfWeek(); @endphp
    @while ($date->lte($monthEnd->copy()->endOfWeek()))
        <tr>
            @for ($d = 0; $d < 7; $d++)
                @if ($date->month == $selectedMonth)
                    <td class="{{ $date->isToday() ? 'today' : '' }} {{ isset($calendar[$date->toDateString()]) ? 'has-video' : '' }}" data-date="{{ $date->toDateString() }}">
                        <div>{{ $date->format('j') }}</div>
                        @if(isset($calendar[$date->toDateString()]))
                            <div class="calendar-hover-card-list">
                                @foreach($calendar[$date->toDateString()] as $video)
                                    <div class="calendar-hover-card-video-thumb" data-video-url="{{ asset($video->video) }}" data-uploaded="{{ $video->created_at->timezone('UTC')->toDayDateTimeString() }}">
                                        <video muted style="width: 100px; height: 60px; border-radius: 0.5rem; object-fit: cover;">
                                            <source src="{{ asset($video->video) }}" type="video/mp4">
                                        </video>
                                        <div class="small text-muted">{{ $video->created_at->format('H:i') }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </td>
                @else
                    <td style="background: none; box-shadow: none;"></td>
                @endif
                @php $date->addDay(); @endphp
            @endfor
        </tr>
    @endwhile
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        setTimeout(function() {
            $('.auto-dismiss').fadeOut('slow');
        }, 5000);

        // Show/hide video list on hover
        $(document).on('mouseenter', '.calendar-table td.has-video', function() {
            const $list = $(this).find('.calendar-hover-card-list');
            $list.css('width', 'auto');
            let totalWidth = 0;
            $list.find('.calendar-hover-card-video-thumb').each(function() {
                totalWidth += $(this).outerWidth(true);
            });
            $list.css('min-width', Math.max(240, totalWidth + 32) + 'px');
            $list.stop(true, true).fadeIn(120);
        });
        $(document).on('mouseleave', '.calendar-table td.has-video', function() {
            $(this).find('.calendar-hover-card-list').stop(true, true).fadeOut(80);
        });

        // Card popup logic for multiple videos per day
        let cardOpen = false;
        let cardEl = null;
        $(document).on('click', '.calendar-hover-card-video-thumb', function(e) {
            e.stopPropagation();
            const videoUrl = $(this).data('video-url');
            const uploaded = $(this).data('uploaded');
            if (!videoUrl) return;
            if (cardOpen && cardEl) { cardEl.remove(); cardOpen = false; }
            cardEl = $('<div class="calendar-hover-card" style="display:block;">' +
                '<button class="close-btn" title="Close">&times;</button>' +
                '<video controls poster="{{ asset('default/video.png') }}" style="width: 300px; max-width: 90vw; border-radius: 0.5rem;">' +
                '<source src="' + videoUrl + '" type="video/mp4">Your browser does not support the video tag.</video>' +
                '<div class="small text-muted mt-1">Uploaded: ' + uploaded + '</div>' +
                '<div class="close-hint">Double click video for fullscreen. Click outside to close.</div>' +
            '</div>');
            $('body').append(cardEl);
            cardOpen = true;
            setTimeout(function() {
                cardEl.find('video')[0].focus();
            }, 100);
            setTimeout(function() {
                $(document).on('mousedown.card', function(ev) {
                    if (cardEl && !$(ev.target).closest('.calendar-hover-card').length) {
                        cardEl.remove();
                        cardOpen = false;
                        $(document).off('mousedown.card');
                    }
                });
            }, 10);
            cardEl.find('.close-btn').on('click', function() {
                cardEl.remove();
                cardOpen = false;
                $(document).off('mousedown.card');
            });
            cardEl.find('video').on('dblclick', function(e) {
                e.stopPropagation();
                if (this.requestFullscreen) this.requestFullscreen();
                else if (this.webkitRequestFullscreen) this.webkitRequestFullscreen();
                else if (this.msRequestFullscreen) this.msRequestFullscreen();
            });
        });
        $(document).on('dblclick', function(e) {
            if (cardEl && !$(e.target).closest('.calendar-hover-card').length) {
                cardEl.remove(); cardOpen = false;
            }
        });
        $('#goto-current').on('click', function() {
            const today = new Date();
            const month = today.getMonth() + 1;
            const year = today.getFullYear();
            window.location.href = '?month=' + month + '&year=' + year;
        });

        const videoInput = document.getElementById('video');
        const previewContainer = document.getElementById('video-preview-container');
        const errorDiv = document.getElementById('video-error');
        videoInput.addEventListener('change', function(e) {
            previewContainer.innerHTML = '';
            errorDiv.textContent = '';
            let hasError = false;
            Array.from(this.files).forEach(file => {
                if (file.type !== 'video/mp4') {
                    errorDiv.textContent = 'Only MP4 video files are allowed.';
                    hasError = true;
                    return;
                }
                if (file.size > 50 * 1024 * 1024) {
                    errorDiv.textContent = 'Each video must be less than 50MB.';
                    hasError = true;
                    return;
                }
                const videoBox = document.createElement('div');
                videoBox.style.width = '120px';
                videoBox.style.textAlign = 'center';
                videoBox.style.position = 'relative';
                videoBox.style.background = '#f0f4f8';
                videoBox.style.borderRadius = '0.5rem';
                videoBox.style.padding = '0.5rem';
                videoBox.style.boxShadow = '0 2px 8px rgba(44,62,80,0.07)';
                const video = document.createElement('video');
                video.src = URL.createObjectURL(file);
                video.controls = true;
                video.muted = true;
                video.style.width = '100%';
                video.style.height = '80px';
                video.style.objectFit = 'cover';
                video.style.borderRadius = '0.5rem';
                videoBox.appendChild(video);
                const name = document.createElement('div');
                name.textContent = file.name;
                name.style.fontSize = '0.85rem';
                name.style.marginTop = '0.25rem';
                videoBox.appendChild(name);
                previewContainer.appendChild(videoBox);
            });
            if (hasError) {
                this.value = '';
                previewContainer.innerHTML = '';
            }
        });
    });
</script>

@endpush



