# Clinic Management System (Multi-Branch)

A full-featured clinic management system for multi-branch clinics in Malaysia. Covers the complete patient journey from lead capture through registration, queue, consultation, prescription, lab, billing, insurance, membership, and treatment plans — plus full pharmacy inventory chain, staff roster, and locum batch payments.

Built with **Laravel 12 + PHP Blade + Star Admin Bootstrap 4 + Alpine.js**.

---

## Features

### 1. Patient Flow
- **Patients** — registration, IC-based identification, allergy tracking, portal access
- **Doctors & Schedules** — recurring schedule grid (per branch), consultation fees, MMC/APC numbers, schedule overrides for date-specific changes
- **Appointments** — booking, status workflow (pending → confirmed → in_progress → completed)
- **Walk-In Queue (Nombor Giliran)** — daily queue with W/A prefix, call-next, TV display screen, appointment check-in integration
- **Consultations** — vitals (BP, pulse, temp, weight, height, BMI auto-calc, SpO2), clinical notes (chief complaint, history, examination, diagnosis, treatment plan, follow-up), Medical Certificate (printable), linked prescriptions / lab orders
- **Treatment Plans** — multi-session plans with templates, auto-generated session schedule, mark-complete flow, progress tracking
- **Referrals** — refer to external specialists with urgency (routine/urgent/emergency) and status tracking

### 2. Sales & Membership
- **Sales CRM (Leads)** — 15-status follow-up workflow (new → contacted → followup_1..5 → appointment_booked → success/reject/kiv/no_answer/wrong_number/duplicate), assignment, next-followup scheduling, convert-to-patient
- **Membership Tiers** — discount %, free consultations/lab per year, family member sharing, priority queue, billing cycle (free/monthly/yearly)
- **Patient Memberships** — assign tier to patient, track savings, free-visit usage, family members, usage logs
- **Service Packages** — one-time / subscription / bundle types with line items (consultation/lab/medicine/service)
- **Patient Subscriptions** — full or partial payment, auto per-session calculation, payment tracking, usage tracking, visit caps

### 3. Clinical
- **Pharmacy** — categories, medicines, stock movement tracking, dispensing with auto-deduction
- **Suppliers** — supplier directory with contact and registration info
- **Purchase Orders** — order from suppliers with batch + expiry tracking, auto-receive into stock on confirmation
- **Stock Transfers** — inter-branch transfers with line items, request/approve/receive workflow, source-deduct + destination-add automation
- **Stock Adjustments** — adjustment in/out, expired, damaged with reason and per-item notes
- **Prescriptions** — multi-item with dosage/frequency/duration/instructions, draft → dispensed flow
- **Lab Tests & Reports** — test catalog, multi-item lab reports with results, printable

### 4. Billing
- **Invoices** — multi-item, tax/discount, auto-generated invoice numbers per branch, links to consultation for fee pre-fill
- **Payments** — multiple payments per invoice, paid/partial/issued status
- **Insurance Panels** — companies, credit terms, consultation/annual limits
- **Patient Insurance** — coverage records, GL workflow
- **Insurance Claims** — claim submission, status tracking (submitted → approved/rejected → paid), annual limit auto-deduction on payment

### 5. Locum Operations
- **Locum Doctors** — locum directory with rates
- **Locum Sessions** — session-based engagements with payment tracking
- **Locum Batch Payments** — period-based payment runs (gross / deductions / net), session bundling, approval workflow, mark-paid

### 6. Staff Operations
- **Staff Roster** — weekly grid view by user, add/delete shifts (morning/afternoon/night/full/custom)
- **Leave Requests** — submit + approve/reject (annual/sick/emergency/unpaid/replacement/other)
- **Shift Swaps** — swap-request workflow with approval

