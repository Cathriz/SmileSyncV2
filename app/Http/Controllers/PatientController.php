<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PatientController extends Controller
{
    /**
     * Handle the update or creation of patient profile details (Phone, Address, Documents).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        // 1. Validation
        $request->validate([
            'patient_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'permanent_document' => 'nullable|file|mimes:pdf,jpg,png|max:2048', 
        ]);

        $patientName = $request->input('patient_name');

        // 2. Check for existing profile
        $existingProfile = DB::table('patients')
            ->where('user_id', Auth::id())
            ->where('patient_name', $patientName)
            ->first();

        $fileName = $existingProfile->permanent_document ?? null;

        // 3. Handle Document Upload/Update
        if ($request->hasFile('permanent_document')) {
            // Delete old file if it exists
            if ($fileName && Storage::disk('public')->exists("uploads/$fileName")) {
                Storage::disk('public')->delete("uploads/$fileName");
            }
            
            // Upload new file
            $file = $request->file('permanent_document');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('uploads', $fileName, 'public');
        }

        // 4. Data Preparation
        $data = [
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'permanent_document' => $fileName,
            'updated_at' => now(),
        ];
        
        // 5. Insert or Update Logic
        if ($existingProfile) {
            // Update existing profile
            DB::table('patients')
                ->where('id', $existingProfile->id)
                ->update($data);
            $message = 'Patient profile updated successfully!';
        } else {
            // Create new profile
            DB::table('patients')->insert(array_merge($data, [
                'user_id' => Auth::id(),
                'patient_name' => $patientName,
                'created_at' => now(),
            ]));
            $message = 'New patient profile created successfully!';
        }

        // Redirect back to the patient profile page
        return redirect()->route('records.show_patient', ['patient_name' => $patientName])
                         ->with('success', $message);
    }
}