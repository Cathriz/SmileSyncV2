<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Records - SmileSync</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* --- GENERAL & SIDEBAR STYLES (FROM ORIGINAL RECORDS PAGE) --- */
        html, body, .container-fluid, .row { height: 100%; }
        body { background: #f0f2f5; font-family: 'Poppins', sans-serif; } /* Updated body background to match dashboard */

        .sidebar {
            background: white;
            min-height: calc(100vh - 60px); /* Adjusted height to account for the sticky navbar */
            border-right: 1px solid #e0e4eb;
            padding: 20px 0; /* Updated padding to match dashboard sidebar */
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
        /* Style for the clickable patient link */
        .patient-link:hover {
            text-decoration: underline !important;
        }

        /* --- NAVBAR STYLES (FROM DASHBOARD CODE) --- */
        .navbar { 
            background: #004c9e; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        } 
        .navbar-brand { font-weight: 700; }
        .user-avatar { border: 2px solid white; width: 40px; height: 40px; object-fit: cover; } 
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg px-4 sticky-top">
    <a class="navbar-brand fw-bold text-white" href="/dashboard"><i class="bi bi-people-fill me-2"></i> SmileSync Records</a>

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
        
        <div class="col-auto col-md-2 sidebar">
            <h5 class="fw-bold mt-2 mb-4">Navigation</h5>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('doctors.index') }}"><i class="bi bi-person-badge me-2"></i>Manage Doctors</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('appointments.index') }}"><i class="bi bi-calendar-check me-2"></i>Appointments</a></li>
                <li class="nav-item"><a class="nav-link active" href="#"><i class="bi bi-people me-2"></i>Records</a></li>
                <li class="nav-item"><a class="nav-link" href="reports"><i class="bi bi-bar-chart-line me-2"></i>Reports</a></li>
                <li class="nav-item mb-2"><a class="nav-link" href="{{ route('notifications.index') }}"><i class="bi bi-bell me-2"></i> Notifications</a></li>
            </ul>
        </div>

        <div class="col-md-10 p-4">
            
            <h2 class="fw-bold mb-4 text-dark"><i class="bi bi-folder-plus me-2 text-primary"></i>Medical Records</h2>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-primary">Manage Records</h3>
            </div>

            <form method="GET" action="{{ route('records.index') }}" class="mb-3">
                <div class="input-group" style="max-width: 350px;">
                    <input type="text" class="form-control" name="search" placeholder="Search records..."
                    value="{{ request('search') }}">
                    <button class="btn btn-primary"><i class="bi bi-search"></i></button>
                </div>
            </form>

            <div class="card p-3 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Type</th>
                                <th>Date/Time</th>
                                <th>Notes</th>
                                <th>Document</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($records as $r)
                            <tr>
                                <td>{{ $r->id }}</td>
                                
                                {{-- Patient Name clickable to view profile --}}
                                <td>
                                    <a href="{{ route('records.show_patient', ['patient_name' => $r->patient]) }}" class="text-primary fw-bold text-decoration-none patient-link">
                                        {{ $r->patient }}
                                    </a>
                                </td>
                                
                                <td>{{ $r->doctor }}</td>
                                <td>{{ ucfirst($r->type) }}</td>
                                <td>{{ $r->date }} at {{ $r->time }}</td>
                                <td>{{ Str::limit($r->notes, 30) }}</td>
                                <td>
                                    @if($r->document)
                                        <a href="{{ asset('storage/uploads/'.$r->document) }}" target="_blank" class="btn btn-sm btn-secondary">View</a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- NEW: Changed Edit button to View button --}}
                                    <button class="btn btn-sm btn-info text-white me-1" data-bs-toggle="modal" data-bs-target="#viewRecord{{ $r->id }}">
                                        <i class="bi bi-eye"></i> View Record
                                    </button>
                                    
                                    {{-- ADDED: Delete button for convenience, kept original Delete route --}}
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteRecord{{ $r->id }}">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


            {{-- 2. VIEW RECORD MODALS (READ-ONLY) --}}
            @foreach($records as $r)
            <div class="modal fade" id="viewRecord{{ $r->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    {{-- REMOVED FORM TAGS: This is a view-only modal --}}
                    <div class="modal-content">

                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title"><i class="bi bi-card-text me-2"></i>Viewing Record #{{ $r->id }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="fw-semibold">Patient Name</label>
                                    {{-- ADDED READONLY ATTRIBUTE --}}
                                    <input type="text" class="form-control" value="{{ $r->patient }}" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="fw-semibold">Doctor Name</label>
                                    <input type="text" class="form-control" value="{{ $r->doctor }}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-semibold">Type</label>
                                    <input type="text" class="form-control" value="{{ $r->type }}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-semibold">Date</label>
                                    <input type="date" class="form-control" value="{{ $r->date }}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="fw-semibold">Time</label>
                                    <input type="time" class="form-control" value="{{ $r->time }}" readonly>
                                </div>
                                <div class="col-md-12">
                                    <label class="fw-semibold">Notes</label>
                                    <textarea class="form-control" rows="4" readonly>{{ $r->notes }}</textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="fw-semibold">Document</label>
                                    @if($r->document)
                                        <p class="mb-0">
                                            <a href="{{ asset('storage/uploads/'.$r->document) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-file-earmark-text"></i> View Attached Document ({{ $r->document }})
                                            </a>
                                        </p>
                                    @else
                                        <p class="text-muted">No document uploaded.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            
            {{-- 3. DELETE CONFIRMATION MODAL --}}
            @foreach($records as $r)
            <div class="modal fade" id="deleteRecord{{ $r->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form method="POST" action="/records/delete/{{ $r->id }}" class="modal-content">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">Confirm Deletion</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="fw-bold">Are you sure you want to delete the record for {{ $r->patient }} (Type: {{ $r->type }})?</p>
                            <p class="text-muted">This action cannot be undone.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Yes, Delete Record</button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
            
            {{-- 4. ADD RECORD MODAL (MODIFIED) --}}
            <div class="modal fade" id="addModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <form method="POST" action="{{ route('records.add') }}" enctype="multipart/form-data" class="modal-content">
                        @csrf

                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Add New Record</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="fw-semibold" for="patient_select">Patient Name</label>
                                    {{-- UPDATED: Replaced text input with dynamic Select dropdown for Patient --}}
                                    <select name="patient_name" id="patient_select" class="form-select" required>
                                        <option value="">Select Patient</option>
                                        {{-- You MUST pass a $patients variable (collection of Patient objects) from your Controller --}}
                                        @isset($patients)
                                            @foreach($patients as $patient)
                                                <option value="{{ $patient->name }}">{{ $patient->name }} (ID: {{ $patient->id ?? 'N/A' }})</option>
                                            @endforeach
                                        @else
                                            <option value="Jane Doe">Jane Doe (Example)</option>
                                            <option value="John Smith">John Smith (Example)</option>
                                        @endisset
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="fw-semibold" for="doctor_select">Doctor Name</label>
                                    {{-- UPDATED: Replaced text input with dynamic Select dropdown for Doctor --}}
                                    <select name="doctor_name" id="doctor_select" class="form-select" required>
                                        <option value="">Select Doctor</option>
                                        {{-- You MUST pass a $doctors variable (collection of Doctor objects) from your Controller --}}
                                        @isset($doctors)
                                            @foreach($doctors as $doctor)
                                                <option value="{{ $doctor->name }}">{{ $doctor->name }}</option>
                                            @endforeach
                                        @else
                                            <option value="Dr. Smith">Dr. Smith (Example)</option>
                                        @endisset
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="fw-semibold">Type</label>
                                    <input type="text" name="type" class="form-control" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="fw-semibold">Date</label>
                                    <input type="date" name="date" class="form-control" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="fw-semibold">Time</label>
                                    <input type="time" name="time" class="form-control" required>
                                </div>

                                <div class="col-md-12">
                                    <label class="fw-semibold">Notes</label>
                                    <textarea name="notes" class="form-control"></textarea>
                                </div>

                                <div class="col-md-12">
                                    <label class="fw-semibold">Upload Document</label>
                                    <input type="file" name="document" class="form-control">
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button class="btn btn-primary" type="submit">Add Record</button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>