### 7. System
- **Multi-Branch** — branch switcher, all data scoped to current branch
- **RBAC** — 9 roles (admin, doctor, staff, receptionist, super_admin, nurse, pharmacist, sales_team, locum_doctor), role-based menu visibility
- **Audit Logs** — admin-only, tracks create/update/delete on 14+ models with old/new values diff
- **In-App Notifications** — bell icon dropdown + full index, fires on appointments / payments / claims / dispensing / lab / low stock
- **Patient Portal** — separate auth, view appointments / invoices / lab reports / prescriptions, profile + password
- **Reports** — 5 categories (Financial, Patients, Appointments, Pharmacy, Lab) with CSV export
- **Settings** — clinic info, logo upload, system preferences
- **WhatsApp Reminders** — appointment reminders (simulated mode, ready for API integration)

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 12 |
| PHP | 8.3+ (tested on 8.4) |
| Frontend | Blade + Alpine.js |
| UI Template | Star Admin Free (Bootstrap 4) |
| Database | MySQL / MariaDB |
| PDF | barryvdh/laravel-dompdf ^3.1 |
| Auth | Laravel Breeze (web) + custom PortalAuth middleware (patient portal) |
| Build | Vite (only used for legacy Tailwind components — Star Admin assets are static) |

---

## Roles

| Role | Access |
|---|---|
| **super_admin** | Highest authority, manages everything including admins |
| **admin** | Full access + user management + audit logs |
| **doctor** | Consultations, patients, appointments, prescriptions, lab, treatment plans, referrals |
| **locum_doctor** | Locum sessions, consultations during shifts |
| **nurse** | Patient vitals, queue assistance, consultation support |
| **pharmacist** | Medicines, stock, prescriptions, dispensing, purchase orders, transfers |
| **receptionist** | Patients, appointments, queue, billing, leads |
| **sales_team** | Sales CRM (Leads), follow-ups, conversion tracking |
| **staff** | General clinic operations |
| **patient** | Patient Portal only (separate login at `/portal/login`) |

---

## Requirements

- PHP 8.3 or higher
- Composer 2.x
- Node.js 18+ (only needed if rebuilding Vite assets)
- MySQL 5.7+ or MariaDB 10.4+
- Web server (Apache / Nginx) or `php artisan serve` for local dev

---

## Installation

```bash
# 1. Clone the project
git clone https://github.com/wafazz/Clinic-Management-System.git
cd Clinic-Management-System

# 2. Install PHP dependencies
composer install

# 3. Environment file
cp .env.example .env
php artisan key:generate

# 4. Configure database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3307           # XAMPP / MAMP often uses 3307
# DB_DATABASE=clinic_management
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Create the database
mysql -u root -e "CREATE DATABASE clinic_management;"

# 6. Run migrations + seeders
php artisan migrate --seed

# 7. Storage symlink (for logo / profile photo uploads)
php artisan storage:link

# 8. Start dev server
php artisan serve
```

Open <http://127.0.0.1:8000>.

---

## Default Login Credentials

| Role | Email | Password |
|---|---|---|
| Admin | `admin@clinic.com` | `password` |
| Patient Portal | IC `900101-01-1234` | `patient123` |

> Patient Portal is at <http://127.0.0.1:8000/portal/login> (separate URL).

---

## Project Structure

```
clinic-app/
├── app/
│   ├── Http/Controllers/        # 36+ controllers
│   ├── Models/                  # 56 Eloquent models
│   ├── Http/Middleware/         # PortalAuth, AdminOnly, etc.
│   └── Traits/                  # Auditable, NotifiesUsers
├── database/
│   ├── migrations/              # 46 migrations
│   └── seeders/                 # DatabaseSeeder + module seeders
├── resources/views/             # Blade views (organized by feature)
│   ├── consultations/           # Vitals + clinical form + MC print
│   ├── walk-in-queue/           # Queue + TV display
│   ├── treatment-plans/         # Multi-session plans
│   ├── referrals/
│   ├── leads/                   # Sales CRM
│   ├── membership-tiers/
│   ├── patient-memberships/
│   ├── service-packages/
│   ├── patient-subscriptions/
│   ├── suppliers/
│   ├── purchase-orders/
│   ├── stock-transfers/
│   ├── stock-adjustments/
│   ├── locum-payments/
│   ├── roster/                  # Weekly grid + leaves
│   ├── prescriptions/
│   ├── lab/
│   ├── invoices/
│   ├── insurance/
│   ├── portal/                  # Patient portal
│   └── layouts/                 # app.blade.php, sidebar.blade.php
├── public/star-admin/           # Star Admin static assets (CSS/JS/fonts)
├── routes/
│   ├── web.php                  # Main routes (200+ routes)
│   └── auth.php                 # Breeze auth routes
└── storage/                     # logs, uploads, sessions
```

---

## End-to-End Patient Flow Diagram

