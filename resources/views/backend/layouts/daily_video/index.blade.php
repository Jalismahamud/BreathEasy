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
            border: 2px solid #3b82f6 !important;
            /* primary border */
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
            width: 540px;
            max-width: 98vw;
            background: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.25);
            padding: 2rem 2.5rem;
            text-align: center;
        }

        .calendar-hover-card video {
            width: 480px;
            max-width: 95vw;
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

        .calendar-hover-card .close-hint {
            color: #888;
            font-size: 0.95rem;
            margin-top: 0.5rem;
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
            /* keep in-cell list for accessibility, but hide visually by default; we'll clone/float it on hover */
            display: none;
            visibility: hidden;
        }

        /* Floating clone appended to body so it won't be clipped by table/container overflow */
        .floating-calendar-hover-card-list {
            display: flex !important;
            justify-content: center;
            align-items: center;
            position: absolute !important;
            z-index: 20000 !important;
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 12px 48px rgba(44, 62, 80, 0.28);
            padding: 0.5rem 0.75rem;
            min-width: 120px;
            text-align: center;
            white-space: nowrap;
            max-width: 95vw;
            overflow-x: auto;
            gap: 0.25rem;
            transform-origin: top center;
        }

        .calendar-hover-card-video-thumb {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            cursor: pointer;
            vertical-align: top;
            min-width: 100px;
            padding: 0.25rem 0.5rem;
        }

        .calendar-hover-card-video-thumb video {
            border: 2px solid #e0e7ef;
            transition: border 0.2s;
            width: 100px !important;
            height: 50px !important;
            object-fit: cover;
            border-radius: 0.5rem;
            background: #f8fafc;
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.07);
            display: block;
            margin: 0 auto;
        }

        .calendar-hover-card-video-thumb:hover video {
            border: 2px solid #3b82f6;
        }

        .calendar-hover-card-video-thumb .small.text-muted {
            margin-top: 0.25rem;
            font-size: 0.95rem;
            color: #666;
            text-align: center;
        }
    </style>
@endpush

