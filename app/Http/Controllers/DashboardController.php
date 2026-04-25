<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Doctor;
use App\Models\Medicine;
use App\Models\InsuranceClaim;
use App\Models\WalkInQueue;
use App\Models\Lead;
use App\Models\Consultation;
use App\Models\PatientMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $branchId = session('current_branch_id');
        $currentBranch = $branchId ? Branch::find($branchId) : null;

        $patientQuery = Patient::query();
        $appointmentQuery = Appointment::query();
        $invoiceQuery = Invoice::query();

        if ($branchId) {
            $patientQuery->where('branch_id', $branchId);
            $appointmentQuery->where('branch_id', $branchId);
            $invoiceQuery->where('branch_id', $branchId);
        }

        // Core KPIs
        $totalPatients = $patientQuery->count();
        $patientsLastMonth = (clone $patientQuery)->where('created_at', '<', now()->startOfMonth())->count();
        $patientsThisMonth = $totalPatients - $patientsLastMonth;
        $patientGrowth = $patientsLastMonth > 0 ? round((($patientsThisMonth) / $patientsLastMonth) * 100, 1) : 0;

        $todayAppointments = (clone $appointmentQuery)->whereDate('appointment_date', today())->count();
        $completedToday = (clone $appointmentQuery)->whereDate('appointment_date', today())->where('status', 'completed')->count();
        $pendingAppointments = (clone $appointmentQuery)->where('status', 'pending')->count();

        $monthlyRevenue = (clone $invoiceQuery)->where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');
        $lastMonthRevenue = (clone $invoiceQuery)->where('status', 'paid')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total');
        $revenueGrowth = $lastMonthRevenue > 0 ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : 0;

        // Secondary KPIs
        $totalDoctors = Doctor::when($branchId, fn($q) => $q->where('branch_id', $branchId))->where('is_active', true)->count();
        $lowStockMedicines = Medicine::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereColumn('current_stock', '<=', 'reorder_level')->count();
        $pendingClaims = InsuranceClaim::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereIn('status', ['draft', 'submitted'])->count();
        $activeMembers = PatientMembership::where('status', 'active')
            ->whereHas('patient', fn($q) => $branchId ? $q->where('branch_id', $branchId) : $q)
            ->count();

        // Today's queue snapshot
        $queueWaiting = WalkInQueue::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereDate('queue_date', today())->where('status', 'waiting')->count();
        $queueServing = WalkInQueue::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereDate('queue_date', today())->where('status', 'serving')->count();
        $queueCompleted = WalkInQueue::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereDate('queue_date', today())->where('status', 'completed')->count();
        $currentServing = WalkInQueue::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereDate('queue_date', today())->where('status', 'serving')
            ->with('doctor.user')->latest('called_at')->first();

        // In-progress consultations
        $inProgressConsultations = Consultation::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('status', 'in_progress')->count();

        // Sales pipeline (leads)
        $newLeads = Lead::where('status', 'new_lead')->count();
        $followUpsDueToday = Lead::whereDate('next_followup_at', today())
            ->whereNotIn('status', ['success', 'reject', 'duplicate'])
            ->count();

        // Monthly revenue trend (last 6 months)
        $revenueMonths = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $q = Invoice::where('status', 'paid')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year);
            if ($branchId) $q->where('branch_id', $branchId);
            $revenueMonths->push([
                'label' => $date->format('M Y'),
                'total' => $q->sum('total'),
            ]);
        }

        // Appointment status distribution (this month)
        $appointmentStats = (clone $appointmentQuery)
            ->whereMonth('appointment_date', now()->month)
            ->whereYear('appointment_date', now()->year)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Daily appointments (last 7 days)
        $dailyAppointments = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = (clone $appointmentQuery)->whereDate('appointment_date', $date)->count();
            $dailyAppointments->push([
                'label' => $date->format('D'),
                'count' => $count,
            ]);
        }

        // Top 5 services by revenue
        $topServices = DB::table('invoice_items')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->where('invoices.status', 'paid')
            ->whereMonth('invoices.created_at', now()->month)
            ->whereYear('invoices.created_at', now()->year)
            ->when($branchId, fn($q) => $q->where('invoices.branch_id', $branchId))
            ->select('invoice_items.description', DB::raw('SUM(invoice_items.total) as revenue'))
            ->groupBy('invoice_items.description')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        // Recent activity
        $recentAppointments = (clone $appointmentQuery)
            ->with(['patient', 'doctor.user'])
            ->orderBy('appointment_date', 'desc')
            ->limit(5)->get();

        $recentInvoices = (clone $invoiceQuery)
            ->with('patient')
            ->orderBy('created_at', 'desc')
            ->limit(5)->get();

        // Top performing doctor this month
        $topDoctor = DB::table('appointments')
            ->join('doctors', 'doctors.id', '=', 'appointments.doctor_id')
            ->join('users', 'users.id', '=', 'doctors.user_id')
            ->when($branchId, fn($q) => $q->where('appointments.branch_id', $branchId))
            ->whereMonth('appointments.appointment_date', now()->month)
            ->whereYear('appointments.appointment_date', now()->year)
            ->where('appointments.status', 'completed')
            ->select('users.name', DB::raw('count(*) as total_appointments'))
            ->groupBy('users.name')
            ->orderByDesc('total_appointments')
            ->first();

        return view('dashboard', compact(
            'currentBranch', 'totalPatients', 'todayAppointments',
            'pendingAppointments', 'monthlyRevenue', 'totalDoctors',
            'lowStockMedicines', 'pendingClaims', 'completedToday',
            'revenueMonths', 'appointmentStats', 'dailyAppointments',
            'topServices', 'recentAppointments', 'recentInvoices',
            'patientsThisMonth', 'patientGrowth', 'revenueGrowth',
            'queueWaiting', 'queueServing', 'queueCompleted', 'currentServing',
            'inProgressConsultations', 'activeMembers',
            'newLeads', 'followUpsDueToday', 'topDoctor'
        ));
    }

    public function switchBranch(Request $request)
    {
        $branchId = $request->input('branch_id');
        session(['current_branch_id' => $branchId ?: null]);
        return redirect()->back()->with('success', 'Branch switched.');
    }
}
