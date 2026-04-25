<?php

namespace App\Http\Controllers;

use App\Models\LocumDoctor;
use App\Models\LocumSession;
use App\Models\LocumPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LocumPortalController extends Controller
{
    public function login()
    {
        return view('locum-portal.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'ic_number' => 'required|string',
            'password' => 'required|string',
        ]);

        $locum = LocumDoctor::where('ic_number', $request->ic_number)->first();

        if (!$locum || !$locum->password || !Hash::check($request->password, $locum->password)) {
            throw ValidationException::withMessages(['ic_number' => 'Invalid IC number or password.']);
        }

        if (!$locum->is_active) {
            throw ValidationException::withMessages(['ic_number' => 'Account is inactive.']);
        }

        session(['locum_id' => $locum->id]);
        $locum->update(['last_login_at' => now()]);

        return redirect()->route('locum-portal.dashboard');
    }

    public function logout()
    {
        session()->forget('locum_id');
        return redirect()->route('locum-portal.login')->with('success', 'Logged out.');
    }

    public function dashboard()
    {
        $locum = LocumDoctor::find(session('locum_id'));
        if (!$locum) return redirect()->route('locum-portal.login');

        $totalSessions = LocumSession::where('locum_doctor_id', $locum->id)->count();
        $sessionsThisMonth = LocumSession::where('locum_doctor_id', $locum->id)
            ->whereMonth('session_date', now()->month)
            ->whereYear('session_date', now()->year)->count();
        $unpaidSessions = LocumSession::where('locum_doctor_id', $locum->id)
            ->where('is_paid', false)->count();
        $unpaidAmount = LocumSession::where('locum_doctor_id', $locum->id)
            ->where('is_paid', false)->sum('total_pay');
        $paidThisMonth = LocumSession::where('locum_doctor_id', $locum->id)
            ->where('is_paid', true)
            ->whereMonth('session_date', now()->month)
            ->whereYear('session_date', now()->year)
            ->sum('total_pay');

        $upcomingSessions = LocumSession::where('locum_doctor_id', $locum->id)
            ->where('session_date', '>=', now())
            ->orderBy('session_date')
            ->limit(5)->get();

        $recentSessions = LocumSession::where('locum_doctor_id', $locum->id)
            ->orderBy('session_date', 'desc')->limit(10)->get();

        $payments = LocumPayment::where('locum_doctor_id', $locum->id)
            ->orderBy('created_at', 'desc')->limit(5)->get();

        return view('locum-portal.dashboard', compact(
            'locum', 'totalSessions', 'sessionsThisMonth', 'unpaidSessions', 'unpaidAmount',
            'paidThisMonth', 'upcomingSessions', 'recentSessions', 'payments'
        ));
    }

    public function sessions()
    {
        $locum = LocumDoctor::find(session('locum_id'));
        if (!$locum) return redirect()->route('locum-portal.login');

        $sessions = LocumSession::where('locum_doctor_id', $locum->id)
            ->orderBy('session_date', 'desc')->paginate(15);

        return view('locum-portal.sessions', compact('locum', 'sessions'));
    }

    public function payments()
    {
        $locum = LocumDoctor::find(session('locum_id'));
        if (!$locum) return redirect()->route('locum-portal.login');

        $payments = LocumPayment::where('locum_doctor_id', $locum->id)
            ->with('items.locumSession')
            ->orderBy('created_at', 'desc')->paginate(15);

        return view('locum-portal.payments', compact('locum', 'payments'));
    }
}
