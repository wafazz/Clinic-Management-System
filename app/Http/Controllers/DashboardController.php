<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Doctor;
use App\Models\Medicine;
use App\Models\InsuranceClaim;
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

        // KPI cards
        $totalPatients = $patientQuery->count();
        $todayAppointments = (clone $appointmentQuery)->whereDate('appointment_date', today())->count();
        $pendingAppointments = (clone $appointmentQuery)->where('status', 'pending')->count();
        $monthlyRevenue = (clone $invoiceQuery)->where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        // Additional KPIs
        $totalDoctors = Doctor::when($branchId, fn($q) => $q->where('branch_id', $branchId))->where('is_active', true)->count();
        $lowStockMedicines = Medicine::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereColumn('current_stock', '<=', 'reorder_level')->count();
        $pendingClaims = InsuranceClaim::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereIn('status', ['draft', 'submitted'])->count();
        $completedToday = (clone $appointmentQuery)->whereDate('appointment_date', today())->where('status', 'completed')->count();

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

        // Recent tables
        $recentAppointments = (clone $appointmentQuery)
            ->with(['patient', 'doctor.user'])
            ->orderBy('appointment_date', 'desc')
            ->limit(5)->get();

        $recentInvoices = (clone $invoiceQuery)
            ->with('patient')
            ->orderBy('created_at', 'desc')
            ->limit(5)->get();

        return view('dashboard', compact(
            'currentBranch', 'totalPatients', 'todayAppointments',
            'pendingAppointments', 'monthlyRevenue', 'totalDoctors',
            'lowStockMedicines', 'pendingClaims', 'completedToday',
            'revenueMonths', 'appointmentStats', 'dailyAppointments',
            'topServices', 'recentAppointments', 'recentInvoices'
        ));
    }

    public function switchBranch(Request $request)
    {
        $branchId = $request->input('branch_id');
        session(['current_branch_id' => $branchId ?: null]);
        return redirect()->back()->with('success', 'Branch switched.');
    }
}
