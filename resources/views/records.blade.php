<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Records - SmileSync</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f8f9fc;
        }
        .sidebar {
            background: white;
            min-height: 100vh;
            padding: 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
        }
        .nav-link {
            color: #333;
            font-weight: 500;
        }
        .nav-link.active, .nav-link:hover {
            color: #0d6efd;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: #0069d9; color: #fff !important; font-weight: bold;
        }
    </style>
</head>

<body>

<!-- TOP NAVBAR -->
<nav class="navbar navbar-expand-lg px-4" style="background:#0069d9;">
  <a class="navbar-brand fw-bold text-white" href="#">🦷 SmileSync</a>
  <div class="ms-auto">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="btn btn-light btn-sm" type="submit">Logout</button>
    </form>
  </div>
</nav>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-2 sidebar">
            <h5 class="fw-bold text-primary">Navigation</h5>
            <ul class="nav flex-column mt-3">
                <li class="nav-item mb-2">
                    <a class="nav-link" href="/dashboard">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link" href="{{ route('appointments.index') }}">
                        <i class="bi bi-calendar-check me-2"></i>Appointments
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link active" href="#">
                        <i class="bi bi-people me-2"></i>Records
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link" href="reports">
                        <i class="bi bi-bar-chart-line me-2"></i>Reports
                    </a>
                </li>

                <li class="nav-item mb-2">
                    <a class="nav-link" href="#">
                        <i class="bi bi-bell me-2"></i>Notifications
                    </a>
                </li>
            </ul>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-md-10 p-4">

            <div class="d-flex justify-content-between mb-4">
                <h3 class="fw-bold text-primary">
                    <i class="bi bi-people me-2"></i>Your Medical Records
                </h3>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="bi bi-plus-circle"></i> Add Record
                </button>
            </div>

            <div class="card p-3 shadow-sm">
                <table class="table table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th>Document</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($records as $r)
                        <tr>
                            <td>{{ $r->patient }}</td>
                            <td>{{ $r->doctor }}</td>
                            <td>{{ ucfirst($r->type) }}</td>
                            <td>{{ $r->date }}</td>
                            <td>{{ $r->time }}</td>

                            <td>
                                <span class="badge
                                    @if($r->status == 'done') bg-success
                                    @elseif($r->status == 'canceled') bg-danger
                                    @else bg-warning text-dark @endif">
                                    {{ ucfirst($r->status) }}
                                </span>
                            </td>

                            <td>{{ $r->notes }}</td>

                            <td>
                                @if($r->document)
                                    <a href="{{ asset('storage/uploads/'.$r->document) }}" target="_blank"
                                        class="btn btn-sm btn-secondary">View</a>
                                @else
                                    <span class="text-muted">No File</span>
                                @endif
                            </td>

                            <td>
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                        data-bs-target="#edit{{ $r->id }}">
                                    Edit
                                </button>

                                    <form action="{{ route('records.delete', $r->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')">Delete</button>
    </form>
                                </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">No records found.</td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODALS BELOW (add + edit modals, unchanged)... -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
