<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PatientPortalController extends Controller
{
    public function login()
    {
        if (session('portal_patient_id')) {
            return redirect()->route('portal.dashboard');
        }
        return view('portal.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'ic_number' => 'required|string',
            'password' => 'required|string',
        ]);

        $patient = Patient::where('ic_number', $request->ic_number)
            ->whereNotNull('password')
            ->first();

        if (!$patient || !Hash::check($request->password, $patient->password)) {
            return back()->withErrors(['ic_number' => 'Invalid IC number or password.'])->withInput();
        }

        session(['portal_patient_id' => $patient->id]);
        $patient->update(['last_portal_login' => now()]);

        return redirect()->route('portal.dashboard');
    }

    public function logout()
    {
        session()->forget('portal_patient_id');
        return redirect()->route('portal.login')->with('success', 'Logged out successfully.');
    }

    public function dashboard()
    {
        $patient = $this->getPatient();

        $upcomingAppointments = $patient->appointments()
            ->where('appointment_date', '>=', now()->toDateString())
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->with('doctor.user')
            ->orderBy('appointment_date')
            ->limit(5)
            ->get();

        $recentInvoices = $patient->invoices()
            ->with('payments')
            ->latest()
            ->limit(5)
            ->get();

        $recentLabReports = $patient->labReports()
            ->where('status', 'completed')
            ->with('doctor.user')
            ->latest('reported_at')
            ->limit(5)
            ->get();

        $recentPrescriptions = $patient->prescriptions()
            ->with(['doctor.user', 'items.medicine'])
            ->latest()
            ->limit(5)
            ->get();

        return view('portal.dashboard', compact('patient', 'upcomingAppointments', 'recentInvoices', 'recentLabReports', 'recentPrescriptions'));
    }

    public function appointments()
    {
        $patient = $this->getPatient();

        $appointments = $patient->appointments()
            ->with('doctor.user')
            ->orderBy('appointment_date', 'desc')
            ->paginate(15);

        return view('portal.appointments', compact('patient', 'appointments'));
    }

    public function invoices()
    {
        $patient = $this->getPatient();

        $invoices = $patient->invoices()
            ->with('payments')
            ->latest()
            ->paginate(15);

        return view('portal.invoices', compact('patient', 'invoices'));
    }

    public function invoiceShow($id)
    {
        $patient = $this->getPatient();
        $invoice = $patient->invoices()->with(['items.service', 'payments', 'branch'])->findOrFail($id);

        return view('portal.invoice-show', compact('patient', 'invoice'));
    }

    public function labReports()
    {
        $patient = $this->getPatient();

        $labReports = $patient->labReports()
            ->where('status', 'completed')
            ->with(['doctor.user', 'items.test'])
            ->latest('reported_at')
            ->paginate(15);

        return view('portal.lab-reports', compact('patient', 'labReports'));
    }

    public function labReportShow($id)
    {
        $patient = $this->getPatient();
        $labReport = $patient->labReports()
            ->where('status', 'completed')
            ->with(['doctor.user', 'items.test', 'branch'])
            ->findOrFail($id);

        return view('portal.lab-report-show', compact('patient', 'labReport'));
    }

    public function prescriptions()
    {
        $patient = $this->getPatient();

        $prescriptions = $patient->prescriptions()
            ->with(['doctor.user', 'items.medicine'])
            ->latest()
            ->paginate(15);

        return view('portal.prescriptions', compact('patient', 'prescriptions'));
    }

    public function profile()
    {
        $patient = $this->getPatient();
        return view('portal.profile', compact('patient'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $patient = $this->getPatient();

        if (!Hash::check($request->current_password, $patient->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $patient->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password updated successfully.');
    }

    // Admin: enable portal access for a patient
    public static function generatePortalAccess(Patient $patient, string $password)
    {
        $patient->update([
            'password' => Hash::make($password),
            'portal_token' => Str::random(64),
            'portal_token_expires_at' => now()->addYear(),
        ]);
    }

    private function getPatient(): Patient
    {
        $patientId = session('portal_patient_id');
        abort_if(!$patientId, 403, 'Unauthorized');

        return Patient::findOrFail($patientId);
    }
}
