<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    public function index(Doctor $doctor)
    {
        $doctor->load('user');
        $schedules = $doctor->schedules()->orderBy('day_of_week')->get();
        return view('doctor-schedules.index', compact('doctor', 'schedules'));
    }

    public function store(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required',
            'end_time' => 'required',
            'slot_duration' => 'required|integer|min:5|max:120',
        ]);

        $validated['doctor_id'] = $doctor->id;
        $validated['branch_id'] = $doctor->branch_id;
        $validated['is_available'] = $request->boolean('is_available', true);

        DoctorSchedule::updateOrCreate(
            ['doctor_id' => $doctor->id, 'branch_id' => $doctor->branch_id, 'day_of_week' => $validated['day_of_week']],
            $validated
        );

        return redirect()->route('doctor-schedules.index', $doctor)->with('success', 'Schedule updated.');
    }

    public function destroy(DoctorSchedule $doctorSchedule)
    {
        $doctorId = $doctorSchedule->doctor_id;
        $doctorSchedule->delete();
        return redirect()->route('doctor-schedules.index', $doctorId)->with('success', 'Schedule removed.');
    }
}
