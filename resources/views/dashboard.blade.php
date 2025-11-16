<!DOCTYPE html>
<html lang="en">
<head>
    {{-- ... (head content remains the same) ... --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmileSync – Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* --- General Styles --- */
        body { background-color: #f0f2f5; font-family: 'Poppins', sans-serif; }
        
        /* --- Navbar --- */
        .navbar { 
            background: #004c9e; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Added subtle shadow */
        } 
        .navbar-brand { font-weight: 700; }
        .user-avatar { border: 2px solid white; }

        /* --- Sidebar --- */
        /* Changed min-height: 100vh to ensure it spans the screen height below the navbar */
        .sidebar { 
            background: #ffffff; 
            min-height: calc(100vh - 60px); /* Adjusted height to account for the navbar */
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

        /* --- Main Content Cards (rest of card styles remain the same) --- */
        .card { 
            border-radius: 12px; 
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08); 
            transition: 0.3s;
        }
        .card:hover { transform: translateY(-3px); }

        /* ... (rest of your styles) ... */
        .kpi-card { border-left: 5px solid; }
        .kpi-card h3 { font-size: 2.2rem; font-weight: 700; margin-top: 5px; }
        .kpi-card h6 { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; }
        .kpi-patient { border-color: #007bff; }
        .kpi-upcoming { border-color: #ffc107; }
        .kpi-complete { border-color: #198754 ; }
        .kpi-overdue { border-color: #dc3545; }
        .table thead th { background-color: #004c9e; color: #fff; border: none; font-weight: 600; }
        .table tbody tr:last-child td { border-bottom: none; }
        .table-responsive { border-radius: 10px; }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg px-4 sticky-top">
    <a class="navbar-brand fw-bold text-white"><i class="bi bi-person-fill-gear me-2"></i> SmileSync Dashboard</a>

    <div class="ms-auto d-flex align-items-center">
        <span class="text-white fw-semibold me-3 d-none d-md-inline">Welcome, {{ Auth::user()->name }}</span>
        <img src="https://i.pravatar.cc/40?img=6" class="rounded-circle user-avatar">
    </div>
</nav>

<div class="container-fluid p-0">
    <div class="row g-0">
        
        <div class="col-md-2 sidebar">
            <h5 class="fw-bold mt-2 mb-4">Main Menu</h5>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link active" href="/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('appointments.index') }}"><i class="bi bi-calendar-check me-2"></i>Appointments</a></li>
                <li class="nav-item"><a class="nav-link" href="/records"><i class="bi bi-people me-2"></i>Records</a></li>
                <li class="nav-item"><a class="nav-link" href="reports"><i class="bi bi-bar-chart-line me-2"></i>Reports</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-bell me-2"></i>Notifications</a></li>
            </ul>
        </div>

        <div class="col-md-10 p-5">
            <h2 class="fw-bold mb-4 text-dark"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Overview</h2>

            <div class="row g-4 mb-5">
                
                <div class="col-md-3">
                    <div class="card p-3 kpi-card kpi-patient shadow-sm">
                        <div class="d-flex align-items-center justify-content-between">
                            <i class="bi bi-people-fill text-primary display-6"></i>
                            <div class="text-end">
                                <h6>Total Patients</h6>
                                <h3>{{ $totalPatients }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 kpi-card kpi-upcoming shadow-sm">
                        <div class="d-flex align-items-center justify-content-between">
                            <i class="bi bi-calendar-event text-warning display-6"></i>
                            <div class="text-end">
                                <h6>Upcoming Appts</h6>
                                <h3>{{ $counts['upcoming'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 kpi-card kpi-complete shadow-sm">
                        <div class="d-flex align-items-center justify-content-between">
                            <i class="bi bi-check-circle text-success display-6"></i>
                            <div class="text-end">
                                <h6>Completed Appts</h6>
                                <h3>{{ $counts['complete'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 kpi-card kpi-overdue shadow-sm">
                        <div class="d-flex align-items-center justify-content-between">
                            <i class="bi bi-exclamation-triangle text-danger display-6"></i>
                            <div class="text-end">
                                <h6>Overdue Appts</h6>
                                <h3>{{ $counts['overdue'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="card p-4 h-100">
                        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-pie-chart me-2 text-info"></i>Appointment Status Distribution</h5>
                        <div style="height: 300px;"><canvas id="statusChart"></canvas></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card p-4 h-100">
                        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-bar-chart me-2 text-primary"></i>Monthly Appointments Trend</h5>
                        <div style="height: 300px;"><canvas id="monthChart"></canvas></div>
                    </div>
                </div>
            </div>

            <div class="card p-4 shadow-lg">
                <h4 class="fw-bold mb-4 text-dark"><i class="bi bi-calendar-week me-2 text-success"></i> Upcoming Appointments</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($appointments as $appt)
                                <tr>
                                    <td><i class="bi bi-person-circle me-1 text-muted"></i> {{ $appt->patient_name ?? '-' }}</td>
                                    <td>Dr. {{ $appt->doctor_name ?? '-' }}</td>
                                    <td>{{ $appt->date }}</td>
                                    <td>{{ $appt->time }}</td>
                                    <td>
                                        <span class="badge rounded-pill p-2 
                                            @if($appt->status=='upcoming') bg-warning text-dark 
                                            @elseif($appt->status=='complete') bg-success 
                                            @else bg-danger @endif">
                                            {{ ucfirst($appt->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">No recent appointments found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('appointments.index') }}" class="btn btn-outline-primary btn-sm mt-3 align-self-start">View All Appointments <i class="bi bi-arrow-right"></i></a>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Define the colors based on status for consistency
    const statusColors = ['#ffc107', '#198754', '#dc3545']; // Warning (Upcoming), Success (Complete), Danger (Overdue)
    const primaryColor = '#004c9e';

    // --- Appointment Status Chart (Pie) ---
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut', // Changed to doughnut for modern look
        data: {
            labels: ['Upcoming', 'Complete', 'Overdue'],
            datasets: [{ 
                data: [{{ $counts['upcoming'] }}, {{ $counts['complete'] }}, {{ $counts['overdue'] }}],
                backgroundColor: statusColors,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' }
            }
        }
    });

    // --- Monthly Appointments Chart (Bar) ---
    new Chart(document.getElementById('monthChart'), {
        type: 'bar',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun'],
            datasets: [{ 
                label: 'Total Appointments', 
                data:[10,20,15,25,30,20],
                backgroundColor: primaryColor,
                borderColor: primaryColor,
                borderWidth: 1,
                borderRadius: 4 // Slightly rounded bars
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
</body>
</html>