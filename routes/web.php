<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorScheduleController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LocumDoctorController;
use App\Http\Controllers\LocumSessionController;
use App\Http\Controllers\PharmacyCategoryController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\LabTestController;
use App\Http\Controllers\LabReportController;
use App\Http\Controllers\AppointmentReminderController;
use App\Http\Controllers\InsurancePanelController;
use App\Http\Controllers\PatientInsuranceController;
use App\Http\Controllers\InsuranceClaimController;
use App\Http\Controllers\PatientPortalController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalkInQueueController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\MembershipTierController;
use App\Http\Controllers\PatientMembershipController;
use App\Http\Controllers\ServicePackageController;
use App\Http\Controllers\PatientSubscriptionController;
use App\Http\Controllers\TreatmentPlanController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\RosterController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\LocumPaymentController;
use App\Http\Controllers\LocumPortalController;
use App\Http\Controllers\LocumInvitationController;
use App\Http\Controllers\BillplzController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('landing');
})->name('landing');

Route::get('/offline', fn() => view('offline'))->name('offline');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/branch/switch', [DashboardController::class, 'switchBranch'])->name('branch.switch');

    // Branches
    Route::resource('branches', BranchController::class);

    // Patients
    Route::resource('patients', PatientController::class);
    Route::post('patients/{patient}/portal-access', [PatientController::class, 'enablePortalAccess'])->name('patients.portal-access');

    // Doctors
    Route::resource('doctors', DoctorController::class);

    // Doctor Schedules
    Route::get('doctor-schedules/{doctor}', [DoctorScheduleController::class, 'index'])->name('doctor-schedules.index');
    Route::post('doctor-schedules/{doctor}', [DoctorScheduleController::class, 'store'])->name('doctor-schedules.store');
    Route::delete('doctor-schedules/{doctorSchedule}', [DoctorScheduleController::class, 'destroy'])->name('doctor-schedules.destroy');

    // Appointments
    Route::resource('appointments', AppointmentController::class);
    Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');

    // Walk-In Queue (Nombor Giliran)
    Route::get('walk-in-queue', [WalkInQueueController::class, 'index'])->name('walk-in-queue.index');
    Route::get('walk-in-queue/create', [WalkInQueueController::class, 'create'])->name('walk-in-queue.create');
    Route::post('walk-in-queue', [WalkInQueueController::class, 'store'])->name('walk-in-queue.store');
    Route::patch('walk-in-queue/{walkInQueue}/status', [WalkInQueueController::class, 'updateStatus'])->name('walk-in-queue.update-status');
    Route::post('walk-in-queue/call-next', [WalkInQueueController::class, 'callNext'])->name('walk-in-queue.call-next');
    Route::get('walk-in-queue/display', [WalkInQueueController::class, 'display'])->name('walk-in-queue.display');
    Route::get('walk-in-queue/display-data', [WalkInQueueController::class, 'displayData'])->name('walk-in-queue.display-data');
    Route::post('walk-in-queue/check-in/{appointment}', [WalkInQueueController::class, 'checkIn'])->name('walk-in-queue.check-in');
    Route::delete('walk-in-queue/{walkInQueue}', [WalkInQueueController::class, 'destroy'])->name('walk-in-queue.destroy');

    // Consultations
    Route::get('consultations', [ConsultationController::class, 'index'])->name('consultations.index');
    Route::get('consultations/create', [ConsultationController::class, 'create'])->name('consultations.create');
    Route::post('consultations/start', [ConsultationController::class, 'start'])->name('consultations.start');
    Route::get('consultations/{consultation}', [ConsultationController::class, 'show'])->name('consultations.show');
    Route::get('consultations/{consultation}/edit', [ConsultationController::class, 'edit'])->name('consultations.edit');
    Route::patch('consultations/{consultation}', [ConsultationController::class, 'update'])->name('consultations.update');
    Route::patch('consultations/{consultation}/complete', [ConsultationController::class, 'complete'])->name('consultations.complete');
    Route::get('consultations/{consultation}/mc-print', [ConsultationController::class, 'printMc'])->name('consultations.mc-print');
    Route::delete('consultations/{consultation}', [ConsultationController::class, 'destroy'])->name('consultations.destroy');

    // Services
    Route::resource('services', ServiceController::class);

    // Invoices
    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::post('invoices/{invoice}/billplz', [BillplzController::class, 'checkout'])->name('invoices.billplz');

    // Payments
    Route::post('invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

    // Locum Doctors
    Route::resource('locum-doctors', LocumDoctorController::class);

    // Locum Sessions
    Route::resource('locum-sessions', LocumSessionController::class);
    Route::patch('locum-sessions/{locumSession}/mark-paid', [LocumSessionController::class, 'markPaid'])->name('locum-sessions.mark-paid');

    // Locum Payments (batch)
    Route::resource('locum-payments', LocumPaymentController::class)->except(['edit', 'update']);
    Route::patch('locum-payments/{locumPayment}/mark-paid', [LocumPaymentController::class, 'markPaid'])->name('locum-payments.mark-paid');

    // Locum Invitations (period-bound clinical access)
    Route::resource('locum-invitations', LocumInvitationController::class)->except(['edit', 'update']);
    Route::patch('locum-invitations/{locumInvitation}/revoke', [LocumInvitationController::class, 'revoke'])->name('locum-invitations.revoke');

    // Suppliers
    Route::resource('suppliers', SupplierController::class)->except('show');

    // Purchase Orders
    Route::resource('purchase-orders', PurchaseOrderController::class)->except(['edit', 'update']);
    Route::patch('purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');

    // Stock Transfers
    Route::resource('stock-transfers', StockTransferController::class)->except(['edit', 'update']);
    Route::patch('stock-transfers/{stockTransfer}/receive', [StockTransferController::class, 'receive'])->name('stock-transfers.receive');

    // Stock Adjustments
    Route::resource('stock-adjustments', StockAdjustmentController::class)->except(['edit', 'update', 'destroy']);

    // Membership
    Route::resource('membership-tiers', MembershipTierController::class)->except('show');
    Route::resource('patient-memberships', PatientMembershipController::class)->except(['edit', 'update']);

    // Service Packages + Subscriptions
    Route::resource('service-packages', ServicePackageController::class)->except(['edit', 'update']);
    Route::resource('patient-subscriptions', PatientSubscriptionController::class)->except(['edit', 'update']);

    // Treatment Plans
    Route::get('treatment-plans/pending-approval', [TreatmentPlanController::class, 'pendingApproval'])->name('treatment-plans.pending-approval');
    Route::patch('treatment-plans/{treatmentPlan}/approve', [TreatmentPlanController::class, 'approve'])->name('treatment-plans.approve');
    Route::patch('treatment-plans/{treatmentPlan}/reject', [TreatmentPlanController::class, 'reject'])->name('treatment-plans.reject');
    Route::resource('treatment-plans', TreatmentPlanController::class)->except(['edit', 'update']);
    Route::patch('treatment-plan-sessions/{session}/complete', [TreatmentPlanController::class, 'completeSession'])->name('treatment-plan-sessions.complete');

    // Sales CRM (Leads)
    Route::resource('leads', LeadController::class)->except(['edit', 'update']);
    Route::patch('leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('leads.update-status');
    Route::post('leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');

    // Staff Roster
    Route::get('roster', [RosterController::class, 'index'])->name('roster.index');
    Route::post('roster/shifts', [RosterController::class, 'storeShift'])->name('roster.shifts.store');
    Route::delete('roster/shifts/{shift}', [RosterController::class, 'destroyShift'])->name('roster.shifts.destroy');
    Route::get('roster/leaves', [RosterController::class, 'leaves'])->name('roster.leaves');
    Route::post('roster/leaves', [RosterController::class, 'storeLeave'])->name('roster.leaves.store');
    Route::patch('roster/leaves/{leave}/approve', [RosterController::class, 'approveLeave'])->name('roster.leaves.approve');
    Route::patch('roster/leaves/{leave}/reject', [RosterController::class, 'rejectLeave'])->name('roster.leaves.reject');

    // Referrals
    Route::resource('referrals', ReferralController::class)->except(['edit', 'update']);
    Route::patch('referrals/{referral}/status', [ReferralController::class, 'updateStatus'])->name('referrals.update-status');

    // Insurance Panels
    Route::resource('insurance-panels', InsurancePanelController::class);
    Route::post('patients/{patient}/insurance', [PatientInsuranceController::class, 'store'])->name('patient-insurance.store');
    Route::put('patient-insurance/{patientInsurance}', [PatientInsuranceController::class, 'update'])->name('patient-insurance.update');
    Route::delete('patient-insurance/{patientInsurance}', [PatientInsuranceController::class, 'destroy'])->name('patient-insurance.destroy');
    Route::resource('insurance-claims', InsuranceClaimController::class)->except(['edit', 'update']);
    Route::patch('insurance-claims/{insuranceClaim}/status', [InsuranceClaimController::class, 'updateStatus'])->name('insurance-claims.update-status');

    // Pharmacy
    Route::resource('pharmacy-categories', PharmacyCategoryController::class)->except('show');
    Route::resource('medicines', MedicineController::class);
    Route::post('medicines/{medicine}/adjust-stock', [MedicineController::class, 'adjustStock'])->name('medicines.adjust-stock');

    // Prescriptions
    Route::resource('prescriptions', PrescriptionController::class)->except(['edit', 'update']);
    Route::patch('prescriptions/{prescription}/dispense', [PrescriptionController::class, 'dispense'])->name('prescriptions.dispense');

    // Lab Tests & Reports
    Route::resource('lab-tests', LabTestController::class)->except('show');
    Route::resource('lab-reports', LabReportController::class);
    Route::get('lab-reports/{labReport}/print', [LabReportController::class, 'print'])->name('lab-reports.print');

    // Appointment Reminders (WhatsApp)
    Route::get('reminders', [AppointmentReminderController::class, 'index'])->name('reminders.index');
    Route::get('reminders/create', [AppointmentReminderController::class, 'create'])->name('reminders.create');
    Route::post('reminders', [AppointmentReminderController::class, 'store'])->name('reminders.store');
    Route::post('reminders/bulk', [AppointmentReminderController::class, 'bulkCreate'])->name('reminders.bulk');
    Route::patch('reminders/{reminder}/send', [AppointmentReminderController::class, 'send'])->name('reminders.send');
    Route::delete('reminders/{reminder}', [AppointmentReminderController::class, 'destroy'])->name('reminders.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/photo', [ProfileController::class, 'removePhoto'])->name('profile.remove-photo');

    // User Management (Admin Only)
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class)->except('show');
    });

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::delete('/settings/logo', [SettingController::class, 'removeLogo'])->name('settings.remove-logo');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
    Route::get('/reports/patients', [ReportController::class, 'patients'])->name('reports.patients');
    Route::get('/reports/appointments', [ReportController::class, 'appointments'])->name('reports.appointments');
    Route::get('/reports/pharmacy', [ReportController::class, 'pharmacy'])->name('reports.pharmacy');
    Route::get('/reports/lab', [ReportController::class, 'lab'])->name('reports.lab');

    // Report Exports (CSV)
    Route::get('/reports/export/financial', [ReportController::class, 'exportFinancial'])->name('reports.export.financial');
    Route::get('/reports/export/patients', [ReportController::class, 'exportPatients'])->name('reports.export.patients');
    Route::get('/reports/export/appointments', [ReportController::class, 'exportAppointments'])->name('reports.export.appointments');
    Route::get('/reports/export/pharmacy', [ReportController::class, 'exportPharmacy'])->name('reports.export.pharmacy');
    Route::get('/reports/export/lab', [ReportController::class, 'exportLab'])->name('reports.export.lab');

    // Audit Logs (Admin Only)
    Route::middleware('admin')->group(function () {
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
    });

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::match(['get', 'post'], '/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
});

