<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Appointments - SmileSync</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fc;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .card {
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .btn-dental {
      background: #0069d9;
      color: #fff;
      border-radius: 10px;
      padding: 10px 20px;
    }
    .btn-dental:hover {
      background: #0056b3;
    }
    .btn-edit {
      background: #ffc107;
      color: white;
    }
    .btn-edit:hover {
      background: #e0a800;
    }
    .btn-cancel {
      background: #dc3545;
      color: white;
    }
    .btn-cancel:hover {
      background: #c82333;
    }

    /* Sidebar style */
    .sidebar {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      height: 100%;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: #0069d9; color: #fff !important; font-weight: bold;
        }
    .nav-link {
      color: #333;
      font-weight: 500;
    }
    .nav-link.active, .nav-link:hover {
      color: #0069d9;
    }

  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg px-4" style="background:#0069d9;">
  <a class="navbar-brand fw-bold text-white" href="#">🦷 SmileSync</a>
  <div class="ms-auto">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="btn btn-light btn-sm" type="submit">Logout</button>
    </form>
  </div>
</nav>

<!-- PAGE LAYOUT (Sidebar + Content) -->
<div class="container-fluid mt-4">
  <div class="row">

    <!-- ➤ INSERTED SIDEBAR -->
    <div class="col-md-2 sidebar">
      <h5 class="fw-bold text-primary">Navigation</h5>
      <ul class="nav flex-column mt-3">
        <li class="nav-item mb-2"><a class="nav-link" href="/dashboard"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
        <li class="nav-item mb-2"><a class="nav-link active" href="{{ route('appointments.index') }}"><i class="bi bi-calendar-check me-2"></i> Appointments</a></li>
        <li class="nav-item mb-2"><a class="nav-link" href="/records"><i class="bi bi-people me-2"></i> Records</a></li>
        <li class="nav-item mb-2"><a class="nav-link" href="reports"><i class="bi bi-bar-chart-line me-2"></i> Reports</a></li>
        <li class="nav-item mb-2"><a class="nav-link" href="#"><i class="bi bi-bell me-2"></i> Notifications</a></li>
      </ul>
    </div>

    <!-- MAIN CONTENT -->
    <div class="col-md-10">

      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary"><i class="bi bi-calendar-check me-2"></i>Your Appointments</h3>
        <button class="btn btn-dental" data-bs-toggle="modal" data-bs-target="#addModal">
          <i class="bi bi-plus-circle me-1"></i> Add Schedule
        </button>
      </div>

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <div class="card p-4">
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
                  <button class="btn btn-edit btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $a->id }}">
                    <i class="bi bi-pencil"></i>
                  </button>

                  <form method="POST" action="{{ route('appointments.destroy', $a->id) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-cancel btn-sm" onclick="return confirm('Delete this appointment?')">
                      <i class="bi bi-x-circle"></i>
                    </button>
                  </form>
                </td>
              </tr>

              <!-- Edit Modal -->
              <div class="modal fade" id="editModal{{ $a->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <form method="POST" action="{{ route('appointments.update', $a->id) }}">
                      @csrf
                      @method('PUT')

                      <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Appointment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <div class="mb-3">
                          <label class="form-label fw-semibold">Type</label>
                          <input type="text" name="type" class="form-control" value="{{ $a->type }}">
                        </div>
                        <div class="mb-3">
                          <label class="form-label fw-semibold">Doctor</label>
                          <input type="text" name="doctor" class="form-control" value="{{ $a->doctor }}">
                        </div>
                        <div class="mb-3">
                          <label class="form-label fw-semibold">Date</label>
                          <input type="date" name="date" class="form-control" value="{{ $a->date }}">
                        </div>
                        <div class="mb-3">
                          <label class="form-label fw-semibold">Time</label>
                          <input type="time" name="time" class="form-control" value="{{ $a->time }}">
                        </div>
                        <div class="mb-3">
                          <label class="form-label fw-semibold">Status</label>
                          <select name="status" class="form-select">
                            <option value="upcoming" {{ $a->status == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="complete" {{ $a->status == 'complete' ? 'selected' : '' }}>Completed</option>
                            <option value="overdue" {{ $a->status == 'overdue' ? 'selected' : '' }}>Overdue</option>
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

              @empty
              <tr>
                <td colspan="8" class="text-center text-muted">No appointments found.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div> <!-- end col-md-10 -->
  </div> <!-- end row -->
</div> <!-- end container-fluid -->

<!-- Add Schedule Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="{{ route('appointments.store') }}">
        @csrf
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add Schedule</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
            <input type="text" name="doctor" class="form-control" required>
          </div>

          <div class="row mb-3">
            <div class="col">
              <label class="form-label fw-semibold">Date</label>
              <input type="date" name="date" class="form-control" required>
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
