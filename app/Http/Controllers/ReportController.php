<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Get the requested report type from the query string
        $reportType = $request->query('report', 'patient_visits');

        // 2. Sample Data (Unchanged)
        $patientVisits = [
            ['month' => 'Jan', 'type' => 'Checkup', 'count' => 10],
            ['month' => 'Jan', 'type' => 'Dental', 'count' => 5],
            ['month' => 'Feb', 'type' => 'Checkup', 'count' => 12],
            ['month' => 'Feb', 'type' => 'Dental', 'count' => 7],
            ['month' => 'Mar', 'type' => 'Checkup', 'count' => 8],
            ['month' => 'Mar', 'type' => 'Dental', 'count' => 6],
        ];

        $doctorAppointments = [
            ['month' => 'Jan', 'doctor' => 'Dr. Smith', 'count' => 15],
            ['month' => 'Jan', 'doctor' => 'Dr. Lee', 'count' => 10],
            ['month' => 'Feb', 'doctor' => 'Dr. Smith', 'count' => 18],
            ['month' => 'Feb', 'doctor' => 'Dr. Lee', 'count' => 12],
            ['month' => 'Mar', 'doctor' => 'Dr. Smith', 'count' => 14],
            ['month' => 'Mar', 'doctor' => 'Dr. Lee', 'count' => 16],
        ];

        // 3. Prepare Patient Data (Unchanged)
        $months = [];
        $visitCounts = [];
        $typeCounts = [];
        $totalVisits = 0;

        foreach ($patientVisits as $visit) {
            if (!in_array($visit['month'], $months)) $months[] = $visit['month'];
            $visitCounts[$visit['month']] = ($visitCounts[$visit['month']] ?? 0) + $visit['count'];
            $typeCounts[$visit['type']] = ($typeCounts[$visit['type']] ?? 0) + $visit['count'];
            $totalVisits += $visit['count'];
        }

        // 4. Prepare Doctor Data (Unchanged)
        $doctors = [];
        $totalAppointments = 0;
        foreach ($doctorAppointments as $appointment) {
            if (!in_array($appointment['doctor'], $doctors)) $doctors[] = $appointment['doctor'];
            $totalAppointments += $appointment['count'];
        }

        $doctorAppointmentCount = collect($doctorAppointments)->groupBy('doctor')->map(function ($items) {
            return $items->sum('count');
        })->sortDesc()->all();
        
        $reportData = [
            'reportType' => $reportType,
            'totalVisits' => $totalVisits,
            'totalAppointments' => $totalAppointments,
            'activeDoctors' => count($doctors),
            'patientCharts' => [
                'months' => $months,
                'visitCounts' => array_values($visitCounts),
                'typeLabels' => array_keys($typeCounts),
                'typeCounts' => array_values($typeCounts),
            ],
            'doctorCharts' => [
                'appointments' => $doctorAppointments,
                'months' => collect($doctorAppointments)->pluck('month')->unique()->values()->all(),
                'doctors' => $doctors,
                'appointmentCounts' => $doctorAppointmentCount,
            ]
        ];

        // *** CHANGED: view('reports.reports') instead of view('reports.index') ***
        return view('reports', $reportData);
    }
}