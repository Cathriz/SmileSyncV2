<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmileSync – Appointments</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* --- General Styles (Unified) --- */
        body { background-color: #f0f2f5; font-family: 'Poppins', sans-serif; }
        
        /* --- Navbar (Unified) --- */
        .navbar { 
            background: #004c9e; /* Consistent blue */
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        } 
        .navbar-brand { font-weight: 700; }
        .user-avatar { border: 2px solid white; width: 40px; height: 40px; object-fit: cover; }

        /* --- Sidebar (Unified) --- */
        .sidebar { 
            background: #ffffff; 
            min-height: calc(100vh - 60px); 
            border-right: 1px solid #e0e4eb; 
            padding: 20px 0; 
            box-shadow: 2px 0 5px rgba(0,0,0,0.02); 
        }
        .sidebar h5 { color: #004c9e; padding: 0 20px; }
        .sidebar .nav-item { padding: 0 10px; }
        .sidebar .nav-link { color: #333; padding: 12px 15px; border-radius: 8px; margin-bottom: 5px; display: flex; align-items: center; transition: all .2s; }
        .sidebar .nav-link i { font-size: 1.1rem; width: 25px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: #0069d9; 
            color: #fff !important; 
            font-weight: 600;
        }

        /* --- Main Content Cards (Unified) --- */
        .main-content-card { 
            border-radius: 12px; 
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08); 
            transition: 0.3s;
        }
        .main-content-card:hover { transform: translateY(-3px); }
        /* Apply consistent table styling */
        .table thead { background-color: #004c9e !important; color: white; }

        /* Re-mapping custom buttons to primary/danger for Bootstrap consistency */
        .btn-dental, .bg-primary { background: #004c9e !important; }
        .btn-dental:hover { background: #0069d9 !important; }

        .btn-edit { color: white !important; }
        .btn-cancel { color: white !important; }

    </style>
</head>

<body>

{{-- ======================== TOP NAV BAR (Unified) ======================== --}}
<nav class="navbar navbar-expand-lg px-4 sticky-top">
    <a class="navbar-brand fw-bold text-white"><i class="bi bi-person-fill-gear me-2"></i> SmileSync Appointment</a>

    {{-- Dropdown Menu for User and Logout --}}
    <div class="ms-auto">
        <div class="dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center p-0" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                {{-- Placeholder text --}}
                <span class="text-white fw-semibold me-2 d-none d-md-inline">Welcome, Admin User</span>
                {{-- Placeholder image --}}
                <img src="https://i.pravatar.cc/40?img=6" class="rounded-circle user-avatar">
            </a>
            
            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="navbarDropdown">
                <li class="dropdown-header">Logged in as:</li>
                <li class="dropdown-header fw-bold text-primary">Admin User</li>
                <li><hr class="dropdown-divider"></li>
                
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item" type="submit">
                            <i class="bi bi-box-arrow-right me-2 text-danger"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid p-0">
    <div class="row g-0">

        {{-- ======================== SIDE BAR (Unified) ======================== --}}
        <div class="col-md-2 sidebar">
            <h5 class="fw-bold mt-2 mb-4">Main Menu</h5>
            <ul class="nav flex-column">
                {{-- Dashboard Link (Placeholder) --}}
                <li class="nav-item"><a class="nav-link" href="/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                
                {{-- Doctors Link --}}
                <li class="nav-item"><a class="nav-link" href="{{ route('doctors.index') }}"><i class="bi bi-person-badge me-2"></i>Manage Doctors</a></li>
                
                {{-- Appointments Link (Active) --}}
                <li class="nav-item"><a class="nav-link active" href="{{ route('appointments.index') }}"><i class="bi bi-calendar-check me-2"></i>Appointments</a></li>
                
                {{-- Other Links (Placeholders) --}}
                <li class="nav-item"><a class="nav-link" href="/records"><i class="bi bi-people me-2"></i>Records</a></li>
                <li class="nav-item"><a class="nav-link" href="reports"><i class="bi bi-bar-chart-line me-2"></i>Reports</a></li>
                <li class="nav-item mb-2"><a class="nav-link" href="{{ route('notifications.index') }}"><i class="bi bi-bell me-2"></i> Notifications</a></li>
            </ul>
        </div>

        {{-- ======================== MAIN CONTENT AREA (Appointment List) ======================== --}}
        <div class="col-md-10 p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark"><i class="bi bi-calendar-check me-2 text-primary"></i> Appointment Schedule</h2>
                <button class="btn btn-dental btn-lg" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="bi bi-plus-circle me-1"></i> Add Schedule
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success main-content-card">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show main-content-card" role="alert">
                    <h6 class="alert-heading fw-bold">Action Failed!</h6>
                    <p>The following errors prevented the schedule from being saved:</p>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card p-4 main-content-card">
                
                <div class="row mb-3 align-items-end">
                    
                    {{-- SEARCH FORM --}}
                    <div class="col-md-5">
                        <form method="GET" action="{{ route('appointments.index') }}" class="d-flex">
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                            <input type="text" name="search" class="form-control me-2" placeholder="Search by patient or doctor..." value="{{ request('search') }}">
                            <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
                            @if(request('search'))
                                <a href="{{ route('appointments.index', ['sort' => request('sort')]) }}" class="btn btn-outline-secondary ms-2"><i class="bi bi-x"></i></a>
                            @endif
                        </form>
                    </div>
                    {{-- END SEARCH FORM --}}

                    {{-- SORTING DROPDOWN --}}
                    <div class="col-md-3 ms-auto">
                        <label for="sortAppointments" class="form-label fw-bold">Sort By:</label>
                        <select class="form-select" id="sortAppointments" onchange="window.location.href = this.value;">
                            @php
                                $baseRoute = route('appointments.index', ['search' => request('search', '')]);
                                $sortParam = request('sort'); // Define $sortParam here for use in options
                            @endphp

                            <option value="{{ $baseRoute }}&sort=date_asc" 
                                {{ $sortParam == 'date_asc' ? 'selected' : '' }}>Date & Time (Ascending)</option>
                            
                            <option value="{{ $baseRoute }}&sort=date_desc" 
                                {{ $sortParam == 'date_desc' ? 'selected' : '' }}>Date & Time (Descending)</option>
                            
                            <option value="{{ $baseRoute }}&sort=patient_asc" 
                                {{ $sortParam == 'patient_asc' ? 'selected' : '' }}>Patient Name (A-Z)</option>

                            <option value="{{ $baseRoute }}&sort=status_asc" 
                                {{ $sortParam == 'status_asc' ? 'selected' : '' }}>Status (Upcoming First)</option>
                        </select>
                    </div>
                    {{-- END SORTING DROPDOWN --}}
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>Type</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            {{-- Start looping through appointments for the table rows --}}
                            @forelse($appointments as $a)
                            <tr>
                                <td>{{ ucfirst($a->type) }}</td>
                                <td>{{ $a->patient ?? '-' }}</td>
                                <td>{{ $a->doctor }}</td>
                                <td>{{ $a->date }}</td>
                                <td>{{ $a->time }}</td>
                                <td>
                                    <span class="badge 
                                        @if($a->status == 'upcoming') bg-info 
                                        @elseif($a->status == 'complete') bg-success 
                                        @else bg-danger @endif">
                                            {{ ucfirst($a->status) }}
                                    </span>
                                </td>
                                <td>{{ $a->notes ?? '-' }}</td>
                                <td>
                                    {{-- Edit Button: Target Modal ID based on appointment ID --}}
                                    <button class="btn btn-warning btn-sm btn-edit" data-bs-toggle="modal" data-bs-target="#editModal{{ $a->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <form method="POST" action="{{ route('appointments.destroy', $a->id) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm btn-cancel" onclick="return confirm('Delete this appointment?')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-info-circle me-1"></i> No appointments found. Click "Add Schedule" to create a new one.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div> 
    </div> 
</div> 

{{-- ========================================================================================= --}}
{{-- !! CRITICAL FIX: INDIVIDUAL EDIT MODALS MOVED OUTSIDE THE TABLE STRUCTURE !! --}}
{{-- Looping through appointments again to define modals, ensuring clean HTML structure --}}
{{-- ========================================================================================= --}}
@if(isset($appointments) && count($appointments) > 0)
    @foreach($appointments as $a)
    <div class="modal fade" id="editModal{{ $a->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('appointments.update', $a->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Appointment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Type</label>
                            <select name="type" class="form-select" required>
                                 <option value="dental" @if($a->type == 'dental') selected @endif>Dental</option>
                                 <option value="checkup" @if($a->type == 'checkup') selected @endif>Checkup</option>
                                 <option value="meeting" @if($a->type == 'meeting') selected @endif>Meeting</option>
                                 <option value="personal" @if($a->type == 'personal') selected @endif>Personal</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Patient</label>
                            <input type="text" name="patient" class="form-control" value="{{ $a->patient }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Doctor</label>
                            <select name="doctor" class="form-select" required>
                                <option value="">Select Doctor</option>
                                {{-- Assuming $doctors is available in the view --}}
                                @foreach($doctors as $doc)
                                    <option value="{{ $doc->name }}" @if($a->doctor == $doc->name) selected @endif>
                                         {{ $doc->name }} - {{ $doc->specialization }}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Date</label>
                            <input 
                                type="date" 
                                name="date" 
                                class="form-control" 
                                value="{{ $a->date }}"
                                min="{{ now()->format('Y-m-d') }}"
                            >
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Time</label>
                            <input type="time" name="time" class="form-control" value="{{ $a->time }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="upcoming" @if($a->status == 'upcoming') selected @endif>Upcoming</option>
                                <option value="complete" @if($a->status == 'complete') selected @endif>Completed</option>
                                <option value="overdue" @if($a->status == 'overdue') selected @endif>Overdue</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ $a->notes }}</textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    @endforeach
@endif
{{-- END OF INDIVIDUAL EDIT MODALS --}}

{{-- Add Schedule Modal (The modal for creating new appointments) --}}
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('appointments.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="dental">Dental</option>
                            <option value="checkup">Checkup</option>
                            <option value="meeting">Meeting</option>
                            <option value="personal">Personal</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Patient</label>
                        <input type="text" name="patient" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Doctor</label>
                        <select name="doctor" class="form-select" required>
                            <option value="">Select Doctor</option> 
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->name }}">
                                    {{ $doc->name }} - {{ $doc->specialization }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label fw-semibold">Date</label>
                            <input 
                                type="date" 
                                name="date" 
                                class="form-control" 
                                min="{{ now()->format('Y-m-d') }}" 
                                required
                            >
                        </div>
                        <div class="col">
                            <label class="form-label fw-semibold">Time</label>
                            <input type="time" name="time" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="upcoming">Upcoming</option>
                            <option value="complete">Completed</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>

            </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>