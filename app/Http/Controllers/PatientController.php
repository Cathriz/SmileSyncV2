<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PatientController extends Controller
{
    /**
     * Handle the update or creation of patient profile details.
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
        $userId = Auth::id();

        // 2. Check for existing profile
        $existingProfile = DB::table('patients')
            ->where('user_id', $userId)
            ->where('patient_name', $patientName)
            ->first();

        // 3. Handle Document Upload
        $filePath = $existingProfile->permanent_document ?? null;

        if ($request->hasFile('permanent_document')) {
            // Delete old file if exists
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            // Save new file
            $file = $request->file('permanent_document');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = 'uploads/' . $fileName;
            $file->storeAs('uploads', $fileName, 'public');
        }

        // 4. Prepare data for DB
        $data = [
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'permanent_document' => $filePath,
            'updated_at' => now(),
        ];

        // 5. Insert or Update
        if ($existingProfile) {
            DB::table('patients')
                ->where('id', $existingProfile->id)
                ->update($data);
            $message = 'Patient profile updated successfully!';
        } else {
            DB::table('patients')->insert(array_merge($data, [
                'user_id' => $userId,
                'patient_name' => $patientName,
                'created_at' => now(),
            ]));
            $message = 'New patient profile created successfully!';
        }

        return redirect()->route('records.show_patient', ['patient_name' => $patientName])
                         ->with('success', $message);
    }
}
