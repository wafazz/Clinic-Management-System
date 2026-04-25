<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentReminder;
use Illuminate\Http\Request;

class AppointmentReminderController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');

        $reminders = AppointmentReminder::whereHas('appointment', fn($q) => $q->where('branch_id', $branchId))
            ->with(['appointment.patient', 'appointment.doctor.user'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('channel'), fn($q) => $q->where('channel', $request->channel))
            ->latest('scheduled_at')
            ->paginate(15)
            ->withQueryString();

        return view('reminders.index', compact('reminders'));
    }

    public function create(Request $request)
    {
        $branchId = session('current_branch_id');

        $appointments = Appointment::where('branch_id', $branchId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('appointment_date', '>=', now()->toDateString())
            ->with(['patient', 'doctor.user'])
            ->orderBy('appointment_date')
            ->get();

        $selectedAppointment = $request->filled('appointment_id') ? $request->appointment_id : null;

        return view('reminders.create', compact('appointments', 'selectedAppointment'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'channel' => 'required|in:whatsapp,sms,email',
            'phone_number' => 'required|string|max:20',
            'message' => 'required|string|max:1000',
            'scheduled_at' => 'required|date|after:now',
        ]);

        AppointmentReminder::create([
            'appointment_id' => $request->appointment_id,
            'channel' => $request->channel,
            'phone_number' => $request->phone_number,
            'message' => $request->message,
            'status' => 'pending',
            'scheduled_at' => $request->scheduled_at,
        ]);

        return redirect()->route('reminders.index')->with('success', 'Reminder scheduled successfully.');
    }

    public function send(AppointmentReminder $reminder)
    {
        if ($reminder->status === 'sent') {
            return back()->with('error', 'Reminder already sent.');
        }

        // WhatsApp API integration placeholder
        // In production, integrate with Twilio/360dialog/Fonnte API here
        $apiKey = config('services.whatsapp.api_key');
        $apiUrl = config('services.whatsapp.api_url');

        if (!$apiKey || !$apiUrl) {
            // Simulate sending for development
            $reminder->update([
                'status' => 'sent',
                'sent_at' => now(),
                'response' => 'Simulated: WhatsApp API not configured. Message would be sent to ' . $reminder->phone_number,
            ]);

            return back()->with('success', 'Reminder sent (simulated - configure WhatsApp API for real sending).');
        }

        // Real API call would go here
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => $apiKey,
            ])->post($apiUrl, [
                'phone' => $reminder->phone_number,
                'message' => $reminder->message,
            ]);

            $reminder->update([
                'status' => $response->successful() ? 'sent' : 'failed',
                'sent_at' => $response->successful() ? now() : null,
                'response' => $response->body(),
            ]);

            return back()->with($response->successful() ? 'success' : 'error',
                $response->successful() ? 'Reminder sent successfully.' : 'Failed to send reminder.');
        } catch (\Exception $e) {
            $reminder->update([
                'status' => 'failed',
                'response' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to send: ' . $e->getMessage());
        }
    }

    public function bulkCreate(Request $request)
    {
        $branchId = session('current_branch_id');

        $request->validate([
            'days_before' => 'required|integer|min:1|max:7',
            'channel' => 'required|in:whatsapp,sms,email',
            'message_template' => 'required|string|max:1000',
        ]);

        $targetDate = now()->addDays($request->days_before)->toDateString();

        $appointments = Appointment::where('branch_id', $branchId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('appointment_date', $targetDate)
            ->with('patient')
            ->get();

        $count = 0;
        foreach ($appointments as $appointment) {
            $phone = $appointment->patient->phone;
            if (!$phone) continue;

            $message = str_replace(
                ['{patient_name}', '{date}', '{time}', '{doctor}'],
                [
                    $appointment->patient->name,
                    $appointment->appointment_date->format('d M Y'),
                    $appointment->start_time,
                    $appointment->doctor?->user?->name ?? 'Doctor',
                ],
                $request->message_template
            );

            $existing = AppointmentReminder::where('appointment_id', $appointment->id)
                ->where('channel', $request->channel)
                ->where('status', 'pending')
                ->exists();

            if (!$existing) {
                AppointmentReminder::create([
                    'appointment_id' => $appointment->id,
                    'channel' => $request->channel,
                    'phone_number' => $phone,
                    'message' => $message,
                    'status' => 'pending',
                    'scheduled_at' => now(),
                ]);
                $count++;
            }
        }

        return redirect()->route('reminders.index')->with('success', "{$count} reminders created for appointments on {$targetDate}.");
    }

    public function destroy(AppointmentReminder $reminder)
    {
        if ($reminder->status === 'sent') {
            return back()->with('error', 'Cannot delete a sent reminder.');
        }

        $reminder->delete();
        return redirect()->route('reminders.index')->with('success', 'Reminder deleted.');
    }
}
