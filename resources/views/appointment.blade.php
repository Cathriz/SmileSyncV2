<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>SmileSync – Appointments</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

    <style>
        :root{
            --primary:#004c9e;
            --muted:#f0f2f5;
        }
        body { background: var(--muted); font-family: 'Poppins', sans-serif; }
        .navbar{ background:var(--primary); box-shadow:0 2px 6px rgba(0,0,0,0.08); }
        .navbar-brand{ font-weight:700; color:#fff; }
        .user-avatar{ width:40px; height:40px; object-fit:cover; border-radius:50%; border:2px solid #fff;}
        .sidebar{ background:#fff; min-height:calc(100vh - 60px); border-right:1px solid #e9eef6; padding:20px 0; }
        .sidebar .nav-link{ color:#333; padding:10px 14px; border-radius:8px; margin-bottom:6px; }
        .sidebar .nav-link.active, .sidebar .nav-link:hover{ background: #0069d9; color:#fff !important; font-weight:600; }

        .card-main{ border-radius:12px; box-shadow:0 6px 20px rgba(14,30,60,0.06); }
        .btn-dental{ background:var(--primary); color:#fff; border:none; }
        .btn-dental:hover{ background:#0069d9; color:#fff; }
        .fc .fc-daygrid-event .fc-event-title { white-space:normal; }
        #appointmentCalendar{ max-width:100%; }

        /* Small screens: stack calendar + list */
        @media (max-width: 991px) {
            .two-column { flex-direction: column; gap: 1rem; }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top px-4 py-2">
    <a class="navbar-brand d-flex align-items-center" href="/dashboard">
        <i class="bi bi-person-fill-gear me-2"></i> SmileSync
    </a>

    <div class="ms-auto d-flex align-items-center">
        <span class="text-white me-3 d-none d-md-inline">Welcome, {{ Auth::user()->name ?? 'User' }}</span>
        <img src="https://i.pravatar.cc/40?u={{ Auth::id() }}" alt="avatar" class="user-avatar me-3">
        <div class="dropdown">
            <a class="text-white dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false"></a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li class="dropdown-header fw-bold">{{ Auth::user()->name ?? 'User' }}</li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right me-2 text-danger"></i>Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid p-0">
    <div class="row g-0">
        {{-- ======================== SIDE BAR (Unified) ======================== --}} <div class="col-md-2 sidebar"> <h5 class="fw-bold mt-2 mb-4">Main Menu</h5> <ul class="nav flex-column"> {{-- Dashboard Link (Placeholder) --}} <li class="nav-item"><a class="nav-link" href="/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li> {{-- Doctors Link --}} <li class="nav-item"><a class="nav-link" href="{{ route('doctors.index') }}"><i class="bi bi-person-badge me-2"></i>Manage Doctors</a></li> {{-- Appointments Link (Active) --}} <li class="nav-item"><a class="nav-link active" href="{{ route('appointments.index') }}"><i class="bi bi-calendar-check me-2"></i>Appointments</a></li> {{-- Other Links (Placeholders) --}} <li class="nav-item"><a class="nav-link" href="/records"><i class="bi bi-people me-2"></i>Records</a></li> <li class="nav-item"><a class="nav-link" href="reports"><i class="bi bi-bar-chart-line me-2"></i>Reports</a></li> <li class="nav-item mb-2"><a class="nav-link" href="{{ route('notifications.index') }}"><i class="bi bi-bell me-2"></i> Notifications</a></li> </ul> </div>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h3 class="mb-0">Appointment Schedule</h3>
                    <small class="text-muted">Manage your upcoming appointments</small>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <button class="btn btn-dental" id="openAddBtn" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-plus-lg me-1"></i> Add Schedule
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success card-main p-3 mb-3">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger card-main p-3 mb-3">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <div class="card card-main p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex gap-2 align-items-center">
                        <form method="GET" action="/appointments" class="d-flex">
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                            <input name="search" value="{{ request('search') }}" class="form-control form-control-sm me-2" placeholder="Search patient or doctor">
                            <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="bi bi-search"></i></button>
                        </form>
                    </div>

                    <div class="d-flex gap-2 align-items-center">
                        <label class="me-2 mb-0 small">Sort</label>
                        <select class="form-select form-select-sm" onchange="window.location.href=this.value">
                            @php $base = url('/appointments').'?search='.urlencode(request('search','')); $s=request('sort'); @endphp
                            <option value="{{$base}}&sort=date_asc" {{$s=='date_asc' ? 'selected':''}}>Date ↑</option>
                            <option value="{{$base}}&sort=date_desc" {{$s=='date_desc' ? 'selected':''}}>Date ↓</option>
                            <option value="{{$base}}&sort=patient_asc" {{$s=='patient_asc' ? 'selected':''}}>Patient A-Z</option>
                            <option value="{{$base}}&sort=status_asc" {{$s=='status_asc' ? 'selected':''}}>Status</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex two-column gap-3">
                    <!-- Calendar (left / top) -->
                    <div style="flex: 1 1 65%;">
                        <div id="appointmentCalendar"></div>
                    </div>

                    <!-- Upcoming list (right / bottom on small screens) -->
                    <div style="flex: 1 1 35%;">
                        <h6 class="mb-2">Upcoming</h6>
                        <div class="list-group">
                            @forelse($appointments->where('status','upcoming')->take(8) as $a)
                                <div class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-bold">{{ $a->patient ?? 'No Patient' }}</div>
                                        <small class="text-muted">{{ $a->doctor }} • {{ \Carbon\Carbon::parse($a->date)->format('M d, Y') }} {{ \Carbon\Carbon::parse($a->time)->format('H:i') }}</small>
                                        <div class="mt-1 small text-break">{{ Str::limit($a->notes, 90) }}</div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge rounded-pill 
                                            @if($a->status=='upcoming') bg-info
                                            @elseif($a->status=='complete') bg-success
                                            @else bg-danger
                                            @endif">
                                            {{ ucfirst($a->status) }}
                                        </span>
                                        <div class="mt-2 d-flex flex-column gap-1">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $a->id }}">Edit</button>
                                            <form action="/appointments/{{ $a->id }}" method="POST" onsubmit="return confirm('Delete this appointment?')" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger mt-1">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted p-3">No upcoming appointments.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            <!-- /card -->
        </div>
    </div>
</div>

{{-- EDIT MODALS --}}
@if(isset($appointments) && $appointments->count())
    @foreach($appointments as $a)
    <div class="modal fade" id="editModal{{ $a->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="/appointments/{{ $a->id }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Appointment</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" required>
                                <option value="dental" {{ $a->type=='dental' ? 'selected':'' }}>Dental</option>
                                <option value="checkup" {{ $a->type=='checkup' ? 'selected':'' }}>Checkup</option>
                                <option value="meeting" {{ $a->type=='meeting' ? 'selected':'' }}>Meeting</option>
                                <option value="personal" {{ $a->type=='personal' ? 'selected':'' }}>Personal</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Patient</label>
                            <input name="patient" value="{{ $a->patient }}" class="form-control" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Doctor</label>
                            <select name="doctor" class="form-select" required>
                                <option value="">Select Doctor</option>
                                @foreach($doctors as $doc)
                                    <option value="{{ $doc->name }}" {{ $a->doctor == $doc->name ? 'selected':'' }}>
                                        {{ $doc->name }} - {{ $doc->specialization }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-2">
                            <div class="col-6 mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" value="{{ $a->date }}" class="form-control" min="{{ now()->format('Y-m-d') }}" />
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Time</label>
                                <input type="time" name="time" value="{{ \Carbon\Carbon::parse($a->time)->format('H:i') }}" class="form-control" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="upcoming" {{ $a->status=='upcoming' ? 'selected':'' }}>Upcoming</option>
                                <option value="complete" {{ $a->status=='complete' ? 'selected':'' }}>Completed</option>
                                <option value="overdue" {{ $a->status=='overdue' ? 'selected':'' }}>Overdue</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ $a->notes }}</textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                        <button class="btn btn-primary" type="submit">Save Changes</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    @endforeach
@endif

{{-- ADD MODAL --}}
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="/appointments">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-lg me-2"></i>New Appointment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="dental">Dental</option>
                            <option value="checkup">Checkup</option>
                            <option value="meeting">Meeting</option>
                            <option value="personal">Personal</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Patient</label>
                        <input name="patient" class="form-control" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Doctor</label>
                        <select name="doctor" class="form-select" required>
                            <option value="">Select Doctor</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->name }}">{{ $doc->name }} - {{ $doc->specialization }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" id="addDate" name="date" class="form-control" min="{{ now()->format('Y-m-d') }}" required />
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Time</label>
                            <input type="time" name="time" class="form-control" required />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="upcoming">Upcoming</option>
                            <option value="complete">Completed</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-primary" type="submit">Save</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

@php
    $events = $appointments->map(function($a){
        $time = isset($a->time) ? substr($a->time,0,5) : '09:00';
        $color = '#0dcaf0';
        if(isset($a->status) && $a->status === 'complete') $color = '#198754';
        if(isset($a->status) && $a->status === 'overdue') $color = '#dc3545';
        return [
            'id' => $a->id,
            'title' => ($a->patient ? $a->patient : 'No Patient') . ' — ' . ($a->doctor ?? ''),
            'start' => ($a->date ? $a->date : date('Y-m-d')) . 'T' . $time,
            'color' => $color,
            'extendedProps' => [
                'status' => $a->status ?? '',
                'notes' => $a->notes ?? '',
                'type' => $a->type ?? ''
            ]
        ];
    });
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('appointmentCalendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        height: 'auto',
        selectable: true,
        navLinks: true,
        dateClick: function(info) {
            // prefill add modal date
            const addDateInput = document.getElementById('addDate');
            if(addDateInput) addDateInput.value = info.dateStr;
            // open add modal
            const addModal = new bootstrap.Modal(document.getElementById('addModal'));
            addModal.show();
        },
        eventClick: function(info) {
            // open edit modal by id if exists
            const id = info.event.id;
            const modalEl = document.getElementById('editModal' + id);
            if(modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            } else {
                alert(info.event.title);
            }
        },
        events: {!! $events->toJson(JSON_PRETTY_PRINT) !!}
    });
    calendar.render();
});
</script>
</body>
</html>
