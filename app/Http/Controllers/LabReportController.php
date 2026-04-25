<?php

namespace App\Http\Controllers;

use App\Models\LabReport;
use App\Models\LabReportItem;
use App\Models\LabTest;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabReportController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');

        $labReports = LabReport::where('branch_id', $branchId)
            ->with(['patient', 'doctor.user', 'items'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('report_number', 'like', "%{$request->search}%")
                       ->orWhereHas('patient', fn($q3) => $q3->where('name', 'like', "%{$request->search}%"));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('lab.reports.index', compact('labReports'));
    }

    public function create()
    {
        $branchId = session('current_branch_id');
        $patients = Patient::where('branch_id', $branchId)->where('is_active', true)->orderBy('name')->get();
        $doctors = Doctor::where('branch_id', $branchId)->where('is_active', true)->with('user')->get();
        $labTests = LabTest::where('branch_id', $branchId)->where('is_active', true)->orderBy('name')->get();

        return view('lab.reports.create', compact('patients', 'doctors', 'labTests'));
    }

    public function store(Request $request)
    {
        $branchId = session('current_branch_id');
        $branch = Branch::findOrFail($branchId);

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'notes' => 'nullable|string',
            'tests' => 'required|array|min:1',
            'tests.*' => 'exists:lab_tests,id',
        ]);

        DB::transaction(function () use ($request, $branchId, $branch) {
            $report = LabReport::create([
                'branch_id' => $branchId,
                'patient_id' => $request->patient_id,
                'doctor_id' => $request->doctor_id,
                'appointment_id' => $request->appointment_id,
                'report_number' => LabReport::generateReportNumber($branch->code),
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            foreach ($request->tests as $testId) {
                LabReportItem::create([
                    'lab_report_id' => $report->id,
                    'lab_test_id' => $testId,
                ]);
            }
        });

        return redirect()->route('lab-reports.index')->with('success', 'Lab report created successfully.');
    }

    public function show(LabReport $labReport)
    {
        $labReport->load(['patient', 'doctor.user', 'appointment', 'items.test', 'branch']);
        return view('lab.reports.show', compact('labReport'));
    }

    public function edit(LabReport $labReport)
    {
        if ($labReport->status === 'completed') {
            return redirect()->route('lab-reports.show', $labReport)->with('error', 'Completed reports cannot be edited.');
        }

        $labReport->load('items.test');
        return view('lab.reports.edit', compact('labReport'));
    }

    public function update(Request $request, LabReport $labReport)
    {
        if ($labReport->status === 'completed') {
            return redirect()->route('lab-reports.show', $labReport)->with('error', 'Completed reports cannot be edited.');
        }

        $request->validate([
            'results' => 'required|array',
            'results.*.lab_report_item_id' => 'required|exists:lab_report_items,id',
            'results.*.result' => 'nullable|string|max:255',
            'results.*.is_abnormal' => 'nullable|boolean',
            'results.*.notes' => 'nullable|string|max:500',
            'status' => 'required|in:pending,in_progress,completed',
            'report_notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $labReport) {
            foreach ($request->results as $result) {
                LabReportItem::where('id', $result['lab_report_item_id'])
                    ->update([
                        'result' => $result['result'] ?? null,
                        'is_abnormal' => !empty($result['is_abnormal']),
                        'notes' => $result['notes'] ?? null,
                    ]);
            }

            $updateData = [
                'status' => $request->status,
                'notes' => $request->report_notes,
            ];

            if ($request->status === 'completed') {
                $updateData['reported_at'] = now();
            }

            $labReport->update($updateData);
        });

        return redirect()->route('lab-reports.show', $labReport)->with('success', 'Lab report updated successfully.');
    }

    public function destroy(LabReport $labReport)
    {
        if ($labReport->status === 'completed') {
            return back()->with('error', 'Cannot delete a completed lab report.');
        }

        $labReport->items()->delete();
        $labReport->delete();

        return redirect()->route('lab-reports.index')->with('success', 'Lab report deleted successfully.');
    }

    public function print(LabReport $labReport)
    {
        $labReport->load(['patient', 'doctor.user', 'appointment', 'items.test', 'branch']);
        return view('lab.reports.print', compact('labReport'));
    }
}