```
                          PATIENT JOURNEY — FROM LEAD TO COMPLETED VISIT
                          ════════════════════════════════════════════════

  ┌──────────────────────────────────────────────────────────────────────────────┐
  │  PHASE 1 · LEAD CAPTURE (optional)                       [ Sales Team ]      │
  └──────────────────────────────────────────────────────────────────────────────┘
                                       │
                  Lead from Facebook / Walk-in / Phone / Referral
                                       │
                                       ▼
                       ┌────────────────────────────┐
                       │  /leads/create             │  ◄── Sales Team
                       │  Capture name, phone,      │
                       │  source, service interest  │
                       └─────────────┬──────────────┘
                                     │
                                     ▼
                       ┌────────────────────────────┐
                       │  Follow-up Workflow        │
                       │  new → contacted →         │  ◄── Sales Team
                       │  followup_1..5 →           │       (15 status options,
                       │  appointment_booked        │        next_followup_at scheduled)
                       └─────────────┬──────────────┘
                                     │
                          status = success / appointment_booked
                                     │
                                     ▼
                       ┌────────────────────────────┐
                       │  Convert to Patient        │  ◄── Sales Team
                       │  /leads/{id}/convert       │       (auto-creates patient record)
                       └─────────────┬──────────────┘
                                     │
                                     ▼
  ┌──────────────────────────────────────────────────────────────────────────────┐
  │  PHASE 2 · ARRIVAL                                       [ Receptionist ]    │
  └──────────────────────────────────────────────────────────────────────────────┘
                                     │
                ┌────────────────────┼────────────────────┐
                │                                         │
                ▼                                         ▼
       ┌────────────────┐                      ┌────────────────────┐
       │  WALK-IN       │                      │  APPOINTMENT       │
       │  /walk-in-     │  ◄── Receptionist   │  /appointments/    │ ◄── Receptionist
       │  queue/create  │                      │  {id} → Check In   │     (or patient
       │  → W001        │                      │  → A001            │      booked online)
       └───────┬────────┘                      └─────────┬──────────┘
               │                                         │
               └────────────────────┬────────────────────┘
                                    │
                                    ▼
                    ┌──────────────────────────────┐
                    │  Queue Display (TV)          │  ◄── Patient sees number
                    │  /walk-in-queue/display      │       (priority members shown
                    └──────────────┬───────────────┘        with red star, called first)
                                   │
                                   ▼
                    ┌──────────────────────────────┐
                    │  Call Next                   │  ◄── Receptionist or Doctor
                    │  status: waiting → serving   │       (calls via dashboard)
                    └──────────────┬───────────────┘
                                   │
                                   ▼
  ┌──────────────────────────────────────────────────────────────────────────────┐
  │  PHASE 3 · CLINICAL ENCOUNTER                            [ Nurse + Doctor ]  │
  └──────────────────────────────────────────────────────────────────────────────┘
                                   │
                                   ▼
                    ┌──────────────────────────────┐
                    │  Vitals Capture (optional)   │  ◄── Nurse
                    │  BP, pulse, temp, weight,    │       (records on consultation
                    │  height (BMI auto), SpO2     │        edit page)
                    └──────────────┬───────────────┘
                                   │
                                   ▼
                    ┌──────────────────────────────┐
                    │  Start Consultation          │  ◄── Doctor (or Locum Doctor)
                    │  /consultations/start        │       Creates CN-{branch}-
                    │  Number: CN-HQ-...-0001      │       {YYYYMMDD}-{seq}
                    └──────────────┬───────────────┘
                                   │
                                   ▼
                    ┌──────────────────────────────┐
                    │  Clinical Notes              │  ◄── Doctor
                    │  • Chief complaint           │
                    │  • History                   │
                    │  • Examination findings      │
                    │  • Diagnosis (primary/2nd)   │
                    │  • Treatment plan            │
                    │  • Follow-up date            │
                    └──────────────┬───────────────┘
                                   │
       ┌───────────────────────────┼───────────────────────────────┐
       │                           │                                │
       ▼                           ▼                                ▼
 ┌──────────────┐         ┌─────────────────┐          ┌─────────────────────┐
 │ Prescription │         │ Lab Order       │          │ Medical Certificate │
 │ Multi-item   │ ◄ Doctor│ Multi-test      │ ◄ Doctor │ MC days, from-to    │ ◄ Doctor
 │ Rx form      │         │ /lab-reports/   │          │ Auto in consultation│
 │              │         │ create          │          │ (printable PDF)     │
 └──────┬───────┘         └────────┬────────┘          └──────────┬──────────┘
        │                          │                              │
        │                          ▼                              │
        │             ┌─────────────────────┐                     │
        │             │ Lab Tech runs tests │  ◄── Lab Tech /     │
        │             │ Records results     │      Pharmacist     │
        │             │ Marks complete      │                     │
        │             └──────────┬──────────┘                     │
        │                        │                                │
        │                        ▼                                │
        │             ┌─────────────────────┐                     │
        │             │ Lab Report ready    │  ◄── Doctor reviews │
        │             │ Patient/Doctor view │                     │
        │             └─────────────────────┘                     │
        │                                                         │
        │              Optional branches from consultation        │
        │  ┌──────────────────┬────────────────────┬───────────┐  │
        │  ▼                  ▼                    ▼           │  │
   ┌────────────────┐  ┌──────────────┐  ┌────────────────┐    │  │
   │ Treatment Plan │  │ Referral to  │  │ Subscription   │    │  │
   │ Multi-session  │  │ Specialist   │  │ Usage logged   │    │  │
   │ auto-generated │◄ │ /referrals/  │◄ │ (if patient    │ ◄ Doctor / Receptionist
   │ schedule       │D │ create       │D │  has package)  │    │  │
   └────────────────┘  └──────────────┘  └────────────────┘    │  │
        │                                                       │  │
        └──────────────────────────────┬────────────────────────┘  │
                                       │                           │
                                       ▼                           │
                       ┌────────────────────────────┐              │
                       │  Complete Consultation     │ ◄── Doctor   │
                       │  status → completed        │              │
                       └─────────────┬──────────────┘              │
                                     │                             │
                                     ▼                             │
  ┌──────────────────────────────────────────────────────────────────────────────┐
  │  PHASE 4 · DISPENSING & BILLING                  [ Pharmacist + Cashier ]    │
  └──────────────────────────────────────────────────────────────────────────────┘
                                     │
            ┌────────────────────────┼────────────────────────┐
            │                                                 │
            ▼                                                 ▼
  ┌────────────────────┐                          ┌────────────────────────┐
  │  DISPENSING        │                          │  INVOICE CREATION      │
  │  /prescriptions/   │  ◄── Pharmacist          │  /invoices/create?     │ ◄── Cashier /
  │  {id}/dispense     │      (deducts stock      │  consultation_id={id}  │     Receptionist
  │  draft → dispensed │       from medicine)     │  Auto pre-fills:       │
  └────────┬───────────┘                          │   • Consultation fee   │
           │                                      │   • Dispensed Rx items │
           ▼                                      │   • Lab tests          │
  ┌────────────────────┐                          │  Membership banner +   │
  │  Stock Movement    │  ◄── System auto-logs    │  auto-discount toggle  │
  │  Auto-deduct       │      (audit trail)       └──────────┬─────────────┘
  └────────────────────┘                                     │
                                                             ▼
                                                   ┌────────────────────┐
                                                   │  Patient Receives  │ ◄── Cashier
                                                   │  Invoice           │     hands over
                                                   │  HQ-INV-202604-... │
                                                   └──────────┬─────────┘
                                                              │
                                                ┌─────────────┴─────────────┐
                                                │                           │
                                                ▼                           ▼
                                       ┌────────────────┐         ┌─────────────────┐
                                       │ CASH PAYMENT   │         │ INSURANCE PANEL │
                                       │ Cash/Card/     │ ◄ Cashier│ Submit claim   │ ◄ Cashier /
                                       │ Online         │         │ to panel        │   Admin
                                       │ /payments      │         │ /insurance-     │
                                       │                │         │ claims/create   │
                                       └────────┬───────┘         └────────┬────────┘
                                                │                          │
                                                │                          ▼
                                                │              ┌─────────────────────┐
                                                │              │ Claim Workflow      │
                                                │              │ submitted →         │ ◄ Admin
                                                │              │ approved/rejected → │
                                                │              │ paid (annual limit  │
                                                │              │ auto-deducted)      │
                                                │              └──────────┬──────────┘
                                                │                         │
                                                └─────────────┬───────────┘
                                                              │
                                                              ▼
                                                   ┌────────────────────┐
                                                   │  Invoice Status    │
                                                   │  → paid / partial  │
                                                   └──────────┬─────────┘
                                                              │
                                                              ▼
  ┌──────────────────────────────────────────────────────────────────────────────┐
  │  PHASE 5 · CLOSE-OUT                                                         │
  └──────────────────────────────────────────────────────────────────────────────┘
                                                              │
                                                              ▼
                                                   ┌────────────────────┐
                                                   │  Queue → completed │ ◄── Receptionist
                                                   │  Patient leaves    │
                                                   └──────────┬─────────┘
                                                              │
                                                              ▼
                                                   ┌────────────────────┐
                                                   │  Patient Portal    │ ◄── Patient
                                                   │  /portal — view    │
                                                   │  invoice, lab,     │
                                                   │  prescriptions     │
                                                   └──────────┬─────────┘
                                                              │
                                                              ▼
                                                   ┌────────────────────┐
                                                   │  Notifications     │ ◄── System
                                                   │  fired to user     │     (auto)
                                                   │  (in-app bell)     │
                                                   └──────────┬─────────┘
                                                              │
                                                              ▼
                                                   ┌────────────────────┐
                                                   │  Audit Log entry   │ ◄── System
                                                   │  (auto, admin-only │     (Auditable
                                                   │   visibility)      │      trait)
                                                   └────────────────────┘


  ┌──────────────────────────────────────────────────────────────────────────────┐
  │  BACK-OFFICE (RUNS ASYNC TO PATIENT VISITS)                                  │
  ├──────────────────────────────────────────────────────────────────────────────┤
  │                                                                              │
  │  • Pharmacy reorder         /purchase-orders/create        [ Pharmacist ]    │
  │  • Stock transfers          /stock-transfers/create        [ Pharmacist ]    │
  │  • Stock adjustments        /stock-adjustments/create      [ Pharmacist ]    │
  │  • Locum batch payments     /locum-payments/create         [ Admin ]         │
  │  • Staff roster             /roster                        [ Admin ]         │
  │  • Leave approvals          /roster/leaves                 [ Admin ]         │
  │  • Reports + exports        /reports                       [ Admin ]         │
  │  • Audit review             /audit-logs                    [ Admin ]         │
  │                                                                              │
  └──────────────────────────────────────────────────────────────────────────────┘
```

