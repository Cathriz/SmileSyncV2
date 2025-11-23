<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmileSync – Doctor Management</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    {{-- Chart.js is not needed for this page, but keeping the script link just in case --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* --- General Styles --- */
        body { background-color: #f0f2f5; font-family: 'Poppins', sans-serif; }
        
        /* --- Navbar --- */
        .navbar { 
            background: #004c9e; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        } 
        .navbar-brand { font-weight: 700; }
        /* Adjusted .user-avatar to be the same size as the dashboard for consistency */
        .user-avatar { border: 2px solid white; width: 40px; height: 40px; object-fit: cover;}

        /* --- Sidebar --- */
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

        /* --- Main Content Cards (Specific for Doctor Page) --- */
        .main-content-card { 
            border-radius: 12px; 
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08); 
            transition: 0.3s;
        }
        .main-content-card:hover { transform: translateY(-3px); }
        .table thead { background-color: #004c9e !important; color: white; }
    </style>
</head>

<body>

{{-- ======================== TOP NAV BAR (UPDATED) ======================== --}}
<nav class="navbar navbar-expand-lg px-4 sticky-top">
    <a class="navbar-brand fw-bold text-white"><i class="bi bi-person-fill-gear me-2"></i> SmileSync Management</a>

    {{-- START: UPDATED Dropdown Menu for User and Logout --}}
    <div class="ms-auto">
        <div class="dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center p-0" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                {{-- Name visible on desktop --}}
                <span class="text-white fw-semibold me-2 d-none d-md-inline">Welcome, Admin User</span>
                {{-- Avatar --}}
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
    {{-- END: UPDATED Dropdown Menu --}}
</nav>

<div class="container-fluid p-0">
    <div class="row g-0">
        
        {{-- ======================== SIDE BAR ======================== --}}
        <div class="col-md-2 sidebar">
            <h5 class="fw-bold mt-2 mb-4">Main Menu</h5>
            <ul class="nav flex-column">
                {{-- Dashboard Link (Placeholder) --}}
                <li class="nav-item"><a class="nav-link" href="/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                
                {{-- Doctors Link (Active on this page) --}}
                <li class="nav-item"><a class="nav-link active" href="{{ route('doctors.index') }}"><i class="bi bi-person-badge me-2"></i>Manage Doctors</a></li>
                
                {{-- Other Links (Placeholders) --}}
                <li class="nav-item"><a class="nav-link" href="{{ route('appointments.index') }}"><i class="bi bi-calendar-check me-2"></i>Appointments</a></li>
                <li class="nav-item"><a class="nav-link" href="/records"><i class="bi bi-people me-2"></i>Records</a></li>
                <li class="nav-item"><a class="nav-link" href="reports"><i class="bi bi-bar-chart-line me-2"></i>Reports</a></li>
                <li class="nav-item mb-2"><a class="nav-link" href="{{ route('notifications.index') }}"><i class="bi bi-bell me-2"></i> Notifications</a></li>
            </ul>
        </div>

        {{-- ======================== MAIN CONTENT AREA (Doctor Management) ======================== --}}
        <div class="col-md-10 p-5">
            <h2 class="fw-bold mb-4 text-dark"><i class="bi bi-person-plus-fill me-2 text-primary"></i> Doctor Management</h2>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show main-content-card" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show main-content-card" role="alert">
                    <strong>Validation Error!</strong> Please check the form fields below for errors.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <div class="card mb-5 main-content-card">
                <div class="card-header bg-white fw-bold h5 text-primary">Add New Doctor</div>
                <div class="card-body">
                    <form action="{{ route('doctors.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="specialization" class="form-label">Specialization</label>
                                <input type="text" class="form-control @error('specialization') is-invalid @enderror" id="specialization" name="specialization" value="{{ old('specialization') }}" required>
                                @error('specialization') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-person-plus me-2"></i>Add Doctor</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card main-content-card">
                <div class="card-header bg-white fw-bold h5 text-primary">All Registered Doctors ({{ $doctors->count() }})</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Specialization</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($doctors as $doctor)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-semibold">{{ $doctor->name }}</td>
                                        <td>{{ $doctor->specialization }}</td>
                                        <td>{{ $doctor->email }}</td>
                                        <td>{{ $doctor->phone ?? 'N/A' }}</td>
                                        <td>
                                            <form action="{{ route('doctors.destroy', $doctor->id) }}" method="POST" onsubmit="return confirm('WARNING: Are you sure you want to delete Dr. {{ $doctor->name }}? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete Doctor"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bi bi-info-circle me-1"></i> No doctors have been added yet. Use the form above to get started.
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>