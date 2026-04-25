<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Doctor;
use App\Models\Medicine;
use App\Models\InsuranceClaim;
use App\Models\LabReport;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function financial(Request $request)
    {
        $branchId = $request->input('branch_id', session('current_branch_id'));
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));
        $branches = Branch::where('is_active', true)->get();

        $invoiceQuery = Invoice::whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId));

        $totalRevenue = (clone $invoiceQuery)->where('status', 'paid')->sum('total');
        $totalOutstanding = (clone $invoiceQuery)->whereIn('status', ['issued', 'partial'])->sum('total');
        $totalInvoices = (clone $invoiceQuery)->count();
        $paidCount = (clone $invoiceQuery)->where('status', 'paid')->count();

        // Revenue by branch
        $revenueByBranch = Invoice::where('invoices.status', 'paid')
            ->whereBetween('invoices.created_at', [$from, Carbon::parse($to)->endOfDay()])
            ->join('branches', 'branches.id', '=', 'invoices.branch_id')
            ->select('branches.name', DB::raw('SUM(invoices.total) as revenue'))
            ->groupBy('branches.name')
            ->orderByDesc('revenue')
            ->get();

        // Revenue by doctor
        $revenueByDoctor = Invoice::where('invoices.status', 'paid')
            ->whereBetween('invoices.created_at', [$from, Carbon::parse($to)->endOfDay()])
            ->when($branchId, fn($q) => $q->where('invoices.branch_id', $branchId))
            ->join('appointments', 'appointments.id', '=', 'invoices.appointment_id')
            ->join('doctors', 'doctors.id', '=', 'appointments.doctor_id')
            ->join('users', 'users.id', '=', 'doctors.user_id')
            ->select('users.name', DB::raw('SUM(invoices.total) as revenue'), DB::raw('COUNT(invoices.id) as invoice_count'))
            ->groupBy('users.name')
            ->orderByDesc('revenue')
            ->get();

        // Revenue by service
        $revenueByService = DB::table('invoice_items')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->where('invoices.status', 'paid')
            ->whereBetween('invoices.created_at', [$from, Carbon::parse($to)->endOfDay()])
            ->when($branchId, fn($q) => $q->where('invoices.branch_id', $branchId))
            ->select('invoice_items.description', DB::raw('SUM(invoice_items.total) as revenue'), DB::raw('SUM(invoice_items.quantity) as qty'))
            ->groupBy('invoice_items.description')
            ->orderByDesc('revenue')
            ->limit(15)
            ->get();

        // Payment method breakdown
        $paymentMethods = DB::table('payments')
            ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->whereBetween('payments.payment_date', [$from, $to])
            ->when($branchId, fn($q) => $q->where('invoices.branch_id', $branchId))
            ->select('payments.method', DB::raw('SUM(payments.amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('payments.method')
            ->get();

        // Daily revenue chart
        $dailyRevenue = Invoice::where('status', 'paid')
            ->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('reports.financial', compact(
            'branches', 'branchId', 'from', 'to',
            'totalRevenue', 'totalOutstanding', 'totalInvoices', 'paidCount',
            'revenueByBranch', 'revenueByDoctor', 'revenueByService',
            'paymentMethods', 'dailyRevenue'
        ));
    }

    public function patients(Request $request)
    {
        $branchId = $request->input('branch_id', session('current_branch_id'));
        $branches = Branch::where('is_active', true)->get();

        $patientQuery = Patient::when($branchId, fn($q) => $q->where('branch_id', $branchId));

        $totalPatients = (clone $patientQuery)->count();
        $newThisMonth = (clone $patientQuery)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $activePatients = (clone $patientQuery)->where('is_active', true)->count();

        // Gender distribution
        $genderDist = (clone $patientQuery)->select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')->pluck('total', 'gender');

        // Blood type distribution
        $bloodDist = (clone $patientQuery)->whereNotNull('blood_type')
            ->select('blood_type', DB::raw('count(*) as total'))
            ->groupBy('blood_type')->pluck('total', 'blood_type');

        // New patients per month (last 6 months)
        $monthlyNew = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = (clone $patientQuery)->whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->count();
            $monthlyNew->push(['label' => $date->format('M Y'), 'count' => $count]);
        }

        // Top patients by visits
        $topPatients = Patient::when($branchId, fn($q) => $q->where('patients.branch_id', $branchId))
            ->withCount('appointments')
            ->orderByDesc('appointments_count')
            ->limit(10)
            ->get();

        // Insurance coverage
        $insuredCount = Patient::when($branchId, fn($q) => $q->where('patients.branch_id', $branchId))
            ->whereHas('insurances', fn($q) => $q->where('status', 'active'))
            ->count();

        return view('reports.patients', compact(
            'branches', 'branchId', 'totalPatients', 'newThisMonth', 'activePatients',
            'genderDist', 'bloodDist', 'monthlyNew', 'topPatients', 'insuredCount'
        ));
    }

    public function appointments(Request $request)
    {
        $branchId = $request->input('branch_id', session('current_branch_id'));
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));
        $branches = Branch::where('is_active', true)->get();

        $apptQuery = Appointment::whereBetween('appointment_date', [$from, $to])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId));

        $totalAppointments = (clone $apptQuery)->count();
        $completedCount = (clone $apptQuery)->where('status', 'completed')->count();
        $cancelledCount = (clone $apptQuery)->where('status', 'cancelled')->count();
        $completionRate = $totalAppointments > 0 ? round(($completedCount / $totalAppointments) * 100, 1) : 0;

        // Status breakdown
        $statusBreakdown = (clone $apptQuery)->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status');

        // Appointments by doctor
        $byDoctor = Appointment::whereBetween('appointment_date', [$from, $to])
            ->when($branchId, fn($q) => $q->where('appointments.branch_id', $branchId))
            ->join('doctors', 'doctors.id', '=', 'appointments.doctor_id')
            ->join('users', 'users.id', '=', 'doctors.user_id')
            ->select('users.name', DB::raw('count(*) as total'),
                DB::raw("SUM(CASE WHEN appointments.status = 'completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN appointments.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled"))
            ->groupBy('users.name')
            ->orderByDesc('total')
            ->get();

        // Peak hours
        $peakHours = (clone $apptQuery)
            ->select(DB::raw('HOUR(start_time) as hour'), DB::raw('count(*) as total'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Daily trend
        $dailyTrend = (clone $apptQuery)
            ->select(DB::raw('DATE(appointment_date) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Day of week distribution
        $dayOfWeek = (clone $apptQuery)
            ->select(DB::raw('DAYNAME(appointment_date) as day_name'), DB::raw('DAYOFWEEK(appointment_date) as day_num'), DB::raw('count(*) as total'))
            ->groupBy('day_name', 'day_num')
            ->orderBy('day_num')
            ->get();

        return view('reports.appointments', compact(
            'branches', 'branchId', 'from', 'to',
            'totalAppointments', 'completedCount', 'cancelledCount', 'completionRate',
            'statusBreakdown', 'byDoctor', 'peakHours', 'dailyTrend', 'dayOfWeek'
        ));
    }

    public function pharmacy(Request $request)
    {
        $branchId = $request->input('branch_id', session('current_branch_id'));
        $branches = Branch::where('is_active', true)->get();

        $medicineQuery = Medicine::when($branchId, fn($q) => $q->where('branch_id', $branchId));

        $totalMedicines = (clone $medicineQuery)->count();
        $lowStock = (clone $medicineQuery)->whereColumn('current_stock', '<=', 'reorder_level')->get();
        $expiringSoon = (clone $medicineQuery)->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addMonths(3)])->get();
        $expired = (clone $medicineQuery)->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())->count();

        // Stock value
        $stockValue = (clone $medicineQuery)->select(DB::raw('SUM(current_stock * cost_price) as cost_value'), DB::raw('SUM(current_stock * selling_price) as sell_value'))->first();

        // Top dispensed medicines (last 30 days)
        $topDispensed = DB::table('prescription_items')
            ->join('prescriptions', 'prescriptions.id', '=', 'prescription_items.prescription_id')
            ->join('medicines', 'medicines.id', '=', 'prescription_items.medicine_id')
            ->where('prescriptions.status', 'dispensed')
            ->where('prescriptions.created_at', '>=', now()->subDays(30))
            ->when($branchId, fn($q) => $q->where('prescriptions.branch_id', $branchId))
            ->select('medicines.name', DB::raw('SUM(prescription_items.quantity) as total_qty'))
            ->groupBy('medicines.name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // Stock by category
        $stockByCategory = Medicine::when($branchId, fn($q) => $q->where('medicines.branch_id', $branchId))
            ->join('pharmacy_categories', 'pharmacy_categories.id', '=', 'medicines.pharmacy_category_id')
            ->select('pharmacy_categories.name', DB::raw('COUNT(medicines.id) as count'), DB::raw('SUM(medicines.current_stock) as total_stock'))
            ->groupBy('pharmacy_categories.name')
            ->get();

        return view('reports.pharmacy', compact(
            'branches', 'branchId', 'totalMedicines', 'lowStock', 'expiringSoon', 'expired',
            'stockValue', 'topDispensed', 'stockByCategory'
        ));
    }

    public function lab(Request $request)
    {
        $branchId = $request->input('branch_id', session('current_branch_id'));
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));
        $branches = Branch::where('is_active', true)->get();

        $reportQuery = LabReport::whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId));

        $totalReports = (clone $reportQuery)->count();
        $completedReports = (clone $reportQuery)->where('status', 'completed')->count();
        $pendingReports = (clone $reportQuery)->where('status', 'pending')->count();

        // Status breakdown
        $statusBreakdown = (clone $reportQuery)->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status');

        // Top tests ordered
        $topTests = DB::table('lab_report_items')
            ->join('lab_reports', 'lab_reports.id', '=', 'lab_report_items.lab_report_id')
            ->join('lab_tests', 'lab_tests.id', '=', 'lab_report_items.lab_test_id')
            ->whereBetween('lab_reports.created_at', [$from, Carbon::parse($to)->endOfDay()])
            ->when($branchId, fn($q) => $q->where('lab_reports.branch_id', $branchId))
            ->select('lab_tests.name', DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN lab_report_items.is_abnormal = 1 THEN 1 ELSE 0 END) as abnormal_count"))
            ->groupBy('lab_tests.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Daily volume
        $dailyVolume = (clone $reportQuery)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('reports.lab', compact(
            'branches', 'branchId', 'from', 'to',
            'totalReports', 'completedReports', 'pendingReports',
            'statusBreakdown', 'topTests', 'dailyVolume'
        ));
    }

    // === CSV Exports ===

    public function exportFinancial(Request $request)
    {
        $branchId = $request->input('branch_id', session('current_branch_id'));
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));

        $invoices = Invoice::with(['patient', 'branch'])
            ->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderByDesc('created_at')
            ->get();

        return $this->streamCsv("financial-report-{$from}-to-{$to}.csv",
            ['Invoice #', 'Date', 'Patient', 'Branch', 'Subtotal', 'Tax', 'Discount', 'Total', 'Status', 'Payment Type'],
            $invoices->map(fn($inv) => [
                $inv->invoice_number,
                $inv->created_at->format('Y-m-d'),
                $inv->patient->name ?? '-',
                $inv->branch->name ?? '-',
                number_format($inv->subtotal, 2),
                number_format($inv->tax, 2),
                number_format($inv->discount, 2),
                number_format($inv->total, 2),
                ucfirst($inv->status),
                ucfirst($inv->payment_type ?? 'cash'),
            ])->toArray()
        );
    }

    public function exportPatients(Request $request)
    {
        $branchId = $request->input('branch_id', session('current_branch_id'));

        $patients = Patient::with('branch')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderBy('name')
            ->get();

        return $this->streamCsv('patients-report.csv',
            ['Patient ID', 'Name', 'IC', 'Gender', 'DOB', 'Phone', 'Email', 'Blood Type', 'Branch', 'Status', 'Registered'],
            $patients->map(fn($p) => [
                $p->patient_id,
                $p->name,
                $p->ic_number,
                ucfirst($p->gender ?? '-'),
                $p->date_of_birth,
                $p->phone,
                $p->email ?? '-',
                $p->blood_type ?? '-',
                $p->branch->name ?? '-',
                $p->is_active ? 'Active' : 'Inactive',
                $p->created_at->format('Y-m-d'),
            ])->toArray()
        );
    }

    public function exportAppointments(Request $request)
    {
        $branchId = $request->input('branch_id', session('current_branch_id'));
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));

        $appointments = Appointment::with(['patient', 'doctor.user', 'branch'])
            ->whereBetween('appointment_date', [$from, $to])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderByDesc('appointment_date')
            ->get();

        return $this->streamCsv("appointments-report-{$from}-to-{$to}.csv",
            ['Date', 'Time', 'Patient', 'Doctor', 'Branch', 'Status', 'Reason'],
            $appointments->map(fn($a) => [
                $a->appointment_date,
                $a->start_time . ' - ' . $a->end_time,
                $a->patient->name ?? '-',
                $a->doctor->user->name ?? '-',
                $a->branch->name ?? '-',
                ucfirst($a->status),
                $a->reason ?? '-',
            ])->toArray()
        );
    }

    public function exportPharmacy(Request $request)
    {
        $branchId = $request->input('branch_id', session('current_branch_id'));

        $medicines = Medicine::with(['pharmacyCategory', 'branch'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderBy('name')
            ->get();

        return $this->streamCsv('pharmacy-report.csv',
            ['Name', 'Generic Name', 'SKU', 'Category', 'Branch', 'Stock', 'Reorder Level', 'Cost Price', 'Selling Price', 'Expiry Date', 'Status'],
            $medicines->map(fn($m) => [
                $m->name,
                $m->generic_name ?? '-',
                $m->sku ?? '-',
                $m->pharmacyCategory->name ?? '-',
                $m->branch->name ?? '-',
                $m->current_stock,
                $m->reorder_level,
                number_format($m->cost_price, 2),
                number_format($m->selling_price, 2),
                $m->expiry_date ?? '-',
                $m->current_stock <= $m->reorder_level ? 'LOW STOCK' : 'OK',
            ])->toArray()
        );
    }

    public function exportLab(Request $request)
    {
        $branchId = $request->input('branch_id', session('current_branch_id'));
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));

        $reports = LabReport::with(['patient', 'doctor.user', 'branch'])
            ->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderByDesc('created_at')
            ->get();

        return $this->streamCsv("lab-report-{$from}-to-{$to}.csv",
            ['Report #', 'Date', 'Patient', 'Doctor', 'Branch', 'Status'],
            $reports->map(fn($r) => [
                $r->report_number,
                $r->created_at->format('Y-m-d'),
                $r->patient->name ?? '-',
                $r->doctor->user->name ?? '-',
                $r->branch->name ?? '-',
                ucfirst($r->status),
            ])->toArray()
        );
    }

    private function streamCsv($filename, $headers, $rows)
    {
        return new StreamedResponse(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