### Responsibility Matrix (per phase)

| Phase | Action | Person Responsible | Module |
|---|---|---|---|
| 1. Lead Capture | Capture lead from source | **Sales Team** | `/leads` |
| 1. Lead Follow-up | Update status, schedule next contact | **Sales Team** | `/leads/{id}` |
| 1. Convert | Convert lead to patient | **Sales Team / Receptionist** | `/leads/{id}/convert` |
| 2. Walk-in Reg | Register patient + queue number | **Receptionist** | `/walk-in-queue/create` |
| 2. Appointment Reg | Book / Check-in | **Receptionist** | `/appointments`, `/walk-in-queue/check-in` |
| 2. Call Next | Move queue from waiting → serving | **Receptionist / Doctor** | `/walk-in-queue/call-next` |
| 3. Vitals | BP, pulse, temp, BMI | **Nurse** | `/consultations/{id}/edit` |
| 3. Consultation | Diagnosis, treatment plan, MC | **Doctor / Locum Doctor** | `/consultations/{id}/edit` |
| 3. Prescription | Issue Rx | **Doctor** | `/prescriptions/create` |
| 3. Lab Order | Order tests | **Doctor** | `/lab-reports/create` |
| 3. Lab Run | Run tests, record results | **Lab Tech / Pharmacist** | `/lab-reports/{id}/edit` |
| 3. MC Issue | Issue + print medical certificate | **Doctor** | `/consultations/{id}/mc-print` |
| 3. Treatment Plan | Multi-session plan | **Doctor** | `/treatment-plans/create` |
| 3. Referral | Refer to specialist | **Doctor** | `/referrals/create` |
| 4. Dispense | Dispense Rx, deduct stock | **Pharmacist** | `/prescriptions/{id}/dispense` |
| 4. Invoice | Create invoice | **Cashier / Receptionist** | `/invoices/create` |
| 4. Cash Payment | Record cash/card | **Cashier** | `/invoices/{id}/payments` |
| 4. Insurance Claim | Submit claim to panel | **Cashier / Admin** | `/insurance-claims/create` |
| 4. Claim Workflow | Approve/reject/paid | **Admin** | `/insurance-claims/{id}/status` |
| 5. Close Queue | Mark queue as completed | **Receptionist** | `/walk-in-queue/{id}/status` |
| 5. Patient Portal | Self-service view | **Patient** | `/portal` |
| 5. Notifications | Auto-fired | **System** | bell icon |
| 5. Audit Log | Auto-recorded | **System** | `/audit-logs` (Admin views) |
| Back-office | PO / Transfers / Adjustments | **Pharmacist** | `/purchase-orders`, `/stock-*` |
| Back-office | Locum payments | **Admin** | `/locum-payments` |
| Back-office | Roster + leave | **Admin** | `/roster`, `/roster/leaves` |
| Back-office | Reports | **Admin** | `/reports` |