@section('content')
    @php
        $today = \Carbon\Carbon::today('UTC');
        $selectedMonth = isset($selectedMonth) ? $selectedMonth : (int) request('month', $today->month);
        $selectedYear = isset($selectedYear) ? $selectedYear : (int) request('year', $today->year);
    @endphp
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
                <form id="chunk-upload-form" enctype="multipart/form-data" class="mb-4">
                    @csrf
                    <div class="mb-3">
                        <label for="video" class="form-label">Video:</label>
                        <input type="file" name="video" id="video" class="form-control" accept="video/*" />
                        <div id="upload-progress" style="margin-top: 10px; display:none;">
                            <div id="progress-bar" style="width:0%; height:20px; background:#22c55e; border-radius:5px;">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="upload-date" class="form-label">Upload Date:</label>
                        <input type="date" id="upload-date" name="upload_date" class="form-control"
                            value="{{ \Carbon\Carbon::today('UTC')->toDateString() }}" />
                        <div class="form-text">Choose the date this video should be assigned to (you can select future
                            dates).</div>
                    </div>
                    <button type="button" id="upload-btn" class="btn btn-primary mt-2">Upload Video</button>
                </form>

                <hr>
                <div class="d-flex align-items-center mb-3">
                    <label for="month-filter" class="me-2 mb-0">Month:</label>
                    <select id="month-filter" class="form-select me-3" style="width: 120px; display: inline-block;">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" @if ($m == $selectedMonth) selected @endif>
                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                        @endfor
                    </select>
                    <label for="year-filter" class="me-2 mb-0">Year:</label>
                    <select id="year-filter" class="form-select" style="width: 100px; display: inline-block;">
                        @for ($y = $today->year - 1; $y <= $today->year + 5; $y++)
                            <option value="{{ $y }}" @if ($y == $selectedYear) selected @endif>
                                {{ $y }}</option>
                        @endfor
                    </select>
                    <button id="filter-btn" class="btn btn-outline-primary ms-3">Filter</button>
                    <button id="reset-btn" class="btn btn-outline-secondary ms-2">Reset Filter</button>
                </div>
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
                                        <td class="{{ $date->isToday() ? 'today' : '' }} {{ isset($calendar[$date->toDateString()]) ? 'has-video' : '' }}"
                                            data-date="{{ $date->toDateString() }}">
                                            <div>{{ $date->format('j') }}</div>
                                            @if (isset($calendar[$date->toDateString()]))
                                                <div class="calendar-hover-card-list">

                                                    @foreach ($calendar[$date->toDateString()] as $video)
                                                        <div class="calendar-hover-card-video-thumb"
                                                            data-video-url="{{ asset($video->video) }}"
                                                            data-uploaded="{{ $video->created_at->timezone('UTC')->toDayDateTimeString() }}">
                                                            <video muted preload="metadata"
                                                                onloadedmetadata="this.nextElementSibling.textContent = Math.floor(this.duration/60) + ':' + ('0' + Math.floor(this.duration%60)).slice(-2)"
                                                                style="width: 50px; height: 30px; border-radius: 0.5rem; object-fit: cover;">
                                                                <source src="{{ asset($video->video) }}" type="video/mp4">
                                                                <source src="{{ asset($video->video) }}" type="video/*">
                                                            </video>
                                                            <div class="small text-muted" data-duration="loading...">
                                                                {{ $video->created_at->format('H:i') }}
                                                            </div>
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


            $('#month-filter, #year-filter').on('change', function() {
                var selectedMonth = $('#month-filter').val();
                var selectedYear = $('#year-filter').val();
                $('.calendar-table td').each(function() {
                    var cellDate = $(this).data('date');
                    if (!cellDate) return;
                    var dateObj = new Date(cellDate);
                    var show = true;
                    if (selectedMonth && (dateObj.getMonth() + 1) != selectedMonth) show = false;
                    if (selectedYear && dateObj.getFullYear() != selectedYear) show = false;
                    $(this).toggle(show);
                });
            });

            $('#filter-btn').on('click', function() {
                var month = $('#month-filter').val();
                var year = $('#year-filter').val();
                window.location.href = '?month=' + month + '&year=' + year;
            });

            $('#reset-btn').on('click', function() {
                const today = new Date();
                const month = today.getMonth() + 1;
                const year = today.getFullYear();
                window.location.href = '?month=' + month + '&year=' + year;
            });


            let floatingList = null;
            let hoverTimeout = null;

            $(document).on('mouseenter', '.calendar-table td.has-video', function(e) {
                const $cell = $(this);
                const $list = $cell.find('.calendar-hover-card-list');

                if (!$list.length) return;


                $list.find('video').each(function() {
                    const video = this;
                    const $durationDiv = $(this).next('.small.text-muted');

                    if (!this.hasAttribute('data-loaded')) {
                        this.preload = 'metadata';
                        this.load();
                        this.setAttribute('data-loaded', 'true');

                        $(this).on('loadedmetadata', function() {
                            if (video.duration && !isNaN(video.duration) && video.duration >
                                0) {
                                const minutes = Math.floor(video.duration / 60);
                                const seconds = Math.floor(video.duration % 60);
                                const durationText = minutes + ':' + (seconds < 10 ? '0' :
                                    '') + seconds;
                                $durationDiv.text(durationText);
                            }
                        });

                        $(this).on('error', function() {
                            console.error('Video failed to load metadata');
                            $durationDiv.text('Error');
                        });
                    }
                });

                if (floatingList) {
                    floatingList.remove();
                    floatingList = null;
                }

                floatingList = $list.clone(true, true).removeClass('calendar-hover-card-list').addClass(
                    'floating-calendar-hover-card-list');
                $('body').append(floatingList);

                const cellRect = $cell[0].getBoundingClientRect();
                const listWidth = Math.min(Math.max(240, floatingList.outerWidth(true)), $(window).width() -
                    40);
                floatingList.css('width', listWidth + 'px');

                const spaceBelow = $(window).height() - cellRect.bottom;
                const spaceAbove = cellRect.top;
                let top, left;
                left = cellRect.left + (cellRect.width / 2) - (listWidth / 2);
                left = Math.max(12, Math.min(left, $(window).width() - listWidth - 12));

                if (spaceBelow > 120 || spaceBelow > spaceAbove) {

                    top = cellRect.bottom + 10 + window.scrollY;
                } else {

                    top = cellRect.top - floatingList.outerHeight(true) - 10 + window.scrollY;
                }

                floatingList.css({
                    top: top + 'px',
                    left: left + 'px'
                });
                floatingList.stop(true, true).fadeIn(120);


                clearTimeout(hoverTimeout);
                hoverTimeout = setTimeout(function() {

                }, 0);


            });


            $(document).on('mouseleave', '.calendar-table td.has-video', function() {

                if (hoverTimeout) clearTimeout(hoverTimeout);
                hoverTimeout = setTimeout(function() {
                    if (floatingList) {
                        floatingList.stop(true, true).fadeOut(80, function() {
                            $(this).remove();
                        });
                        floatingList = null;
                    }
                }, 120);
            });


            $(document).on('mouseenter', '.floating-calendar-hover-card-list', function() {
                if (hoverTimeout) clearTimeout(hoverTimeout);
            });
            $(document).on('mouseleave', '.floating-calendar-hover-card-list', function() {
                var $fl = $(this);
                hoverTimeout = setTimeout(function() {
                    $fl.stop(true, true).fadeOut(80, function() {
                        $(this).remove();
                    });
                    floatingList = null;
                }, 120);
            });


            let cardOpen = false;
            let cardEl = null;


            $(document).on('click', '.calendar-hover-card-video-thumb', function(e) {
                e.stopPropagation();
                const videoUrl = $(this).data('video-url');
                const uploaded = $(this).data('uploaded');

                if (!videoUrl) {
                    console.warn('No video URL found');
                    return;
                }

                if (cardOpen && cardEl) {
                    cardEl.remove();
                    cardOpen = false;
                }

                cardEl = $('<div class="calendar-hover-card" style="display:block;">' +
                    '<button class="close-btn" title="Close">&times;</button>' +
                    '<video controls preload="auto" autoplay muted poster="{{ asset('default/video.png') }}" style="width: 480px; max-width: 95vw; border-radius: 0.5rem;">' +
                    '<source src="' + videoUrl + '" type="video/mp4">' +
                    '<source src="' + videoUrl + '" type="video/*">' +
                    'Your browser does not support the video tag.' +
                    '</video>' +
                    '<div class="small text-muted mt-1">Uploaded: ' + uploaded + '</div>' +
                    '<div class="close-hint">Double click video for fullscreen. Click outside to close.</div>' +
                    '</div>');

                $('body').append(cardEl);
                cardOpen = true;


                const mainVideo = cardEl.find('video')[0];
                mainVideo.load();


                $(mainVideo).on('canplay', function() {

                    console.log('Video ready to play');
                }).on('loadedmetadata', function() {

                    console.log('Video metadata loaded, duration:', this.duration);
                }).on('error', function(e) {
                    console.error('Main video failed to load:', videoUrl, e);
                    $(this).after(
                        '<div class="text-danger mt-2">Video failed to load. Please check the file.</div>'
                        );
                });

                setTimeout(function() {
                    mainVideo.focus();
                }, 100);

                setTimeout(function() {
                    $(document).on('mousedown.card', function(ev) {
                        if (cardEl && !$(ev.target).closest('.calendar-hover-card')
                            .length) {
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
                    cardEl.remove();
                    cardOpen = false;
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

            if (videoInput && previewContainer && errorDiv) {
                videoInput.addEventListener('change', function(e) {
                    previewContainer.innerHTML = '';
                    errorDiv.textContent = '';
                    let hasError = false;
                    Array.from(this.files).forEach(file => {
                        if (!file.type.startsWith('video/')) {
                            errorDiv.textContent = 'Only video files are allowed.';
                            hasError = true;
                            return;
                        }
                        if (file.size > 500 * 1024 * 1024) {
                            errorDiv.textContent = 'Each video must be less than 500MB.';
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
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            const chunkSize = 2 * 1024 * 1024;
            const uploadUrl = "{{ route('admin.daily-video.chunkUpload') }}";


            function showToast(message, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `toast-message toast-${type}`;
                toast.style.position = 'fixed';
                toast.style.top = '30px';
                toast.style.right = '30px';
                toast.style.zIndex = '99999';
                toast.style.background = type === 'success' ? '#22c55e' : '#ef4444';
                toast.style.color = '#fff';
                toast.style.padding = '12px 24px';
                toast.style.borderRadius = '8px';
                toast.style.boxShadow = '0 2px 8px rgba(44,62,80,0.12)';
                toast.style.fontWeight = '600';
                toast.innerText = message;
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.remove();
                }, 3500);
            }

            $("#upload-btn").on("click", function() {
                const fileInput = document.getElementById("video");
                const file = fileInput.files[0];
                if (!file) {
                    showToast("Please select a video", 'error');
                    return;
                }

                const timestamp = Date.now();
                const random = Math.floor(Math.random() * 1000000000);
                const ext = file.name.split('.').pop();
                const fileName = `${timestamp}-${random}.${ext}`;

                const totalChunks = Math.ceil(file.size / chunkSize);
                let currentChunk = 0;

                $("#upload-progress").show();

                function uploadNextChunk() {
                    const start = currentChunk * chunkSize;
                    const end = Math.min(file.size, start + chunkSize);
                    const blob = file.slice(start, end);

                    const formData = new FormData();
                    formData.append("_token", "{{ csrf_token() }}");
                    formData.append("file", blob);
                    formData.append("fileName", fileName);
                    formData.append("chunkIndex", currentChunk);
                    formData.append("totalChunks", totalChunks);

                    const uploadDate = document.getElementById('upload-date') ? document.getElementById(
                        'upload-date').value : '';
                    formData.append('upload_date', uploadDate);

                    $.ajax({
                        url: uploadUrl,
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(resp) {
                            currentChunk++;
                            let progress = Math.floor((currentChunk / totalChunks) * 100);
                            $("#progress-bar").css("width", progress + "%");

                            if (currentChunk < totalChunks) {
                                uploadNextChunk();
                            } else {
                                showToast("Video uploaded successfully!", 'success');
                                setTimeout(function() {
                                    location.reload();
                                }, 1200);
                            }
                        },
                        error: function(err) {
                            console.error(err);
                            showToast("Upload failed!", 'error');
                        }
                    });
                }

                uploadNextChunk();
            });


            const style = document.createElement('style');
            style.innerHTML = `.toast-message { transition: opacity 0.3s; opacity: 0.95; font-size: 1rem; }`;
            document.head.appendChild(style);
        });
    </script>
@endpush
