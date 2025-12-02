<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; 
use App\Models\Record;

class RecordController extends Controller
{
    /**
     * Display all records for the currently logged-in user with search.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $search = $request->input('search');
        
        $query = Record::where('user_id', $userId); 

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('patient', 'like', "%{$search}%")
                  ->orWhere('doctor', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $records = $query->orderBy('date', 'desc')->get();
        
        $doctors = DB::table('doctors')->get(); 
        
        return view('records', compact('records', 'doctors'));
    }

    /**
     * Show patient profile + records
     */
    public function showPatient($patient_name)
    {
        $userId = Auth::id();
        $doctors = DB::table('doctors')->get();

        // 🟢 FIX: Load the correct patient profile from 'patients' table
        $profile = DB::table('patients')
            ->where('user_id', $userId)
            ->where('patient_name', $patient_name)
            ->first();

        // Patient-related appointment records
        $patientRecords = DB::table('records')
            ->where('user_id', $userId)
            ->where('patient', $patient_name)
            ->orderBy('date', 'desc')
            ->get();

        return view('patient_profile', [
            'patient_name' => $patient_name,
            'records' => $patientRecords,
            'doctors' => $doctors,
            'profile' => $profile, // 🟢 NOW IT SHOWS THE UPDATED PROFILE
        ]);
    }

    /**
     * Add a new record.
     */
    public function add(Request $request)
    {
        $request->validate([
            'patient_name' => 'required|string',
            'doctor_name' => 'required|string',
            'date' => 'required|date',
            'time' => 'required',
            'type' => 'required|string',
            'notes' => 'nullable|string',
            'document' => 'nullable|file|max:2048', 
        ]);

        $fileName = null;
        if ($request->hasFile('document')) {
            $fileName = time().'_'.$request->file('document')->getClientOriginalName();
            $request->file('document')->storeAs('uploads', $fileName, 'public');
        }

        DB::table('records')->insert([
            'user_id' => Auth::id(), 
            'patient' => $request->patient_name,
            'doctor' => $request->doctor_name,
            'type' => $request->type,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'complete', 
            'notes' => $request->notes,
            'document' => $fileName,
            'document_path' => null, 
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/records')->with('success', 'Record added successfully!');
    }

    /**
     * Update an existing record.
     */
    public function update(Request $request)
    {
        $recordId = $request->id;
        $userId = Auth::id();
        
        $oldRecord = DB::table('records')
                       ->where('id', $recordId)
                       ->where('user_id', $userId) 
                       ->first(); 

        if (!$oldRecord) {
            return redirect('/records')->with('error', 'Record not found or unauthorized.');
        }

        $fileName = $oldRecord->document;
        if ($request->hasFile('document')) {
            if ($oldRecord->document && Storage::disk('public')->exists("uploads/$oldRecord->document")) {
                Storage::disk('public')->delete("uploads/$oldRecord->document");
            }
            $fileName = time().'_'.$request->file('document')->getClientOriginalName();
            $request->file('document')->storeAs('uploads', $fileName, 'public');
        }

        DB::table('records')
            ->where('id', $recordId)
            ->where('user_id', $userId) 
            ->update([
                'patient' => $request->patient_name,
                'doctor' => $request->doctor_name,
                'type' => $request->type,
                'date' => $request->date,
                'time' => $request->time,
                'status' => $request->status ?? $oldRecord->status,
                'notes' => $request->notes,
                'document' => $fileName,
                'updated_at' => now(),
            ]);

        return redirect('/records')->with('success', 'Record updated successfully!');
    }

    /**
     * Delete an existing record.
     */
    public function delete($id)
    {
        $userId = Auth::id();

        $record = DB::table('records')
                     ->where('id', $id)
                     ->where('user_id', $userId) 
                     ->first();

        if (!$record) {
            return redirect('/records')->with('error', 'Record not found or unauthorized.');
        }

        if ($record->document && Storage::disk('public')->exists("uploads/$record->document")) {
            Storage::disk('public')->delete("uploads/$record->document");
        }

        DB::table('records')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->delete();

        return redirect('/records')->with('success', 'Record deleted successfully.');
    }
}