---

## Key Workflows

### 1. Lead → Patient → Consultation → Bill
1. Sales team logs lead at `/leads/create` (source, interest, assignee).
2. Follow up via 15-status workflow (`/leads/{id}` → update status with notes + next-followup datetime).
3. On success → click **Convert** → patient record auto-created.
4. Patient walks in → register or look up.
5. Add to queue (`W001`) or check in via appointment (`A001`).
6. Doctor calls patient → **Start Consultation** → vitals + diagnosis + Rx/Lab + MC.
7. **Complete & Bill** → auto-redirect to invoice with consultation fee pre-filled.

### 2. Membership-Backed Visit
1. Patient enrolled in tier (`/patient-memberships/create`).
2. Tier benefits auto-applied at billing: free visit allowance, % discount on consultation/medicine/lab, priority queue.
3. Family members benefit from same membership (within tier limit).
4. Usage tracked in `membership_usage_logs` for audit and savings reporting.

### 3. Treatment Plan
1. Doctor creates plan post-consultation (`/treatment-plans/create`) — 6 sessions × 7-day interval auto-generates session schedule.
2. Each session links to an appointment.
3. Doctor marks each session complete on the show page.
4. Plan auto-marks `completed` when all sessions done.

### 4. Pharmacy Inventory Chain
1. Add suppliers (`/suppliers`).
2. Create PO (`/purchase-orders/create`) → batch + expiry per item.
3. Receive PO → auto-update medicine stock + log stock movement.
4. Transfer stock between branches (`/stock-transfers/create`).
5. Adjust for expired/damaged (`/stock-adjustments/create`).

