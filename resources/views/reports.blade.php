<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>SmileSync – Reports</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* --- Styles for Dashboard Look & Feel --- */
    body { background-color: #f0f2f5; font-family: 'Poppins', sans-serif; }
    
    /* --- Navbar (Header) --- */
    .navbar { 
        background: #004c9e; /* Darker blue for contrast */
        box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
    } 
    .navbar-brand { font-weight: 700; }
    .user-avatar { border: 2px solid white; }

    /* --- Sidebar --- */
    .sidebar { 
        background: #ffffff; 
        min-height: calc(100vh - 60px); /* Adjusted height to account for the navbar */
        border-right: 1px solid #e0e4eb; 
        padding: 20px 0; 
        box-shadow: 2px 0 5px rgba(0,0,0,0.02); 
    }
    .sidebar h5 { color: #004c9e; padding: 0 20px; font-weight: 700; }
    .sidebar .nav-item { padding: 0 10px; }
    .sidebar .nav-link { 
        color: #333 !important; 
        padding: 12px 15px; 
        border-radius: 8px; 
        margin-bottom: 5px; 
        display: flex; 
        align-items: center; 
        transition: all .2s; 
    }
    .sidebar .nav-link i { font-size: 1.1rem; width: 25px; }
    .sidebar .nav-link:hover {
        background-color: #0069d9; 
        color: #fff !important; 
    }
    .sidebar .nav-link.active {
        background-color: #0069d9; 
        color: #fff !important; 
        font-weight: 600;
    }

    /* --- Card & KPI Styles --- */
    .card { 
        border-radius: 12px; 
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
        transition: 0.3s;
    }
    .kpi-card { 
        border-left: 5px solid; 
        transition: 0.3s; 
    }
    .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,0,0,0.1); }
    .chart-container { height: 350px; } 
</style>
</head>

<body>

<nav class="navbar navbar-expand-lg px-4 sticky-top">
    <a class="navbar-brand fw-bold text-white"><i class="bi bi-bar-chart-fill me-2"></i> SmileSync Reports</a>

    <div class="ms-auto d-flex align-items-center">
        <span class="text-white fw-semibold me-3 d-none d-md-inline">Welcome, Admin</span> 
        <img src="https://i.pravatar.cc/40?img=6" class="rounded-circle user-avatar">
    </div>
</nav>

