# Session Memory - Clinic Management System
> Last updated: 2026-04-25

## Session Context
- **Project**: Clinic Management System (Multi-Branch SaaS)
- **Profile**: `~/Desktop/MemoryCore Project/Projects/03-codex-lure.md`
- **Branch**: No git repo initialized yet
- **Status**: active
- **Focus**: Phase 3 + Walk-In Queue (Nombor Giliran) with appointment integration

## Current Tasks
- [x] Phase 1: Branch, Patient, Doctor, Appointment, Billing, Locum
- [x] Phase 2: Pharmacy Inventory, Lab Reports, Reminders, Patient Portal
- [x] Phase 2: Insurance Panel (panels, patient coverage, GL, claims)
- [x] Template Migration: Tailwind CSS → Star Admin Bootstrap 4
- [x] RBAC: Role-based access control (admin, doctor, staff, receptionist)
- [x] Phase 3: Audit Logs, In-App Notifications, Report CSV Exports
- [x] Walk-In Queue (Nombor Giliran): queue number, status management, call next, TV display screen, appointment check-in integration
- [x] Consultation Module: vitals, clinical notes, diagnosis, treatment, MC, follow-up — bridges Queue/Appointment to Invoice

## Working Memory
### Active Context
- **Root**: `/Users/wafazztechnology/Desktop/Codex Lure/project/Clinic Management System plus Branches/clinic-app`
- **Stack**: Laravel 12, PHP Blade, Star Admin Bootstrap 4, Alpine.js, MySQL (MariaDB port 3307)
- **DB**: `clinic_management`, root, no password
- **Admin Login**: admin@clinic.com / password
- **Patient Portal Login**: IC: 900101-01-1234 / password: patient123 (at /portal/login)
- **Template**: Star Admin Free — assets in `public/star-admin/`, no Vite/Tailwind needed
- **37 migrations**, **29 models**, **23 controllers**, **95+ Blade views**
- Alpine.js preserved for dynamic forms (invoice items, prescriptions, claims)

### Decisions Made
- Star Admin over Tailwind CSS — full migration, all views converted
- Portal has its own layout (`portal/layout.blade.php`) with Star Admin CSS/JS + Bootstrap navbar
- Audit Logs: admin-only, Auditable trait on 14 models, tracks create/update/delete with old/new values
- Notifications: in-app system with bell icon in navbar, NotifiesUsers trait on 6 key models
- Report exports: StreamedResponse CSV downloads for all 5 report types

### Blockers / Open Questions
- WhatsApp API not yet configured (simulated mode)
- No git repo initialized
- Breeze component files still exist in `resources/views/components/` (unused, can be deleted)

## Session Recap
> This section survives resets. Keep it under 30 lines.

### What Was Done
- **Phase 1**: Branch, Patient, Doctor/Schedule, Appointment, Billing, Locum Doctor
- **Phase 2**: Pharmacy, Lab Reports, Reminders, Patient Portal, Insurance Panel
- **Star Admin Migration**: Complete rewrite of ALL views from Tailwind CSS to Bootstrap 4
- **RBAC**: 4 roles (admin, doctor, staff, receptionist), user management, inactive blocking
- **Phase 3 — Audit Logs**:
  - `audit_logs` table: user_id, branch_id, action, model_type/id, old/new values, IP, user agent
  - `Auditable` trait added to 14 models (Patient, Appointment, Invoice, Doctor, Branch, Service, Medicine, Prescription, LabReport, InsurancePanel, InsuranceClaim, LocumDoctor, LocumSession, User)
  - AuditLogController with index (filterable) + show (diff view)
  - Admin-only access via middleware
- **Phase 3 — Notifications**:
  - `notifications` table: user_id, type, title, message, icon, color, link, read_at
  - Notification model with send/sendToRole/sendToAll helpers
  - `NotifiesUsers` trait on 6 models: fires on new appointment, invoice paid, claim status change, prescription dispensed, lab report complete, low stock
  - Bell icon in navbar with dropdown (5 recent) + unread count badge
  - Full notifications index page with filter by type/status
- **Phase 3 — Report Exports**:
  - CSV export for all 5 reports (financial, patients, appointments, pharmacy, lab)
  - Export buttons added to each report view header

### Where We Left Off
- All 3 phases complete + Walk-In Queue (Nombor Giliran) + Consultation Module
- Ready for testing and polish

### Key Context for Next Session
- Template: Star Admin Bootstrap 4 (no Vite/Tailwind)
- Roles: admin, doctor, staff, receptionist — admin manages users + audit logs
- Audit trail on 14 models, notifications on 6 key models
- Patient portal at /portal/login (IC: 900101-01-1234, pw: patient123)
- Nombor Giliran: W-prefix walk-in, A-prefix appointment check-in, daily reset per branch
- Queue display screen: /walk-in-queue/display (for TV/tablet in waiting area)
- **Consultation flow**: Queue (serving) / Appointment (confirmed/in_progress) → "Start Consultation" → vitals + diagnosis + Rx/Lab + MC → "Complete & Bill" → auto-redirect to invoice with consultation fee pre-filled
- **Consultation number format**: `CN-{branchCode}-{YYYYMMDD}-{0001}` (daily reset per branch)
- **MC printable** at `/consultations/{id}/mc-print` (DomPDF-style HTML, native print)
- Schema: `consultations` table + `consultation_id` FK on prescriptions, lab_reports, invoices