### 5. Insurance Panel Patient
1. Coverage recorded in `patient_insurance`.
2. Invoice marked `payment_type = panel` → linked to panel + patient_insurance.
3. Submit `insurance_claim` → workflow: submitted → approved (or rejected) → paid.
4. Annual limit auto-deducted on insurer payment.

### 6. Locum Batch Payment
1. Locum doctor completes multiple sessions across the period.
2. Admin opens `/locum-payments/create?locum_doctor_id={id}` → unpaid sessions listed.
3. Select sessions, set deductions, submit → calculates gross, deductions, net.
4. Mark paid → updates session payment status.

---

## Database Schema (Highlights)

**Core**: `branches`, `users`, `patients`, `doctors`, `doctor_schedules`, `schedule_overrides`

**Operations**: `appointments`, `walk_in_queues`, `consultations`, `services`, `treatment_plans` + `treatment_plan_sessions` + `treatment_plan_templates`, `referrals`

**Sales & Membership**: `leads`, `membership_tiers`, `patient_memberships`, `family_members`, `membership_usage_logs`, `service_packages` + `package_items`, `patient_subscriptions` + `subscription_payments` + `subscription_usages`

**Clinical**: `prescriptions` + `prescription_items`, `medicines` + `pharmacy_categories` + `stock_movements`, `lab_tests`, `lab_reports` + `lab_report_items`

**Pharmacy Inventory**: `suppliers`, `purchase_orders` + `purchase_order_items`, `stock_transfers` + `stock_transfer_items`, `stock_adjustments` + `stock_adjustment_items`

**Billing**: `invoices` + `invoice_items`, `payments`

**Insurance**: `insurance_panels`, `patient_insurances`, `insurance_claims`

**Locum**: `locum_doctors`, `locum_sessions`, `locum_payments` + `locum_payment_items`

