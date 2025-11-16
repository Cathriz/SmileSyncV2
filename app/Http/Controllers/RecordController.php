<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RecordController extends Controller
{
    public function index()
    {
        $appointments = DB::table('appointments')
            ->whereNotIn('type', ['personal', 'meeting'])
            ->get();

        foreach ($appointments as $a) {
            $exists = DB::table('records')
                ->where([
                    ['patient', $a->patient],
                    ['date', $a->date],
                    ['time', $a->time],
                ])
                ->exists();

            if (!$exists) {
                DB::table('records')->insert([
                    'patient' => $a->patient,
                    'doctor' => $a->doctor,
                    'type' => $a->type,
                    'date' => $a->date,
                    'time' => $a->time,
                    'status' => $a->status,
                    'notes' => $a->notes,
                ]);
            }
        }

        $records = DB::table('records')->orderBy('date', 'DESC')->get();

        return view('records', compact('records'));
    }

    public function add(Request $request)
    {
        $fileName = null;

        if ($request->hasFile('document')) {
            $fileName = time().'_'.$request->file('document')->getClientOriginalName();
            $request->file('document')->storeAs('uploads', $fileName, 'public');
        }

        DB::table('records')->insert([
            'patient' => $request->patient_name,
            'doctor' => $request->doctor_name,
            'type' => $request->type,
            'date' => $request->date,
            'time' => $request->time,
            'status' => $request->status,
            'notes' => $request->notes,
            'document' => $fileName
        ]);

        return redirect()->route('records');
    }

    public function update(Request $request)
    {
        $fileName = $request->existing_document;

        if ($request->hasFile('document')) {
            $fileName = time().'_'.$request->file('document')->getClientOriginalName();
            $request->file('document')->storeAs('uploads', $fileName, 'public');
        }

        DB::table('records')
            ->where('id', $request->id)
            ->update([
                'patient' => $request->patient_name,
                'doctor' => $request->doctor_name,
                'type' => $request->type,
                'date' => $request->date,
                'time' => $request->time,
                'status' => $request->status,
                'notes' => $request->notes,
                'document' => $fileName
            ]);

        return redirect()->route('records');
    }

    public function delete($id)
    {
        $record = DB::table('records')->where('id', $id)->first();

        if (!$record) {
            return redirect()->route('records')->with('error', 'Record not found.');
        }

        if ($record->document && Storage::disk('public')->exists("uploads/$record->document")) {
            Storage::disk('public')->delete("uploads/$record->document");
        }

        DB::table('records')->where('id', $id)->delete();

        return redirect()->route('records')->with('success', 'Record deleted successfully.');
    }
}
