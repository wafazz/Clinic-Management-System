<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $branchId = $request->input('branch_id', session('current_branch_id'));
        $branches = Branch::where('is_active', true)->get();
        $users = User::where('is_active', true)->orderBy('name')->get();

        $query = AuditLog::with(['user', 'branch'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($request->user_id, fn($q, $v) => $q->where('user_id', $v))
            ->when($request->action, fn($q, $v) => $q->where('action', $v))
            ->when($request->model_type, fn($q, $v) => $q->where('model_type', 'App\\Models\\' . $v))
            ->when($request->from, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->to, fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when($request->search, fn($q, $v) => $q->where('description', 'like', "%{$v}%"))
            ->orderByDesc('created_at');

        $logs = $query->paginate(25)->withQueryString();

        $modelTypes = [
            'Patient', 'Appointment', 'Invoice', 'Doctor', 'Branch', 'Service',
            'Medicine', 'Prescription', 'LabReport', 'LabTest', 'InsurancePanel',
            'InsuranceClaim', 'LocumDoctor', 'LocumSession', 'User',
        ];

        return view('audit-logs.index', compact('logs', 'branches', 'users', 'modelTypes', 'branchId'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load(['user', 'branch']);
        return view('audit-logs.show', compact('auditLog'));
    }
}
