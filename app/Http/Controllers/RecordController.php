<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Notifications\RecordAdded;
use App\Notifications\RecordEdited; 

class RecordController extends Controller
{
    /**
     * Display a listing of the resource. Synchronizes appointments to records.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 1. --- APPOINTMENT SYNC LOGIC (Prevents Duplication) ---
        $appointments = DB::table('appointments')
            ->whereNotIn('type', ['personal', 'meeting'])
            ->where('user_id', Auth::id())
            ->get();

        foreach ($appointments as $a) {
            $existingRecord = DB::table('records')
                ->where('user_id', Auth::id())
                ->where('appointment_id', $a->id)
                ->first();

            $data = [
                'patient' => $a->patient,
                'doctor' => $a->doctor,
                'type' => $a->type,
                'date' => $a->date,
                'time' => $a->time,
                'notes' => $a->notes,
                'document' => $a->document ?? null,
                'updated_at' => now(),
            ];

            if (!$existingRecord) {
                DB::table('records')->insert(array_merge($data, [
                    'user_id' => Auth::id(),
                    'appointment_id' => $a->id,
                    'created_at' => now(),
                ]));
            } else {
                DB::table('records')
                    ->where('id', $existingRecord->id)
                    ->update($data);
            }
        }

        // 2. --- SEARCH AND SORT FUNCTIONALITY ---
        $search = $request->input('search');
        
        $query = DB::table('records')->where('user_id', Auth::id());

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('patient', 'like', "%{$search}%")
                      ->orWhere('doctor', 'like', "%{$search}%")
                      ->orWhere('type', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $records = $query->orderBy('date', 'desc')->get();

        return view('records', compact('records'));
    }

    /**
     * Display the profile and all records for a specific patient.
     *
     * @param  string  $patient_name
     * @return \Illuminate\View\View
     */
    public function showPatient($patient_name)
    {
        // 1. Fetch all medical records for the specified patient name for the authenticated user
        $patientRecords = DB::table('records')
            ->where('user_id', Auth::id())
            ->where('patient', $patient_name)
            ->orderBy('date', 'desc')
            ->get();
            
        // 2. Fetch patient profile details from the separate 'patients' table
        $patientProfile = DB::table('patients')
            ->where('user_id', Auth::id())
            ->where('patient_name', $patient_name)
            ->first();

        // Pass both records and profile to the view
        return view('patient_profile', [
            'patient_name' => $patient_name,
            'records' => $patientRecords,
            'profile' => $patientProfile, // This is the new data passed
        ]);
    }

    /**
     * Store a manually created record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Request $request)
    {
        $fileName = null;

        if ($request->hasFile('document')) {
            $fileName = time().'_'.$request->file('document')->getClientOriginalName();
            $request->file('document')->storeAs('uploads', $fileName, 'public');
        }

        $recordId = DB::table('records')->insertGetId([
            'user_id' => Auth::id(),
            'appointment_id' => null,
            'patient' => $request->patient_name,
            'doctor' => $request->doctor_name,
            'type' => $request->type,
            'date' => $request->date,
            'time' => $request->time,
            'notes' => $request->notes,
            'document' => $fileName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $newRecord = DB::table('records')->where('id', $recordId)->first();

        if ($newRecord) {
            $userName = Auth::user()->name ?? 'A user';
            Auth::user()->notify(new RecordAdded($newRecord, $userName));
        }

        return redirect('/records')->with('success', 'Record added successfully!');
    }

    /**
     * Update the specified record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $recordId = $request->id;
        
        $oldRecord = DB::table('records')
            ->where('id', $recordId)
            ->where('user_id', Auth::id())
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
            ->where('user_id', Auth::id())
            ->update([
                'patient' => $request->patient_name,
                'doctor' => $request->doctor_name,
                'type' => $request->type,
                'date' => $request->date,
                'time' => $request->time,
                'notes' => $request->notes,
                'document' => $fileName,
                'updated_at' => now(),
            ]);

        $updatedRecord = DB::table('records')->where('id', $recordId)->first();

        if ($updatedRecord) {
            $userName = Auth::user()->name ?? 'A user';
            Auth::user()->notify(new RecordEdited($updatedRecord, $userName));
        }

        return redirect('/records')->with('success', 'Record updated successfully!');
    }

    /**
     * Remove the specified record.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $record = DB::table('records')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$record) {
            return redirect('/records')->with('error', 'Record not found or unauthorized.');
        }

        if ($record->document && Storage::disk('public')->exists("uploads/$record->document")) {
            Storage::disk('public')->delete("uploads/$record->document");
        }

        DB::table('records')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return redirect('/records')->with('success', 'Record deleted successfully.');
    }
}