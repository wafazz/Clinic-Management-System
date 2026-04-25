<?php

namespace App\Http\Controllers;

use App\Models\TreatmentPlan;
use App\Models\TreatmentPlanSession;
use App\Models\TreatmentPlanTemplate;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TreatmentPlanController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');
        $plans = TreatmentPlan::with(['patient', 'doctor.user'])
            ->where('branch_id', $branchId)
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()->paginate(15)->withQueryString();
        return view('treatment-plans.index', compact('plans'));
    }

    public function create()
    {
        $branchId = session('current_branch_id');
        $patients = Patient::where('branch_id', $branchId)->where('is_active', true)->orderBy('name')->get();
        $doctors = Doctor::where('branch_id', $branchId)->where('is_active', true)->with('user')->get();
        $templates = TreatmentPlanTemplate::where('is_active', true)->orderBy('name')->get();
        return view('treatment-plans.create', compact('patients', 'doctors', 'templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'title' => 'required|string|max:255',
            'diagnosis' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'total_sessions' => 'required|integer|min:1',
            'interval_days' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'template_id' => 'nullable|exists:treatment_plan_templates,id',
            'notes' => 'nullable|string',
        ]);

        $branchId = session('current_branch_id');

        DB::transaction(function () use ($validated, $branchId) {
            $startDate = \Carbon\Carbon::parse($validated['start_date']);
            $expectedEnd = $startDate->copy()->addDays(($validated['total_sessions'] - 1) * $validated['interval_days']);

            $plan = TreatmentPlan::create([
                'patient_id' => $validated['patient_id'],
                'doctor_id' => $validated['doctor_id'],
                'branch_id' => $branchId,
                'template_id' => $validated['template_id'] ?? null,
                'plan_number' => TreatmentPlan::generateNumber(),
                'title' => $validated['title'],
                'diagnosis' => $validated['diagnosis'] ?? null,
                'description' => $validated['description'] ?? null,
                'total_sessions' => $validated['total_sessions'],
                'status' => 'active',
                'start_date' => $startDate,
                'expected_end_date' => $expectedEnd,
                'notes' => $validated['notes'] ?? null,
            ]);

            for ($i = 1; $i <= $validated['total_sessions']; $i++) {
                TreatmentPlanSession::create([
                    'treatment_plan_id' => $plan->id,
                    'session_number' => $i,
                    'scheduled_date' => $startDate->copy()->addDays(($i - 1) * $validated['interval_days']),
                    'status' => 'pending',
                ]);
            }
        });

        return redirect()->route('treatment-plans.index')->with('success', 'Treatment plan created.');
    }

    public function show(TreatmentPlan $treatmentPlan)
    {
        $treatmentPlan->load(['patient', 'doctor.user', 'sessions']);
        return view('treatment-plans.show', compact('treatmentPlan'));
    }

    public function completeSession(TreatmentPlanSession $session)
    {
        $session->update(['status' => 'completed', 'completed_at' => now()]);
        $plan = $session->treatmentPlan;
        $completed = $plan->sessions()->where('status', 'completed')->count();
        $plan->update([
            'completed_sessions' => $completed,
            'status' => $completed >= $plan->total_sessions ? 'completed' : 'active',
            'actual_end_date' => $completed >= $plan->total_sessions ? now() : null,
        ]);
        return back()->with('success', "Session {$session->session_number} completed.");
    }

    public function destroy(TreatmentPlan $treatmentPlan)
    {
        $treatmentPlan->update(['status' => 'cancelled']);
        return redirect()->route('treatment-plans.index')->with('success', 'Plan cancelled.');
    }

    public function pendingApproval()
    {
        $branchId = session('current_branch_id');
        $plans = TreatmentPlan::where('approval_status', 'pending_approval')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->with(['patient', 'createdByLocum'])
            ->latest()->paginate(15);
        return view('treatment-plans.pending-approval', compact('plans'));
    }

    public function approve(TreatmentPlan $treatmentPlan)
    {
        if ($treatmentPlan->approval_status !== 'pending_approval') {
            return back()->with('error', 'This plan is not pending approval.');
        }
        $treatmentPlan->update([
            'approval_status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'Treatment plan approved.');
    }

    public function reject(\Illuminate\Http\Request $request, TreatmentPlan $treatmentPlan)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);
        if ($treatmentPlan->approval_status !== 'pending_approval') {
            return back()->with('error', 'This plan is not pending approval.');
        }
        $treatmentPlan->update([
            'approval_status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
            'status' => 'cancelled',
        ]);
        return back()->with('success', 'Treatment plan rejected.');
    }
}
