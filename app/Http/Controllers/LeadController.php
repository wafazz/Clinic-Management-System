<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $leads = Lead::with(['assignedTo', 'branch'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")->orWhere('phone', 'like', "%{$request->search}%");
            })
            ->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => Lead::count(),
            'new' => Lead::where('status', 'new_lead')->count(),
            'success' => Lead::where('status', 'success')->count(),
            'follow_up' => Lead::whereIn('status', ['followup_1','followup_2','followup_3','followup_4','followup_5'])->count(),
        ];

        return view('leads.index', compact('leads', 'stats'));
    }

    public function create()
    {
        $users = User::whereIn('role', ['admin', 'sales_team', 'receptionist'])->where('is_active', true)->get();
        return view('leads.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'ic_number' => 'nullable|string|max:30',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'source' => 'nullable|string|max:100',
            'service_interest' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);
        $validated['branch_id'] = session('current_branch_id');
        $validated['status'] = 'new_lead';
        Lead::create($validated);
        return redirect()->route('leads.index')->with('success', 'Lead created.');
    }

    public function show(Lead $lead)
    {
        $lead->load(['assignedTo', 'branch', 'patient']);
        return view('leads.show', compact('lead'));
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $request->validate([
            'status' => 'required|string',
            'last_followup_notes' => 'nullable|string',
            'next_followup_at' => 'nullable|date',
        ]);
        $lead->update([
            'status' => $request->status,
            'last_followup_notes' => $request->last_followup_notes,
            'last_contacted_at' => now(),
            'next_followup_at' => $request->next_followup_at,
        ]);
        return back()->with('success', 'Status updated.');
    }

    public function convert(Lead $lead)
    {
        if ($lead->patient_id) {
            return back()->with('error', 'Already converted.');
        }
        $branchId = $lead->branch_id ?? session('current_branch_id');
        $branch = \App\Models\Branch::find($branchId);
        $patient = Patient::create([
            'branch_id' => $branchId,
            'patient_id' => Patient::generatePatientId($branch->code ?? 'BR'),
            'name' => $lead->name,
            'phone' => $lead->phone,
            'email' => $lead->email,
            'ic_number' => $lead->ic_number,
            'gender' => $lead->gender,
            'date_of_birth' => $lead->date_of_birth,
            'is_active' => true,
        ]);
        $lead->update(['status' => 'success', 'converted_at' => now(), 'patient_id' => $patient->id]);
        return redirect()->route('patients.show', $patient)->with('success', 'Lead converted to patient.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('leads.index')->with('success', 'Lead deleted.');
    }
}