**Roster**: `staff_shifts`, `leave_requests`, `shift_swaps`

**System**: `audit_logs`, `notifications`, `appointment_reminders`, `settings`

> The `consultation_id` FK links `consultations` to `prescriptions`, `lab_reports`, and `invoices` — providing a single thread for the patient encounter.

---

## Important Routes

| URL | Purpose |
|---|---|
| `/dashboard` | Branch overview, today's stats |
| `/leads` | Sales CRM with status filter + stats |
| `/walk-in-queue` | Today's queue with call-next |
| `/walk-in-queue/display` | TV display screen for waiting area |
| `/consultations` | All consultations (filterable) |
| `/treatment-plans` | Treatment plans with progress tracking |
| `/referrals` | External referrals |
| `/membership-tiers` | Tier configuration |
| `/patient-memberships` | Active memberships |
| `/service-packages` | Package catalog |
| `/patient-subscriptions` | Active subscriptions |
| `/appointments` | Appointment view |
| `/invoices` | Billing |
| `/insurance-claims` | Claims dashboard |
| `/suppliers` | Supplier directory |
| `/purchase-orders` | Purchase orders |
| `/stock-transfers` | Inter-branch transfers |
| `/stock-adjustments` | Stock corrections |
| `/locum-payments` | Locum batch payments |
| `/roster` | Weekly staff roster |
| `/roster/leaves` | Leave requests |
| `/reports` | Reports hub |
| `/audit-logs` | Admin-only audit trail |
| `/portal/login` | Patient portal entry |

---

## Numbering Conventions

| Entity | Format | Example |
|---|---|---|
| Patient | `{branch}-P{seq}` | `HQ-P00001` |
| Queue (walk-in) | `W{seq}` | `W001` |
| Queue (appointment) | `A{seq}` | `A001` |
| Consultation | `CN-{branch}-{YYYYMMDD}-{seq}` | `CN-HQ-20260425-0001` |
| Invoice | `{branch}-INV-{YYYYMM}-{seq}` | `HQ-INV-202604-0001` |
| Lab Report | `LAB-{branch}-{seq}` | `LAB-HQ-00001` |
| Purchase Order | `PO-{branch}-{YYYYMM}-{seq}` | `PO-HQ-202604-0001` |
| Stock Transfer | `TR-{YYYYMMDD}-{seq}` | `TR-20260425-0001` |
| Stock Adjustment | `ADJ-{branch}-{YYYYMMDD}-{seq}` | `ADJ-HQ-20260425-0001` |
| Membership | `MBR-{YYYY}-{seq}` | `MBR-2026-00001` |
| Subscription | `SUB-{YYYYMM}-{seq}` | `SUB-202604-0001` |
| Treatment Plan | `TP-{YYYYMM}-{seq}` | `TP-202604-0001` |
| Referral | `REF-{branch}-{YYYYMM}-{seq}` | `REF-HQ-202604-0001` |
| Locum Payment | `LP-{YYYYMM}-{seq}` | `LP-202604-0001` |

---

## Customization

### Branch Code
Set in `branches.code` (e.g. `HQ`, `BR01`). Used as prefix in numbering.

### Star Admin Theme
Assets live in `public/star-admin/`. Customize via:
- `resources/views/layouts/app.blade.php` — main layout
- `resources/views/layouts/sidebar.blade.php` — left navigation
- `public/star-admin/css/style.css` — theme colors

### Adding a New Branch
1. Login as admin → Branches → New Branch.
2. Set unique `code` (used in numbering).
3. Switch branch via the navbar selector — all data is auto-scoped.

---

## Maintenance

```bash
# Clear caches
php artisan optimize:clear

# Run any new migrations
php artisan migrate

# Rebuild compiled views
php artisan view:clear

# Tail logs
php artisan pail
```

---

## Known Limitations

- WhatsApp reminders run in **simulated mode** — implement real provider in `AppointmentReminderController@send`.
- Billplz / online payment gateway not yet wired (DomPDF installed and ready for receipt PDFs but templates not built).
- Locum Portal (separate locum login) not yet implemented — locums currently access via main staff login.
- Power BI-style chart dashboards not yet built (Chart.js bundle not added).
- Old Breeze Tailwind components remain in `resources/views/components/` (unused, safe to delete).

---

## License

Proprietary — built for internal clinic use. Laravel framework is MIT-licensed.
