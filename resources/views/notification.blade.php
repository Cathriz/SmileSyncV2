<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications - SmileSync</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* (Keep your existing styles here for consistency) */
        body { background: #f8f9fc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .sidebar { background: #fff; border-radius: 10px; padding: 20px; height: 100%; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #0069d9; color: #fff !important; font-weight: bold; }
        .nav-link { color: #333; font-weight: 500; }
        .nav-link.active, .nav-link:hover { color: #0069d9; }
        .notification-item { border-left: 5px solid; margin-bottom: 10px; padding: 15px; border-radius: 8px; }
        .unread { border-left-color: #007bff; background-color: #eaf3ff; } /* Blue for unread */
        .read { border-left-color: #6c757d; background-color: #f8f9fa; } /* Gray for read */
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg px-4" style="background:#0069d9;">
    <a class="navbar-brand fw-bold text-white" href="#">🦷 SmileSync</a>
    <div class="ms-auto">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-light btn-sm" type="submit">Logout</button>
        </form>
    </div>
</nav>

<div class="container-fluid mt-4">
    <div class="row">

        <div class="col-md-2 sidebar">
            <h5 class="fw-bold text-primary">Navigation</h5>
            <ul class="nav flex-column mt-3">
                <li class="nav-item mb-2"><a class="nav-link" href="/dashboard"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('doctors.index') }}"><i class="bi bi-person-badge me-2"></i>Manage Doctors</a></li>
                <li class="nav-item mb-2"><a class="nav-link" href="{{ route('appointments.index') }}"><i class="bi bi-calendar-check me-2"></i> Appointments</a></li>
                <li class="nav-item mb-2"><a class="nav-link" href="/records"><i class="bi bi-people me-2"></i> Records</a></li>
                <li class="nav-item mb-2"><a class="nav-link" href="reports"><i class="bi bi-bar-chart-line me-2"></i> Reports</a></li>
                <li class="nav-item mb-2"><a class="nav-link active" href="{{ route('notifications.index') }}"><i class="bi bi-bell me-2"></i> Notifications</a></li>
            </ul>
        </div>

        <div class="col-md-10">
            <h3 class="fw-bold text-primary mb-4"><i class="bi bi-bell me-2"></i>Notifications</h3>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card p-4">
                <ul class="nav nav-tabs mb-4" id="notificationTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="unread-tab" data-bs-toggle="tab" data-bs-target="#unread-tab-pane" type="button" role="tab">
                            Unread ({{ $unreadNotifications->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="read-tab" data-bs-toggle="tab" data-bs-target="#read-tab-pane" type="button" role="tab">
                            Read ({{ $readNotifications->count() }})
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="notificationTabsContent">
                    
                    {{-- UNREAD NOTIFICATIONS --}}
                    <div class="tab-pane fade show active" id="unread-tab-pane" role="tabpanel" tabindex="0">
                        @forelse ($unreadNotifications as $notification)
                            <div class="d-flex justify-content-between align-items-center notification-item unread">
                                <div>
                                    <p class="mb-1 fw-bold text-primary">{{ $notification->data['title'] ?? 'New Appointment Scheduled' }}</p>
                                    <p class="mb-0 text-dark">{{ $notification->data['message'] ?? 'Check your appointments for details.' }}</p>
                                    <small class="text-muted"><i class="bi bi-clock me-1"></i> Received {{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="alert alert-info text-center">You have no new notifications.</div>
                        @endforelse
                    </div>

                    {{-- READ NOTIFICATIONS --}}
                    <div class="tab-pane fade" id="read-tab-pane" role="tabpanel" tabindex="0">
                        @forelse ($readNotifications as $notification)
                            <div class="d-flex justify-content-between align-items-center notification-item read">
                                <div>
                                    <p class="mb-1 text-secondary fw-semibold">{{ $notification->data['title'] ?? 'New Appointment Scheduled' }}</p>
                                    <p class="mb-0 text-muted">{{ $notification->data['message'] ?? 'Check your appointments for details.' }}</p>
                                    <small class="text-muted"><i class="bi bi-check-circle me-1"></i> Read {{ $notification->read_at->diffForHumans() }}</small>
                                </div>
                                <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @empty
                            <div class="alert alert-info text-center">You have no read notifications.</div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>