<div class="container-fluid p-0">
    <div class="row g-0">
        
        <div class="col-md-2 sidebar">
            
            <h5 class="fw-bold mt-2 mb-4">Main Menu</h5>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('appointments.index') }}"><i class="bi bi-calendar-check me-2"></i>Appointments</a></li>
                <li class="nav-item"><a class="nav-link" href="/records"><i class="bi bi-people me-2"></i>Records</a></li>
                <li class="nav-item"><a class="nav-link active" href="reports"><i class="bi bi-bar-chart-line me-2"></i>Reports</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-bell me-2"></i>Notifications</a></li>
            </ul>
            <hr class="my-4 mx-3">

            <h5 class="mt-4">Report Views</h5>
            <ul class="nav flex-column mt-3">
                <li class="nav-item">
                    <a class="nav-link {{ $reportType === 'patient_visits' ? 'active' : '' }}" href="{{ route('reports.index', ['report' => 'patient_visits']) }}">
                        <i class="bi bi-person-check me-2"></i> Patient Visits
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $reportType === 'doctor_overview' ? 'active' : '' }}" href="{{ route('reports.index', ['report' => 'doctor_overview']) }}">
                        <i class="bi bi-heart-pulse me-2"></i> Doctor Overview
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $reportType === 'financial' ? 'active' : '' }}" href="{{ route('reports.index', ['report' => 'financial']) }}">
                        <i class="bi bi-currency-dollar me-2"></i> Financial (Mock)
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="col-md-10 p-5">
            @php
                $reportTitle = [
                    'patient_visits' => 'Patient Visit Summary',
                    'doctor_overview' => 'Doctor Appointments Overview',
                    'financial' => 'Financial Overview'
                ][$reportType] ?? 'Reports Overview';
            @endphp
            <h2 class="fw-bold mb-4 text-dark"><i class="bi bi-bar-chart-line text-warning me-2"></i> {{ $reportTitle }}</h2>

            <div class="row g-4 mb-5">
                <div class="col-lg-4 col-md-6">
                    <div class="card kpi-card border-start border-primary border-4 h-100 p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <i class="bi bi-calendar-event text-primary display-6"></i>
                            <div class="text-end">
                                <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Visits (Q1)</div>
                                <div class="h3 mb-0 fw-bold text-gray-800">{{ $totalVisits }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card kpi-card border-start border-success border-4 h-100 p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <i class="bi bi-check-circle text-success display-6"></i>
                            <div class="text-end">
                                <div class="text-xs fw-bold text-success text-uppercase mb-1">Total Appointments</div>
                                <div class="h3 mb-0 fw-bold text-gray-800">{{ $totalAppointments }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card kpi-card border-start border-warning border-4 h-100 p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <i class="bi bi-person-lines-fill text-warning display-6"></i>
                            <div class="text-end">
                                <div class="text-xs fw-bold text-warning text-uppercase mb-1">Active Doctors</div>
                                <div class="h3 mb-0 fw-bold text-gray-800">{{ $activeDoctors }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($reportType === 'patient_visits')
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="card h-100 p-4">
                            <h5 class="fw-bold card-title mb-3"><i class="bi bi-graph-up me-2 text-primary"></i> Monthly Patient Visits</h5>
                            <div class="card-body chart-container">
                                <canvas id="monthlyVisitsChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card h-100 p-4">
                            <h5 class="fw-bold card-title mb-3"><i class="bi bi-pie-chart me-2 text-info"></i> Visit Type Distribution</h5>
                            <div class="card-body chart-container d-flex justify-content-center align-items-center">
                                <canvas id="treatmentTypeChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($reportType === 'doctor_overview')
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card h-100 p-4">
                            <h5 class="fw-bold card-title mb-3"><i class="bi bi-person-bounding-box me-2 text-danger"></i> Appointments by Doctor & Month</h5>
                            <div class="card-body chart-container">
                                <canvas id="doctorAppointmentsChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card h-100 p-4">
                            <h5 class="fw-bold card-title mb-3"><i class="bi bi-list-ol me-2 text-secondary"></i> Doctor Ranking (Q1)</h5>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    @php $rank = 1; @endphp
                                    @foreach ($doctorCharts['appointmentCounts'] as $doctorName => $count)
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <strong>{{ $rank++ }}. {{ $doctorName }}</strong>
                                            <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card mt-4 p-5 text-center">
                    <h4 class="fw-bold text-primary mb-3"><i class="bi bi-info-circle display-5 me-2"></i> Select a Report View</h4>
                    <p class="mb-0 fs-5 text-muted">Use the **Report Views** menu on the left to select a specific report type to see detailed charts and data.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Utility for generating random colors (for pie/doughnut charts)
    function generateColors(count) {
        // Updated colors for a professional look
        const baseColors = ['#007bff', '#198754', '#ffc107', '#dc3545', '#17a2b8', '#6c757d'];
        return Array.from({length: count}, (_, i) => baseColors[i % baseColors.length]);
    }

    // --- Report: Patient Visit Summary ---
    @if ($reportType === 'patient_visits')
        // Monthly Visits Chart (Bar)
        const monthlyCtx = document.getElementById('monthlyVisitsChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: @json($patientCharts['months']),
                datasets: [{
                    label: 'Total Visits',
                    data: @json($patientCharts['visitCounts']),
                    backgroundColor: '#004c9e', /* Matched new primary color */
                    borderColor: '#004c9e',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Number of Visits' } }
                }
            }
        });

        // Visit Type Distribution Chart (Doughnut)
        const typeCtx = document.getElementById('treatmentTypeChart').getContext('2d');
        const treatmentLabels = @json($patientCharts['typeLabels']);
        new Chart(typeCtx, {
            type: 'doughnut', 
            data: {
                labels: treatmentLabels,
                datasets: [{
                    data: @json($patientCharts['typeCounts']),
                    backgroundColor: generateColors(treatmentLabels.length),
                    hoverOffset: 8
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
        });
    @endif

    // --- Report: Doctor Appointments Overview ---
    @if ($reportType === 'doctor_overview')
        const doctorCtx = document.getElementById('doctorAppointmentsChart').getContext('2d');
        const allMonths = @json($doctorCharts['months']);
        const allDoctors = @json($doctorCharts['doctors']);
        const allAppointments = @json($doctorCharts['appointments']);
        
        const colors = generateColors(allDoctors.length);

        const doctorDatasets = allDoctors.map((doc, i)=>{
            const data = allMonths.map(m=>{
                const match = allAppointments.find(v=>v.month===m && v.doctor===doc);
                return match ? match.count : 0;
            });
            return {
                label: doc, 
                data, 
                backgroundColor: colors[i],
                borderColor: colors[i],
                borderWidth: 1
            };
        });

        new Chart(doctorCtx, {
            type: 'bar',
            data: { labels: allMonths, datasets: doctorDatasets },
            options: { 
                responsive: true,
                maintainAspectRatio: false,
                scales: { 
                    x: { stacked: true },
                    y: { 
                        stacked: true, 
                        beginAtZero: true,
                        title: { display: true, text: 'Number of Appointments' }
                    } 
                } 
            }
        });
    @endif
</script>
</body>
</html>