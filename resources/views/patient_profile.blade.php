<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Profile: {{ $patient_name }} - SmileSync</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* --- GENERAL & BASE STYLES --- */
        body { 
            background: #f0f2f5; 
            font-family: 'Poppins', sans-serif; /* Added font-family for consistency */
        }
        
        /* --- NAVBAR STYLES (FROM DASHBOARD CODE) --- */
        .navbar { 
            background: #004c9e; /* Dark Blue from Dashboard */
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        } 
        .navbar-brand { font-weight: 700; }
        .user-avatar { border: 2px solid white; width: 40px; height: 40px; object-fit: cover; } 

        /* --- SIDEBAR STYLES --- */
        .sidebar {
            background: white;
            /* Adjusted height to account for the sticky navbar (Approx 60px) */
            min-height: calc(100vh - 60px); 
            border-right: 1px solid #e0e4eb; /* Added border for look */
            padding: 20px 0; /* Adjusted padding to match dashboard sidebar */
            box-shadow: 2px 0 5px rgba(0,0,0,0.02);
            position: sticky;
            top: 60px; /* Offset by the navbar height */
        }
        .sidebar h5 { color: #004c9e; padding: 0 20px; }
        .sidebar .nav-item { padding: 0 10px; }
        .sidebar .nav-link { 
            color: #333; 
            padding: 12px 15px; /* Updated padding to match dashboard sidebar */
            border-radius: 8px; /* Updated border-radius to match dashboard sidebar */
            margin-bottom: 5px; 
            display: flex; 
            align-items: center; 
            transition: all .2s; 
        }
        .sidebar .nav-link i { font-size: 1.1rem; width: 25px; }
        .sidebar .nav-link.active,
        .sidebar .nav-link:hover { 
            background-color: #0069d9; 
            color: #fff !important; 
            font-weight: 600; /* Updated font-weight to match dashboard sidebar */
        }
        
        /* New Styles for Profile Card - Keeping existing */
        .profile-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .profile-header {
            background-color: #e6f7ff;
            padding: 25px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            text-align: center;
        }
        .profile-icon {
            font-size: 4rem;
            color: #0069d9;
            margin-bottom: 10px;
        }
        .record-entry {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .record-entry:hover {
            background-color: #e9f0ff;
            border-color: #0069d9;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg px-4 sticky-top">
    <a class="navbar-brand fw-bold text-white" href="/dashboard"><i class="bi bi-person-fill-gear me-2"></i> SmileSync Dashboard</a>

    {{-- UPDATED Dropdown Menu for User and Logout (Directly copied from dashboard code) --}}
    <div class="ms-auto">
        <div class="dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center p-0" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="text-white fw-semibold me-2 d-none d-md-inline">Welcome, {{ Auth::user()->name ?? 'Admin User' }}</span>
                <img src="https://i.pravatar.cc/40?img=6" class="rounded-circle user-avatar">
            </a>
            
            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="navbarDropdown">
                <li class="dropdown-header">Logged in as:</li>
                <li class="dropdown-header fw-bold text-primary">{{ Auth::user()->name ?? 'Admin User' }}</li>
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

        {{-- SIDEBAR --}}
        <div class="col-auto col-md-2 p-0 sidebar">
            <h5 class="fw-bold mt-2 mb-4">Navigation</h5>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('doctors.index') }}"><i class="bi bi-person-badge me-2"></i>Manage Doctors</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('appointments.index') }}"><i class="bi bi-calendar-check me-2"></i>Appointments</a></li>
                <li class="nav-item"><a class="nav-link active" href="{{ route('records.index') }}"><i class="bi bi-people me-2"></i>Records</a></li>
                <li class="nav-item"><a class="nav-link" href="reports"><i class="bi bi-bar-chart-line me-2"></i>Reports</a></li>
                <li class="nav-item mb-2"><a class="nav-link" href="{{ route('notifications.index') }}"><i class="bi bi-bell me-2"></i> Notifications</a></li>
            </ul>
        </div>

        {{-- MAIN CONTENT --}}
        <div class="col-md-10 p-4">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-dark"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Patient Profile: {{ $patient_name }}</h2>
                <a href="{{ route('records.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Records
                </a>
            </div>
            <hr>

            <div class="row">
                
                {{-- PATIENT DETAILS COLUMN (4 Columns) --}}
                <div class="col-lg-4">
                    <div class="profile-card shadow-lg">
                        <div class="profile-header">
                            <i class="bi bi-person-circle profile-icon"></i>
                            <h4 class="fw-bold text-dark">{{ $patient_name }}</h4>
                            <p class="text-muted mb-0">Record ID: {{ $profile->id ?? 'N/A' }}</p>
                        </div>
                        
                        <div class="p-4">
                            <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                                <i class="bi bi-info-circle me-1"></i> Demographic Information
                            </h6>
                            
                            {{-- Display Profile Data --}}
                            <p class="mb-2"><span class="fw-semibold">Phone:</span> {{ $profile->phone ?? 'N/A' }}</p>
                            <p class="mb-2"><span class="fw-semibold">Address:</span> {{ $profile->address ?? 'N/A' }}</p>
                            <p class="mb-2"><span class="fw-semibold">Records Count:</span> {{ $records->count() }}</p>
                            <p class="mb-2"><span class="fw-semibold">Last Visit:</span> {{ $records->first()->date ?? 'N/A' }}</p>

                            <h6 class="fw-bold text-primary border-bottom pb-2 mt-4 mb-3">
                                <i class="bi bi-file-earmark-medical me-1"></i> Permanent Documents
                            </h6>
                            
                            @if($profile && $profile->permanent_document)
                                <a href="{{ asset('storage/uploads/'.$profile->permanent_document) }}" target="_blank" class="btn btn-sm btn-outline-success w-100">
                                    <i class="bi bi-file-earmark-check"></i> View Permanent Document
                                </a>
                            @else
                                <p class="text-muted">No permanent documents uploaded.</p>
                            @endif
                            
                            <button class="btn btn-sm btn-info text-white w-100 mt-3" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                <i class="bi bi-pencil-square"></i> Edit Patient Details
                            </button>
                        </div>
                    </div>
                </div>

                {{-- RECORD HISTORY COLUMN (8 Columns) --}}
                <div class="col-lg-8">
                    <div class="card p-4 shadow-lg h-100">
                        <h4 class="fw-bold text-danger border-bottom pb-2 mb-4">
                            <i class="bi bi-clock-history me-2"></i>Treatment History ({{ $records->count() }} Entries)
                        </h4>

                        @forelse($records as $record)
                            <div class="mb-3 p-3 record-entry">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="fw-bold text-success mb-1">
                                            <i class="bi bi-file-earmark-check me-2"></i> {{ ucfirst($record->type) }}
                                        </h5>
                                        <p class="text-muted mb-1">
                                            <i class="bi bi-calendar me-1"></i> {{ $record->date }} at {{ $record->time }} | 
                                            <i class="bi bi-person-badge-fill me-1"></i> Dr. {{ $record->doctor }}
                                        </p>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">{{ \Carbon\Carbon::parse($record->created_at)->diffForHumans() }}</span>
                                </div>

                                <p class="mt-2 mb-2 border-start border-3 border-info ps-3 text-secondary">
                                    **Notes:** {{ $record->notes ?? 'No detailed notes provided.' }}
                                </p>

                                @if($record->document)
                                    <a href="{{ asset('storage/uploads/'.$record->document) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="bi bi-file-earmark-arrow-down"></i> View Attached Document
                                    </a>
                                @endif
                                
                            </div>
                        @empty
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i> No record history found for this patient.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- 2. MODALS (Kept original modals for functionality) --}}

