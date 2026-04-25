<?php

namespace App\Traits;

use App\Models\Notification;
use App\Models\User;
use App\Models\Medicine;

trait NotifiesUsers
{
    public static function bootNotifiesUsers()
    {
        static::created(function ($model) {
            self::fireNotification('created', $model);
        });

        static::updated(function ($model) {
            self::fireNotification('updated', $model);
        });
    }

    protected static function fireNotification($action, $model)
    {
        $class = class_basename($model);

        match ($class) {
            'Appointment' => self::notifyAppointment($action, $model),
            'Invoice' => self::notifyInvoice($action, $model),
            'InsuranceClaim' => self::notifyClaim($action, $model),
            'Prescription' => self::notifyPrescription($action, $model),
            'LabReport' => self::notifyLabReport($action, $model),
            default => null,
        };

        // Check low stock on medicine update
        if ($class === 'Medicine' && $model->current_stock <= $model->reorder_level) {
            self::notifyLowStock($model);
        }
    }

    private static function notifyAppointment($action, $model)
    {
        if ($action === 'created') {
            $patientName = $model->patient->name ?? 'Patient';
            $doctorUser = $model->doctor->user ?? null;

            // Notify the doctor
            if ($doctorUser) {
                Notification::send($doctorUser->id, 'appointment', 'New Appointment',
                    "New appointment with {$patientName} on {$model->appointment_date}",
                    ['link' => route('appointments.show', $model->id)]);
            }

            // Notify admin/receptionist
            self::notifyRoles(['admin', 'receptionist'], 'appointment', 'New Appointment Booked',
                "{$patientName} booked for {$model->appointment_date}",
                ['link' => route('appointments.show', $model->id)]);
        }

        if ($action === 'updated' && $model->isDirty('status') && $model->status === 'cancelled') {
            self::notifyRoles(['admin', 'receptionist'], 'appointment', 'Appointment Cancelled',
                "Appointment #{$model->id} has been cancelled",
                ['link' => route('appointments.show', $model->id), 'color' => 'danger']);
        }
    }

    private static function notifyInvoice($action, $model)
    {
        if ($action === 'updated' && $model->isDirty('status') && $model->status === 'paid') {
            self::notifyRoles(['admin'], 'invoice', 'Invoice Paid',
                "Invoice {$model->invoice_number} — RM " . number_format($model->total, 2) . " paid",
                ['link' => route('invoices.show', $model->id), 'color' => 'success']);
        }
    }

    private static function notifyClaim($action, $model)
    {
        if ($action === 'updated' && $model->isDirty('status')) {
            $status = ucfirst($model->status);
            self::notifyRoles(['admin'], 'insurance', "Claim {$status}",
                "Claim {$model->claim_number} is now {$status}",
                ['link' => route('insurance-claims.show', $model->id)]);
        }
    }

    private static function notifyPrescription($action, $model)
    {
        if ($action === 'updated' && $model->isDirty('status') && $model->status === 'dispensed') {
            self::notifyRoles(['admin', 'doctor'], 'pharmacy', 'Prescription Dispensed',
                "Prescription #{$model->id} has been dispensed",
                ['link' => route('prescriptions.show', $model->id)]);
        }
    }

    private static function notifyLabReport($action, $model)
    {
        if ($action === 'updated' && $model->isDirty('status') && $model->status === 'completed') {
            $doctorUser = $model->doctor->user ?? null;
            if ($doctorUser) {
                Notification::send($doctorUser->id, 'lab', 'Lab Report Ready',
                    "Lab report {$model->report_number} is completed",
                    ['link' => route('lab-reports.show', $model->id)]);
            }
        }
    }

    private static function notifyLowStock($medicine)
    {
        self::notifyRoles(['admin', 'staff'], 'pharmacy', 'Low Stock Alert',
            "{$medicine->name} — only {$medicine->current_stock} left (reorder: {$medicine->reorder_level})",
            ['link' => route('medicines.show', $medicine->id), 'color' => 'warning']);
    }

    private static function notifyRoles($roles, $type, $title, $message, $options = [])
    {
        $users = User::whereIn('role', $roles)->where('is_active', true)->get();
        foreach ($users as $user) {
            Notification::send($user->id, $type, $title, $message, $options);
        }
    }
}