// Billplz callback (CSRF-exempt — no auth required)
Route::post('billplz/callback', [BillplzController::class, 'callback'])->name('billplz.callback');
Route::get('billplz/redirect/{invoice?}', [BillplzController::class, 'redirect'])->name('billplz.redirect');

// Locum Portal (separate auth)
Route::prefix('locum-portal')->group(function () {
    Route::get('login', [LocumPortalController::class, 'login'])->name('locum-portal.login');
    Route::post('login', [LocumPortalController::class, 'authenticate'])->name('locum-portal.authenticate');
    Route::post('logout', [LocumPortalController::class, 'logout'])->name('locum-portal.logout');

    Route::middleware('locum.auth')->group(function () {
        Route::get('/', [LocumPortalController::class, 'dashboard'])->name('locum-portal.dashboard');
        Route::get('sessions', [LocumPortalController::class, 'sessions'])->name('locum-portal.sessions');
        Route::get('payments', [LocumPortalController::class, 'payments'])->name('locum-portal.payments');
        Route::patch('invitations/{invitation}/accept', [LocumPortalController::class, 'acceptInvitation'])->name('locum-portal.invitations.accept');
        Route::patch('invitations/{invitation}/decline', [LocumPortalController::class, 'declineInvitation'])->name('locum-portal.invitations.decline');

        // Consultations (gated by active invitation)
        Route::get('consultations', [LocumPortalController::class, 'consultations'])->name('locum-portal.consultations');
        Route::post('consultations/start', [LocumPortalController::class, 'startConsultation'])->name('locum-portal.consultations.start');
        Route::get('consultations/{consultation}/edit', [LocumPortalController::class, 'editConsultation'])->name('locum-portal.consultations.edit');
        Route::patch('consultations/{consultation}', [LocumPortalController::class, 'updateConsultation'])->name('locum-portal.consultations.update');
        Route::patch('consultations/{consultation}/complete', [LocumPortalController::class, 'completeConsultation'])->name('locum-portal.consultations.complete');

        // Treatment plans (gated by active invitation)
        Route::get('treatment-plans', [LocumPortalController::class, 'treatmentPlans'])->name('locum-portal.treatment-plans');
        Route::get('treatment-plans/create', [LocumPortalController::class, 'createTreatmentPlan'])->name('locum-portal.treatment-plans.create');
        Route::post('treatment-plans', [LocumPortalController::class, 'storeTreatmentPlan'])->name('locum-portal.treatment-plans.store');
    });
});

