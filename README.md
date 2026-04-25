# Clinic Management System (Multi-Branch)

A full-featured clinic management system for multi-branch clinics in Malaysia. Handles the complete patient journey: registration, queue (Nombor Giliran), appointment, consultation, prescription, lab, billing, insurance panels, and patient portal.

Built with **Laravel 12 + PHP Blade + Star Admin Bootstrap 4 + Alpine.js**.

---

## Features

### Patient Flow
- **Patients** — registration, IC-based identification, allergy tracking, portal access
- **Doctors & Schedules** — recurring schedule grid (per branch), consultation fees, MMC/APC numbers
- **Appointments** — booking, status workflow (pending → confirmed → in_progress → completed)
- **Walk-In Queue (Nombor Giliran)** — daily queue with W/A prefix, call-next, TV display screen, appointment check-in
- **Consultations** — vitals (BP, pulse, temp, weight, height, BMI auto-calc, SpO2), clinical notes (chief complaint, history, examination, diagnosis, treatment plan, follow-up), Medical Certificate (printable), linked prescriptions / lab orders

### Clinical
- **Pharmacy** — categories, medicines, stock movement tracking, dispensing with auto-deduction
- **Prescriptions** — multi-item with dosage/frequency/duration/instructions, draft → dispensed flow
- **Lab Tests & Reports** — test catalog, multi-item lab reports with results, printable

### Billing
- **Invoices** — multi-item, tax/discount, auto-generated invoice numbers per branch
- **Payments** — multiple payments per invoice, paid/partial/issued status
- **Insurance Panels** — companies, credit terms, consultation/annual limits
- **Patient Insurance** — coverage records, GL workflow
- **Insurance Claims** — claim submission, status tracking (submitted → approved/rejected → paid)

### System
- **Multi-Branch** — branch switcher, all data scoped to current branch
- **RBAC** — 4 roles (admin, doctor, staff, receptionist), role-based menu visibility
- **Audit Logs** — admin-only, tracks create/update/delete on 14 models with old/new values diff
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
| PHP | 8.2+ |
| Frontend | Blade + Alpine.js |
| UI Template | Star Admin Free (Bootstrap 4) |
| Database | MySQL / MariaDB |
| Auth | Laravel Breeze (web) + custom PortalAuth middleware (patient portal) |
| Build | Vite (only used for legacy Tailwind components — Star Admin assets are static) |

---

## Roles

| Role | Access |
|---|---|
| **admin** | Full access + user management + audit logs |
| **doctor** | Consultations, patients, appointments, prescriptions, lab |
| **staff** | General clinic operations (pharmacy, billing, lab) |
| **receptionist** | Patients, appointments, queue, billing |
| **patient** | Patient Portal only (separate login at `/portal/login`) |

---

## Requirements

- PHP 8.2 or higher
- Composer 2.x
- Node.js 18+ (only needed if rebuilding Vite assets)
- MySQL 5.7+ or MariaDB 10.4+
- Web server (Apache / Nginx) or `php artisan serve` for local dev

---

## Installation

```bash
# 1. Clone the project (or copy the folder)
cd clinic-app

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

# 5. Create the database (in phpMyAdmin or CLI)
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
│   ├── Http/Controllers/        # 30+ controllers
│   ├── Models/                  # 29 Eloquent models
│   ├── Http/Middleware/         # PortalAuth, AdminOnly, etc.
│   └── Traits/                  # Auditable, NotifiesUsers
├── database/
│   ├── migrations/              # 37 migrations
│   └── seeders/                 # DatabaseSeeder + module seeders
├── resources/views/             # Blade views (organized by feature)
│   ├── consultations/
│   ├── walk-in-queue/
│   ├── appointments/
│   ├── prescriptions/
│   ├── lab/
│   ├── invoices/
│   ├── insurance/
│   ├── portal/                  # Patient portal
│   └── layouts/                 # app.blade.php, sidebar.blade.php
├── public/star-admin/           # Star Admin static assets (CSS/JS/fonts)
├── routes/
│   ├── web.php                  # Main routes
│   └── auth.php                 # Breeze auth routes
└── storage/                     # logs, uploads, sessions
```

---

## Key Workflows

### 1. Walk-In Patient → Consultation → Bill
1. Receptionist registers patient (or selects existing).
2. Add to queue at `/walk-in-queue/create` → gets **W001** number.
3. Doctor clicks **Call Next** → patient status: `serving`.
4. Doctor clicks **Start Consultation** → records vitals + diagnosis + Rx/Lab + MC.
5. Click **Complete & Bill** → auto-redirects to invoice creation with consultation fee pre-filled.

### 2. Appointment Patient
1. Booked online or via receptionist at `/appointments/create`.
2. On the day, receptionist clicks **Check In** on appointment → gets **A001** queue number.
3. Same consultation flow as walk-in.

### 3. Insurance Panel Patient
1. Patient comes with panel coverage → recorded in `patient_insurance`.
2. Invoice marked as `payment_type = panel` → linked to panel + patient_insurance.
3. Submit `insurance_claim` → workflow: submitted → approved (or rejected) → paid.
4. Annual limit auto-deducted on insurer payment.

---

## Database Schema (Highlights)

**Core**: `branches`, `users`, `patients`, `doctors`, `doctor_schedules`

**Operations**: `appointments`, `walk_in_queues`, `consultations`, `services`

**Clinical**: `prescriptions` + `prescription_items`, `medicines` + `pharmacy_categories` + `stock_movements`, `lab_tests`, `lab_reports` + `lab_report_items`

**Billing**: `invoices` + `invoice_items`, `payments`

**Insurance**: `insurance_panels`, `patient_insurances`, `insurance_claims`

**Locum**: `locum_doctors`, `locum_sessions`

**System**: `audit_logs`, `notifications`, `appointment_reminders`, `settings`

> The `consultation_id` FK links `consultations` to `prescriptions`, `lab_reports`, and `invoices` — providing a single thread for the patient encounter.

---

## Important Routes

| URL | Purpose |
|---|---|
| `/dashboard` | Branch overview, today's stats |
| `/walk-in-queue` | Today's queue with call-next |
| `/walk-in-queue/display` | TV display screen for waiting area |
| `/consultations` | All consultations (filterable) |
| `/appointments` | Appointment calendar view |
| `/invoices` | Billing |
| `/insurance-claims` | Claims dashboard |
| `/reports` | Reports hub |
| `/audit-logs` | Admin-only audit trail |
| `/portal/login` | Patient portal entry |

---

## Customization

### Branch Code
Used as prefix for invoice / lab / consultation numbers. Set in `branches.code` (e.g. `HQ`, `BR01`). Format examples:
- Consultation: `CN-HQ-20260425-0001`
- Invoice: `HQ-INV-202604-0001`
- Lab Report: `LAB-HQ-00001`

### Star Admin Theme
The UI uses Star Admin Free (Bootstrap 4) — assets live in `public/star-admin/`. To customize, edit:
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
- No git repository initialized in this folder yet.
- Old Breeze Tailwind components remain in `resources/views/components/` (unused, safe to delete).

---

## License

Proprietary — built for internal clinic use. Laravel framework is MIT-licensed.
