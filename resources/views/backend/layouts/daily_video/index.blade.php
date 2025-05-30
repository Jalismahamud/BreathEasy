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
                    <input type="file" name="video" id="video"
                        class="dropify form-control @error('video') is-invalid @enderror" data-allowed-file-extensions="mp4"
                        data-default-file="{{ isset($latestVideo) && file_exists(public_path($latestVideo->video)) ? asset($latestVideo->video) : asset('default/video.png') }}">
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
                    $calendar[$date] = $video;
                }
            @endphp
            <div class="d-flex align-items-center mb-3">
                <form method="GET" class="d-flex align-items-center gap-2" style="gap: 0.5rem;">
                    <label for="month" class="form-label mb-0">Month:</label>
                    <select name="month" id="month" class="form-select form-select-sm" style="width: auto;">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                        @endfor
                    </select>
                    <label for="year" class="form-label mb-0 ms-2">Year:</label>
                    <div class="calendar-year-scroll">
                        <select name="year" id="year" class="form-select form-select-sm" style="width: 100%; background: transparent; border: none; box-shadow: none;">
                            @php
                                $minYear = 2020;
                                $maxYear = $today->year;
                            @endphp
                            @for ($y = $minYear; $y <= $maxYear; $y++)
                                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <button type="submit" class="btn btn-outline-primary btn-sm ms-2">Go</button>
                    <button type="button" id="goto-current" class="btn btn-success btn-sm ms-2">Current Month</button>
                </form>
            </div>
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
                    <td class="{{ $date->isToday() ? 'today' : '' }} {{ isset($calendar[$date->toDateString()]) ? 'has-video' : '' }}" data-date="{{ $date->toDateString() }}" @if(isset($calendar[$date->toDateString()])) data-video-url="{{ asset($calendar[$date->toDateString()]->video) }}" data-uploaded="{{ $calendar[$date->toDateString()]->created_at->timezone('UTC')->toDayDateTimeString() }}" @endif>
                        <div>{{ $date->format('j') }}</div>
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
        $('.dropify').dropify();
        setTimeout(function() {
            $('.auto-dismiss').fadeOut('slow');
        }, 5000);

        // Card popup logic
        let cardOpen = false;
        let cardEl = null;
        $('.calendar-table td.has-video').on('click', function(e) {
            e.stopPropagation();
            const videoUrl = $(this).data('video-url');
            const uploaded = $(this).data('uploaded');
            if (!videoUrl) return;
            if (cardOpen && cardEl) { cardEl.remove(); cardOpen = false; }
            cardEl = $('<div class="calendar-hover-card" style="display:block;">' +
                '<button class="close-btn" title="Close">&times;</button>' +
                '<video controls poster="' + '{{ asset('default/video.png') }}' + '" style="width: 300px; max-width: 90vw; border-radius: 0.5rem;">' +
                '<source src="' + videoUrl + '" type="video/mp4">Your browser does not support the video tag.</video>' +
                '<div class="small text-muted mt-1">Uploaded: ' + uploaded + '</div>' +
                '<div class="close-hint">Double click video for fullscreen. Click outside to close.</div>' +
            '</div>');
            $('body').append(cardEl);
            cardOpen = true;
            // Focus the video for keyboard controls
            setTimeout(function() {
                cardEl.find('video')[0].focus();
            }, 100);
            // Close on click outside
            setTimeout(function() {
                $(document).on('mousedown.card', function(ev) {
                    if (cardEl && !$(ev.target).closest('.calendar-hover-card').length) {
                        cardEl.remove();
                        cardOpen = false;
                        $(document).off('mousedown.card');
                    }
                });
            }, 10);
            // Close on close button
            cardEl.find('.close-btn').on('click', function() {
                cardEl.remove();
                cardOpen = false;
                $(document).off('mousedown.card');
            });
            // Double click video for fullscreen
            cardEl.find('video').on('dblclick', function(e) {
                e.stopPropagation();
                if (this.requestFullscreen) this.requestFullscreen();
                else if (this.webkitRequestFullscreen) this.webkitRequestFullscreen();
                else if (this.msRequestFullscreen) this.msRequestFullscreen();
            });
        });
        // Remove card on double click outside
        $(document).on('dblclick', function(e) {
            if (cardEl && !$(e.target).closest('.calendar-hover-card').length) {
                cardEl.remove(); cardOpen = false;
            }
        });
        // Go to current month button
        $('#goto-current').on('click', function() {
            const today = new Date();
            const month = today.getMonth() + 1;
            const year = today.getFullYear();
            window.location.href = '?month=' + month + '&year=' + year;
        });
    });
</script>
@endpush