// Patient Portal (separate auth)
Route::prefix('portal')->group(function () {
    Route::get('login', [PatientPortalController::class, 'login'])->name('portal.login');
    Route::post('login', [PatientPortalController::class, 'authenticate'])->name('portal.authenticate');
    Route::post('logout', [PatientPortalController::class, 'logout'])->name('portal.logout');

    Route::middleware(\App\Http\Middleware\PortalAuth::class)->group(function () {
        Route::get('/', [PatientPortalController::class, 'dashboard'])->name('portal.dashboard');
        Route::get('appointments', [PatientPortalController::class, 'appointments'])->name('portal.appointments');
        Route::get('invoices', [PatientPortalController::class, 'invoices'])->name('portal.invoices');
        Route::get('invoices/{id}', [PatientPortalController::class, 'invoiceShow'])->name('portal.invoices.show');
        Route::get('lab-reports', [PatientPortalController::class, 'labReports'])->name('portal.lab-reports');
        Route::get('lab-reports/{id}', [PatientPortalController::class, 'labReportShow'])->name('portal.lab-reports.show');
        Route::get('prescriptions', [PatientPortalController::class, 'prescriptions'])->name('portal.prescriptions');
        Route::get('profile', [PatientPortalController::class, 'profile'])->name('portal.profile');
        Route::patch('password', [PatientPortalController::class, 'updatePassword'])->name('portal.password');
    });
});

require __DIR__.'/auth.php';
