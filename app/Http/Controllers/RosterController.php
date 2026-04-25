<?php

namespace App\Http\Controllers;

use App\Models\StaffShift;
use App\Models\LeaveRequest;
use App\Models\ShiftSwap;
use App\Models\User;
use Illuminate\Http\Request;

class RosterController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');
        $weekStart = $request->week ? \Carbon\Carbon::parse($request->week)->startOfWeek() : now()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $shifts = StaffShift::with('user')
            ->where('branch_id', $branchId)
            ->whereBetween('shift_date', [$weekStart, $weekEnd])
            ->orderBy('shift_date')->orderBy('start_time')->get();

        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('roster.index', compact('shifts', 'users', 'weekStart', 'weekEnd'));
    }

    public function storeShift(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'shift_type' => 'required|in:morning,afternoon,night,full,custom',
            'notes' => 'nullable|string',
        ]);
        $validated['branch_id'] = session('current_branch_id');
        $validated['created_by'] = auth()->id();
        StaffShift::create($validated);
        return back()->with('success', 'Shift created.');
    }

    public function destroyShift(StaffShift $shift)
    {
        $shift->delete();
        return back()->with('success', 'Shift deleted.');
    }

    public function leaves(Request $request)
    {
        $leaves = LeaveRequest::with('user', 'approver')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()->paginate(15)->withQueryString();
        return view('roster.leaves', compact('leaves'));
    }

    public function storeLeave(Request $request)
    {
        $validated = $request->validate([
            'leave_type' => 'required|in:annual,sick,emergency,unpaid,replacement,other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);
        $validated['user_id'] = auth()->id();
        $validated['days'] = \Carbon\Carbon::parse($validated['start_date'])->diffInDays(\Carbon\Carbon::parse($validated['end_date'])) + 1;
        $validated['status'] = 'pending';
        LeaveRequest::create($validated);
        return redirect()->route('roster.leaves')->with('success', 'Leave request submitted.');
    }

    public function approveLeave(LeaveRequest $leave)
    {
        $leave->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'Leave approved.');
    }

    public function rejectLeave(LeaveRequest $leave)
    {
        $leave->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'Leave rejected.');
    }
}
