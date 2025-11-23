<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Required for Auth::user()
// 🎯 ADD THESE IMPORTS:
use App\Notifications\DoctorAdded;
use App\Notifications\DoctorDeleted; 

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all doctors to display in the table
        $doctors = Doctor::orderBy('name')->get();
        
        // Pass the doctors list to the view, now pointing to doctor.blade.php
        return view('doctor', compact('doctors')); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('doctors.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:doctors,email',
        ]);

        // 2. Create the Doctor record
        $doctor = Doctor::create($request->all());

        // 🎯 DISPATCH NOTIFICATION: Doctor Added
        $userName = Auth::user()->name ?? 'A user';
        Auth::user()->notify(new DoctorAdded($doctor, $userName));
        // ----------------------------------------

        // 3. Redirect back to the index view with a success message
        return redirect()->route('doctors.index')->with('success', 'Doctor added successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor)
    {
        // Save the doctor's name before deletion, as the object is lost afterward
        $doctorName = $doctor->name; 
        
        // 1. Delete the Doctor record
        $doctor->delete();

        // 🎯 DISPATCH NOTIFICATION: Doctor Deleted
        $userName = Auth::user()->name ?? 'A user';
        // Pass the doctor's name to the notification class
        Auth::user()->notify(new DoctorDeleted($doctorName, $userName)); 
        // ------------------------------------------

        // 2. Redirect back with a success message
        return redirect()->route('doctors.index')->with('success', 'Doctor deleted successfully!');
    }

    // Edit and Update methods are omitted for simplicity but follow the same pattern
}