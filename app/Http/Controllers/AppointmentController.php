<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\AppointmentDeleted; 
use App\Notifications\AppointmentEdited; 

class AppointmentController extends Controller
{
    // Show only appointments of the logged-in user, with sorting and searching
    public function index(Request $request)
    {
        // 1. Setup Sorting Parameters
        $sortColumn = 'date';
        $sortDirection = 'asc';

        $sortParam = $request->get('sort', 'date_asc'); 
        
        if ($sortParam) {
            $parts = explode('_', $sortParam);
            $sortColumn = $parts[0] ?? 'date';
            $sortDirection = $parts[1] ?? 'asc';
        }
        
        // 2. Start the base query for the logged-in user
        $query = Appointment::where('user_id', Auth::id());
        
        // 3. Add Search Filter (if present)
        $search = $request->get('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('patient', 'like', '%' . $search . '%')
                  ->orWhere('doctor', 'like', '%' . $search . '%')
                  ->orWhere('notes', 'like', '%' . $search . '%');
            });
        }


        // 4. Apply Sorting
        if ($sortColumn === 'status') {
            // Sort by status with 'upcoming' first, then by date/time
            $appointments = $query->orderByRaw("CASE WHEN status = 'upcoming' THEN 1 WHEN status = 'overdue' THEN 2 ELSE 3 END")
                                   ->orderBy('date', 'asc')
                                   ->orderBy('time', 'asc')
                                   ->get();
        } else {
            // Apply standard sorting by column/direction
            $appointments = $query->orderBy($sortColumn, $sortDirection)
                                  ->orderBy('time', $sortDirection) 
                                  ->get();
        }


        // Fetch all doctors for the modals
        $doctors = DB::table('doctors')->get();
        
        // Pass the current sort parameter to the view to maintain state
        return view('appointment', compact('appointments', 'doctors', 'sortParam')); 
    }


    // Store a new appointment
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'patient' => 'nullable|string', 
            'doctor' => 'required|string',
            'date' => 'required|date|after_or_equal:today', 
            'time' => 'required',
            'status' => 'required|in:upcoming,complete,overdue', 
            'notes' => 'nullable|string', 
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


    // Update appointment
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        // Prevent editing by other users
        if ($appointment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'type' => 'required|string',
            'patient' => 'nullable|string', 
            'doctor' => 'required|string',
            'date' => 'required|date|after_or_equal:today', 
            'time' => 'required',
            'status' => 'required|in:upcoming,complete,overdue', 
            'notes' => 'nullable|string', 
        ]);

        // 1. Update the record in the database
        $appointment->update([
            'type' => $request->type,
            'patient' => $request->patient, 
            'doctor' => $request->doctor,
            'date' => $request->date,
            'time' => $request->time,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        // 🎯 DISPATCH NOTIFICATION FOR EDIT
        $userName = Auth::user()->name ?? 'A user';
        Auth::user()->notify(new AppointmentEdited($appointment, $userName)); 
        // ------------------------------------------

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

        // 🎯 DISPATCH NOTIFICATION BEFORE DELETING
        $userName = Auth::user()->name ?? 'A user';
        Auth::user()->notify(new AppointmentDeleted($appointment, $userName)); 
        // ------------------------------------------
        
        $appointment->delete();

        return redirect()->route('appointments.index')
                         ->with('success', 'Appointment deleted successfully!');
    }
}