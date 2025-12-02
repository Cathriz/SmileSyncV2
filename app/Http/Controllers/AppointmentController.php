<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Record;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\AppointmentDeleted;
use App\Notifications\AppointmentEdited;

class AppointmentController extends Controller
{
    /**
     * Show the appointments page (user-only).
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Sorting
        $sortParam = $request->get('sort', 'date_asc');
        $parts = explode('_', $sortParam);
        $sortColumn = $parts[0] ?? 'date';
        $sortDirection = $parts[1] ?? 'asc';

        // Base query: only current user's appointments
        $query = Appointment::where('user_id', $userId);

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('patient', 'like', '%' . $search . '%')
                  ->orWhere('doctor', 'like', '%' . $search . '%')
                  ->orWhere('notes', 'like', '%' . $search . '%');
            });
        }

        // Sorting with status special-case
        if ($sortColumn === 'status') {
            $appointments = $query->orderByRaw(
                    "CASE WHEN status = 'upcoming' THEN 1 WHEN status = 'overdue' THEN 2 ELSE 3 END"
                )
                ->orderBy('date', 'asc')
                ->orderBy('time', 'asc')
                ->get();
        } else {
            $appointments = $query->orderBy($sortColumn, $sortDirection)
                                  ->orderBy('time', $sortDirection)
                                  ->get();
        }

        // doctors for modals (you might later scope this to the clinic / user)
        $doctors = DB::table('doctors')->get();

        return view('appointment', compact('appointments', 'doctors', 'sortParam'));
    }

    /**
     * Store new appointment (user-only).
     */
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

        return redirect('/appointments')->with('success', 'Schedule added successfully!');
    }

    /**
     * Update appointment (user-only).
     */
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'type' => 'required|string',
            'patient' => 'nullable|string',
            'doctor' => 'required|string',
            'date' => 'required|date',
            'time' => 'required',
            'status' => 'required|in:upcoming,complete,overdue',
            'notes' => 'nullable|string',
        ]);

        $appointment->update($validatedData);

        // If completed, move to records (updateOrCreate to avoid duplicates)
        if ($request->status === 'complete') {
            $recordData = $appointment->toArray();
            unset($recordData['id']);
            Record::updateOrCreate(
                [
                    'user_id' => $appointment->user_id,
                    'date' => $appointment->date,
                    'time' => $appointment->time,
                ],
                $recordData
            );
        }

        // Notify user (optional)
        $userName = Auth::user()->name ?? 'A user';
        Auth::user()->notify(new AppointmentEdited($appointment, $userName));

        return redirect('/appointments')->with('success', 'Appointment updated successfully!');
    }

    /**
     * Delete appointment (user-only).
     */
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $userName = Auth::user()->name ?? 'A user';
        Auth::user()->notify(new AppointmentDeleted($appointment, $userName));

        $appointment->delete();

        return redirect('/appointments')->with('success', 'Appointment deleted successfully!');
    }
}
