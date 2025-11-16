<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    // Show only appointments of the logged-in user
    public function index()
    {
        $appointments = Appointment::where('user_id', Auth::id())->get();
        return view('appointment', compact('appointments'));
    }


    // Store a new appointment
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'doctor' => 'required',
            'date' => 'required|date',
            'time' => 'required',
            'status' => 'required',
        ]);

        Appointment::create([
            'user_id' => Auth::id(),
            'patient' => $request->patient,
            'doctor' => $request->doctor,
            'date' => $request->date,
            'time' => $request->time,
            'status' => $request->status,
            'type' => $request->type,
            'notes' => $request->notes,
        ]);

        return redirect()->route('appointments.index')
                         ->with('success', 'Schedule added successfully!');
    }


    // 🔧 FIXED: Missing update() method (This caused your error!)
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        // Check if user is allowed to update
        if ($appointment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'type' => 'required',
            'doctor' => 'required',
            'date' => 'required|date',
            'time' => 'required',
            'status' => 'required',
        ]);

        $appointment->update([
            'type' => $request->type,
            'doctor' => $request->doctor,
            'date' => $request->date,
            'time' => $request->time,
            'status' => $request->status,
            'notes' => $request->notes,
            
        ]);

        return redirect()->route('appointments.index')
                         ->with('success', 'Appointment updated successfully!');
    }


    // Delete appointment
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $appointment->delete();

        return redirect()->route('appointments.index')
                         ->with('success', 'Appointment deleted successfully!');
    }
}