<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('patients.update_profile') }}" enctype="multipart/form-data" class="modal-content">
            @csrf
            
            {{-- HIDDEN FIELD: PASS THE CURRENT PATIENT NAME --}}
            <input type="hidden" name="patient_name" value="{{ $patient_name }}">
            
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit {{ $patient_name }} Profile</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control" placeholder="(555) 123-4567" 
                            value="{{ $profile->phone ?? '' }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Address</label>
                    <textarea name="address" class="form-control" rows="2">{{ $profile->address ?? '' }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Upload Permanent Profile Document</label>
                    <input type="file" name="permanent_document" class="form-control">
                    @if($profile && $profile->permanent_document)
                        <small class="text-muted">Current document: {{ $profile->permanent_document }}</small>
                    @endif
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Profile</button>
            </div>
        </form>
    </div>
</div>

{{-- EDIT RECORD MODALS (Need to include full form logic if you want to edit here) --}}
@foreach($records as $r)
    <div class="modal fade" id="editRecordModal{{ $r->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            {{-- NOTE: You must place the full update form content here, similar to the one in your records.blade.php --}}
            <form method="POST" action="{{ route('records.update') }}" enctype="multipart/form-data" class="modal-content">
                @csrf
                @method('PUT')

                <input type="hidden" name="id" value="{{ $r->id }}">
                <input type="hidden" name="existing_document" value="{{ $r->document }}">

                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Edit Record #{{ $r->id }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fw-semibold">Patient Name</label>
                            <input type="text" name="patient_name" class="form-control" value="{{ $r->patient }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-semibold">Doctor Name</label>
                            <input type="text" name="doctor_name" class="form-control" value="{{ $r->doctor }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-semibold">Type</label>
                            <input type="text" name="type" class="form-control" value="{{ $r->type }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-semibold">Date</label>
                            <input type="date" name="date" class="form-control" value="{{ $r->date }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-semibold">Time</label>
                            <input type="time" name="time" class="form-control" value="{{ \Carbon\Carbon::parse($r->time)->format('H:i') }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control">{{ $r->notes }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="fw-semibold">Upload Document (Replaces current)</label>
                            <input type="file" name="document" class="form-control">
                            @if($r->document)
                                <small class="text-muted">Current file: {{ $r->document }}</small>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-info text-white" type="submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>