# Clinic Management System (CMS)
## Full System Documentation

> **Version**: 1.0  
> **Last Updated**: 2 April 2026  
> **Prepared by**: Codex Lure Development Team

---

## Table of Contents

1. [System Overview](#1-system-overview)
2. [Tech Stack](#2-tech-stack)
3. [Installation & Setup](#3-installation--setup)
4. [User Roles & Access Control](#4-user-roles--access-control)
5. [System Modules](#5-system-modules)
6. [Database Schema](#6-database-schema)
7. [API Routes](#7-api-routes)
8. [Patient Portal](#8-patient-portal)
9. [Audit & Security](#9-audit--security)
10. [Reports & Analytics](#10-reports--analytics)
11. [Default Test Data](#11-default-test-data)

---

## 1. System Overview

The Clinic Management System (CMS) is a **multi-branch SaaS** web application designed for Malaysian private clinics. It manages the full clinic workflow — from patient registration, doctor scheduling, and appointments to billing, pharmacy, lab reports, insurance claims, and patient self-service portal.

### Key Highlights

- **Multi-Branch**: Centralized system with branch-based data filtering and branch switcher
- **Role-Based Access**: 4 user roles — Admin, Doctor, Staff, Receptionist
- **Complete Clinical Workflow**: Appointments → Prescriptions → Pharmacy → Lab → Billing → Insurance Claims
- **Patient Portal**: Separate login for patients to view their records
- **Audit Trail**: Every create/update/delete action logged with full diff
- **In-App Notifications**: Real-time alerts for appointments, payments, low stock, etc.
- **Reports with Export**: 5 report modules with CSV export

---

## 2. Tech Stack

| Component        | Technology                                      |
| ---------------- | ----------------------------------------------- |
| **Framework**    | Laravel 12 (PHP 8.4)                            |
| **Frontend**     | Blade Templates + Star Admin Bootstrap 4        |
| **JS**           | Alpine.js (dynamic forms)                       |
| **Database**     | MySQL / MariaDB (port 3307)                     |
| **Auth**         | Laravel Breeze (staff) + Session-based (portal) |
| **File Storage** | Laravel Storage (public disk)                   |
| **Icons**        | Material Design Icons (MDI)                     |

### Directory Structure

```
clinic-app/
├── app/
│   ├── Http/Controllers/     # 21 controllers
│   ├── Http/Middleware/       # AdminMiddleware, RoleMiddleware, PortalAuth
│   ├── Http/Requests/        # Form request classes
│   ├── Models/               # 27 Eloquent models
│   └── Traits/               # Auditable, NotifiesUsers
├── database/
│   ├── migrations/           # 33 migration files
│   └── seeders/              # DatabaseSeeder with full test data
├── resources/views/
│   ├── layouts/              # app, sidebar, navigation, guest
│   ├── auth/                 # Login, register, password reset
│   ├── portal/               # Patient portal views
│   ├── reports/              # 6 report views
│   ├── audit-logs/           # Audit log views
│   ├── notifications/        # Notification views
│   └── [module]/             # CRUD views per module
├── routes/
│   ├── web.php               # All web routes (~150 routes)
│   └── auth.php              # Authentication routes
└── public/star-admin/        # Star Admin template assets (CSS/JS)
```

---

## 3. Installation & Setup

### Requirements
- PHP 8.4+
- Composer
- MySQL / MariaDB
- Node.js (not required — no Vite/Tailwind build)

### Steps

```bash
# 1. Clone and install
cd clinic-app
composer install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Configure .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=clinic_management
DB_USERNAME=root
DB_PASSWORD=

# 4. Run migrations and seed
php artisan migrate --seed

# 5. Storage link (for file uploads)
php artisan storage:link

# 6. Serve
php artisan serve
```

### Default Logins

| Role    | Email                 | Password   |
| ------- | --------------------- | ---------- |
| Admin   | admin@clinic.com      | password   |
| Doctor  | dr.ahmad@clinic.com   | password   |
| Doctor  | dr.siti@clinic.com    | password   |
| Doctor  | dr.lee@clinic.com     | password   |
| Staff   | nurse.aminah@clinic.com | password |

| Portal (Patient)  | IC Number       | Password    |
| ----------------- | --------------- | ----------- |
| Fatimah Abdullah   | 850215-14-5678 | patient123  |

---

## 4. User Roles & Access Control

### Roles

| Role           | Description                                             | Access Level                       |
| -------------- | ------------------------------------------------------- | ---------------------------------- |
| **Admin**      | Full system access, manages users, settings, audit logs | All modules + System settings      |
| **Doctor**     | Clinical access, appointments, prescriptions, lab       | Clinical modules (own schedule)    |
| **Staff**      | Day-to-day operations, pharmacy, billing                | Operational modules                |
| **Receptionist** | Front desk — appointments, patients, billing          | Patient-facing modules             |

### Access Restrictions

- **User Management** — Admin only
- **Audit Logs** — Admin only
- **Settings** — All authenticated users (admin typically manages)
- **All other modules** — All authenticated roles
- **Patient Portal** — Separate authentication (IC + password)

### Middleware

| Middleware         | Purpose                                         |
| ------------------ | ----------------------------------------------- |
| `auth`             | Standard Laravel authentication                 |
| `admin`            | Restricts to admin role only                    |
| `role:admin,staff` | Restricts to specified roles (variadic)         |
| `PortalAuth`       | Patient portal session-based authentication     |

---

## 5. System Modules

### 5.1 Branch Management

Manages multiple clinic locations. Each branch has its own data scope.

- **Fields**: Name, Code (unique), Address, Phone, Email, Opening/Closing Time, Status
- **Branch Switcher**: Dropdown in navbar to filter data by branch
- **Session-based**: `current_branch_id` stored in session

---

### 5.2 Patient Management

Central patient registry with Malaysian-specific fields.

- **Fields**: Name, IC Number, Gender, DOB, Phone, Email, Address, Emergency Contact, Allergies, Medical History, Blood Type
- **Auto-Generated Patient ID**: Format `{branch_code}-P{number}` (e.g., `KU-SA-P00001`)
- **Portal Access**: Admin can enable patient portal login
- **Patient Profile**: View appointments, invoices, prescriptions, lab reports, insurance coverage

---

### 5.3 Doctor & Schedule Management

Manage permanent doctors and their weekly schedules.

- **Doctor Fields**: User account, Branch, Specialization, Qualification, MMC Number, APC Number, Consultation Fee
- **Schedule**: Weekly grid — set available days, start/end time, slot duration (e.g., 30 min)
- **Auto-creates User**: Creating a doctor auto-creates a user account with `doctor` role

---

### 5.4 Appointment Management

Book and manage patient appointments.

- **Fields**: Patient, Doctor, Date, Time Slot, Reason, Notes
- **Status Flow**: `pending` → `confirmed` → `in_progress` → `completed`
- **Cancellation**: Can mark as `cancelled` or `no_show`
- **Linked To**: Invoice, Prescription, Lab Report (one-to-one from appointment)
- **Filters**: By date, doctor, status

---

### 5.5 Services & Billing

Define clinic services and generate invoices.

#### Services
- **Fields**: Name, Description, Price, Category, Status
- **Categories**: Consultation, Lab, Imaging, Diagnostic, Procedure, Treatment, Document

#### Invoices
- **Auto-Generated Invoice Number**: Format `INV-{branch_code}-{number}`
- **Dynamic Line Items**: Add services with quantity and price (Alpine.js)
- **Calculations**: Subtotal, Tax, Discount, Total
- **Payment Types**: Cash or Insurance Panel
- **Status**: `draft` → `issued` → `partial` → `paid`
- **Print View**: Printable invoice layout

#### Payments
- **Methods**: Cash, Card, Bank Transfer, E-Wallet
- **Partial Payments**: Multiple payments per invoice
- **Auto-Status**: Invoice status auto-updates based on payment total

---

### 5.6 Insurance Panel & Claims

Full insurance workflow — panel management, patient coverage, GL support, claims.

#### Insurance Panels
- **Types**: Corporate, Insurance, TPA, Government
- **Fields**: Company Name, Contact, Credit Terms, Consultation Limit, Annual Limit, Covered Services, Exclusions, GL Required
- **Per-Branch**: Each branch has its own panel agreements

#### Patient Insurance Coverage
- **Fields**: Member ID, Policy Number, Employer, Department, Effective/Expiry Date, Remaining Limit, Status
- **Linked**: Patient ↔ Insurance Panel

#### Insurance Claims
- **Auto-Generated Claim Number**: Format `CLM-{branch_code}-{number}`
- **GL (Guarantee Letter)**: If panel requires GL, claim starts with `gl_status: pending`
- **Status Flow**: `draft` → `submitted` → `approved` / `partial` / `rejected` → `paid`
- **Fields**: Claim Amount, Approved Amount, Patient Co-pay, Submission/Approval/Payment Dates, Rejection Reason

---

### 5.7 Pharmacy & Prescriptions

Inventory management and prescription dispensing.

#### Pharmacy Categories
- Organize medicines by type (Antibiotics, Painkillers, Vitamins, etc.)

#### Medicines
- **Fields**: Name, Generic Name, SKU, Unit, Cost/Selling Price, Reorder Level, Current Stock, Expiry Date, Manufacturer
- **Alerts**: Low stock warning (stock <= reorder level), Expiry tracking
- **Stock Adjustment**: Purchase, Adjustment, Return, Expired — all tracked with stock movements

#### Prescriptions
- **Fields**: Patient, Doctor, Appointment (optional), Status, Notes
- **Items**: Medicine, Dosage, Frequency, Duration, Quantity, Instructions
- **Dispense Action**: Marks as dispensed + auto-deducts stock from pharmacy
- **Status**: `draft` → `dispensed`

#### Stock Movements
- Every stock change is logged: type, quantity, before/after stock, reference, user

---

### 5.8 Lab Tests & Reports

Manage laboratory tests and patient results.

#### Lab Tests
- **Fields**: Name, Description, Category, Normal Range, Unit, Price
- **Categories**: Blood, Urine, Imaging, etc.

#### Lab Reports
- **Auto-Generated Report Number**: Format `LAB-{branch_code}-{number}`
- **Items**: Each report contains multiple test results
- **Result Entry**: Value, Normal/Abnormal flag, Notes
- **Status**: `pending` → `in_progress` → `completed`
- **Print View**: Printable lab report with results table

---

### 5.9 Locum Doctor Management

Manage temporary/contract doctors and their sessions.

#### Locum Doctors (Global — not branch-scoped)
- **Fields**: Name, IC, Email, Phone, MMC/APC Number, Specialization, Hourly Rate, Session Rate, Bank Details

#### Locum Sessions (Branch-scoped)
- **Fields**: Locum Doctor, Branch, Date, Start/End Time, Status, Total Pay, Is Paid
- **Pay Calculation**: `session_rate` if set, otherwise `hourly_rate × hours worked`
- **Status**: `scheduled` → `in_progress` → `completed`
- **Mark Paid**: Admin can mark sessions as paid

---

### 5.10 Appointment Reminders

Send reminders to patients via WhatsApp, SMS, or Email.

- **Channels**: WhatsApp (primary), SMS, Email
- **Fields**: Appointment link, Phone Number, Message, Scheduled At
- **Bulk Create**: Auto-generate reminders for all appointments on a target date
- **Status**: `pending` → `sent` / `failed`
- **Note**: WhatsApp API currently in simulated mode

---

### 5.11 Settings

System-wide configuration.

| Setting        | Description                  |
| -------------- | ---------------------------- |
| `clinic_name`  | Displayed in navbar & footer |
| `clinic_logo`  | Logo image in navbar         |

---

### 5.12 User Management (Admin Only)

- **CRUD**: Create, edit, deactivate users
- **Assign Roles**: Admin, Doctor, Staff, Receptionist
- **Assign Branch**: Link user to a branch
- **Activate/Deactivate**: Inactive users cannot log in
- **Note**: Public registration is disabled — only admin can create users

---

### 5.13 Profile Management

Each user can manage their own profile.

- **Editable**: Name, Phone, Profile Photo
- **Read-Only**: Email, Role, Branch, Member Since
- **Profile Photo**: Upload JPG/PNG/WebP (max 2MB), preview before save, remove option
- **Change Password**: Current password required

---

## 6. Database Schema

### Entity Relationship Summary

```
Branch (1) ──── (M) Patient
Branch (1) ──── (M) Doctor
Branch (1) ──── (M) Appointment
Branch (1) ──── (M) Invoice
Branch (1) ──── (M) Service
Branch (1) ──── (M) Medicine
Branch (1) ──── (M) LabTest
Branch (1) ──── (M) InsurancePanel

User (1) ──── (1) Doctor
Doctor (1) ──── (M) DoctorSchedule
Doctor (1) ──── (M) Appointment

Patient (1) ──── (M) Appointment
Patient (1) ──── (M) Invoice
Patient (1) ──── (M) Prescription
Patient (1) ──── (M) LabReport
Patient (1) ──── (M) PatientInsurance

Appointment (1) ──── (1) Invoice
Appointment (1) ──── (1) Prescription
Appointment (1) ──── (1) LabReport
Appointment (1) ──── (M) AppointmentReminder

Invoice (1) ──── (M) InvoiceItem
Invoice (1) ──── (M) Payment
Invoice (1) ──── (1) InsuranceClaim

Prescription (1) ──── (M) PrescriptionItem
PrescriptionItem ──── Medicine

LabReport (1) ──── (M) LabReportItem
LabReportItem ──── LabTest

InsurancePanel (1) ──── (M) PatientInsurance
InsurancePanel (1) ──── (M) InsuranceClaim

Medicine (1) ──── (M) StockMovement
PharmacyCategory (1) ──── (M) Medicine

LocumDoctor (1) ──── (M) LocumSession
```

### Tables Summary (33 migrations)

| #  | Table                  | Key Columns                                                     |
| -- | ---------------------- | --------------------------------------------------------------- |
| 1  | `users`                | name, email, role, branch_id, phone, profile_photo, is_active   |
| 2  | `branches`             | name, code, address, phone, opening_time, closing_time          |
| 3  | `patients`             | patient_id, name, ic_number, gender, dob, blood_type, portal    |
| 4  | `doctors`              | user_id, branch_id, specialization, mmc_number, consultation_fee|
| 5  | `doctor_schedules`     | doctor_id, day_of_week, start_time, end_time, slot_duration     |
| 6  | `appointments`         | patient_id, doctor_id, date, start_time, end_time, status       |
| 7  | `services`             | branch_id, name, price, category                                |
| 8  | `invoices`             | invoice_number, subtotal, tax, discount, total, status, payment_type |
| 9  | `invoice_items`        | invoice_id, service_id, description, quantity, unit_price, total |
| 10 | `payments`             | invoice_id, amount, method, reference, payment_date             |
| 11 | `insurance_panels`     | company_name, type, credit_terms, consultation_limit, requires_gl|
| 12 | `patient_insurances`   | patient_id, panel_id, member_id, policy_number, remaining_limit |
| 13 | `insurance_claims`     | claim_number, gl_number, claim_amount, approved_amount, status  |
| 14 | `pharmacy_categories`  | branch_id, name, description                                   |
| 15 | `medicines`            | name, sku, cost_price, selling_price, current_stock, reorder_level|
| 16 | `stock_movements`      | medicine_id, type, quantity, stock_before, stock_after           |
| 17 | `prescriptions`        | patient_id, doctor_id, appointment_id, status                   |
| 18 | `prescription_items`   | medicine_id, dosage, frequency, duration, quantity               |
| 19 | `lab_tests`            | name, category, normal_range, unit, price                       |
| 20 | `lab_reports`          | report_number, patient_id, doctor_id, status                    |
| 21 | `lab_report_items`     | lab_test_id, result, is_abnormal                                |
| 22 | `appointment_reminders`| appointment_id, channel, phone_number, message, status          |
| 23 | `locum_doctors`        | name, ic_number, mmc_number, hourly_rate, session_rate          |
| 24 | `locum_sessions`       | locum_doctor_id, branch_id, session_date, total_pay, is_paid    |
| 25 | `settings`             | key, value                                                      |
| 26 | `audit_logs`           | user_id, action, model_type, model_id, old/new_values, ip      |
| 27 | `notifications`        | user_id, type, title, message, icon, color, link, read_at       |

---

## 7. API Routes

### Staff Routes (requires login)

Total: **~150 routes** under `auth` middleware.

| Method   | URL                                      | Description                     |
| -------- | ---------------------------------------- | ------------------------------- |
| GET      | `/dashboard`                             | Main dashboard                  |
| POST     | `/branch/switch`                         | Switch active branch            |
| Resource | `/branches`                              | Branch CRUD                     |
| Resource | `/patients`                              | Patient CRUD                    |
| POST     | `/patients/{id}/portal-access`           | Enable portal for patient       |
| Resource | `/doctors`                               | Doctor CRUD                     |
| GET/POST | `/doctor-schedules/{doctor}`             | Doctor schedule management      |
| Resource | `/appointments`                          | Appointment CRUD                |
| PATCH    | `/appointments/{id}/status`              | Update appointment status       |
| Resource | `/services`                              | Service CRUD                    |
| Resource | `/invoices`                              | Invoice CRUD                    |
| GET      | `/invoices/{id}/print`                   | Print invoice                   |
| POST     | `/invoices/{id}/payments`                | Record payment                  |
| DELETE   | `/payments/{id}`                         | Delete payment                  |
| Resource | `/insurance-panels`                      | Insurance panel CRUD            |
| POST     | `/patients/{id}/insurance`               | Add patient insurance           |
| PUT      | `/patient-insurance/{id}`                | Update patient insurance        |
| Resource | `/insurance-claims`                      | Insurance claim CRUD            |
| PATCH    | `/insurance-claims/{id}/status`          | Update claim status             |
| Resource | `/pharmacy-categories`                   | Pharmacy category CRUD          |
| Resource | `/medicines`                             | Medicine CRUD                   |
| POST     | `/medicines/{id}/adjust-stock`           | Adjust medicine stock           |
| Resource | `/prescriptions`                         | Prescription CRUD               |
| PATCH    | `/prescriptions/{id}/dispense`           | Dispense prescription           |
| Resource | `/lab-tests`                             | Lab test CRUD                   |
| Resource | `/lab-reports`                           | Lab report CRUD                 |
| GET      | `/lab-reports/{id}/print`                | Print lab report                |
| Resource | `/locum-doctors`                         | Locum doctor CRUD               |
| Resource | `/locum-sessions`                        | Locum session CRUD              |
| PATCH    | `/locum-sessions/{id}/mark-paid`         | Mark session as paid            |
| GET/POST | `/reminders`                             | Reminder management             |
| POST     | `/reminders/bulk`                        | Bulk create reminders           |
| PATCH    | `/reminders/{id}/send`                   | Send reminder                   |
| GET/PATCH| `/profile`                               | User profile                    |
| DELETE   | `/profile/photo`                         | Remove profile photo            |
| Resource | `/users` (admin only)                    | User management                 |
| GET/PUT  | `/settings`                              | System settings                 |
| GET      | `/reports`                               | Reports dashboard               |
| GET      | `/reports/financial`                     | Financial report                |
| GET      | `/reports/patients`                      | Patient report                  |
| GET      | `/reports/appointments`                  | Appointment report              |
| GET      | `/reports/pharmacy`                      | Pharmacy report                 |
| GET      | `/reports/lab`                           | Lab report analytics            |
| GET      | `/reports/export/{type}`                 | CSV export (5 types)            |
| GET      | `/audit-logs` (admin only)               | Audit log list                  |
| GET      | `/audit-logs/{id}` (admin only)          | Audit log detail                |
| GET      | `/notifications`                         | Notification list               |
| POST     | `/notifications/{id}/read`               | Mark notification read          |
| POST     | `/notifications/mark-all-read`           | Mark all read                   |
| DELETE   | `/notifications/{id}`                    | Delete notification             |

---

## 8. Patient Portal

A separate self-service portal for patients to view their medical records.

### Access

- **URL**: `/portal/login`
- **Authentication**: IC Number + Password (session-based, not Laravel auth)
- **Enabling Access**: Admin enables portal access from Patient management page

### Features

| Feature          | Description                                              |
| ---------------- | -------------------------------------------------------- |
| **Dashboard**    | Upcoming appointments, recent invoices, lab reports      |
| **Appointments** | View all past and upcoming appointments                  |
| **Invoices**     | View invoices with payment status and line item details  |
| **Lab Reports**  | View completed lab results with abnormal flags           |
| **Prescriptions**| View medication details, dosage, instructions            |
| **Profile**      | View personal info, change password                      |

### Portal Layout
- Separate Bootstrap navbar layout (not Star Admin sidebar)
- Star Admin CSS/JS shared for consistent styling
- Mobile-responsive

---

## 9. Audit & Security

### 9.1 Audit Logs

Every significant action is automatically tracked.

- **Tracked Actions**: Create, Update, Delete
- **Tracked Models** (14 total): Patient, Appointment, Invoice, Doctor, Branch, Service, Medicine, Prescription, LabReport, InsurancePanel, InsuranceClaim, LocumDoctor, LocumSession, User
- **Logged Data**: User, Branch, Action, Model, Description, Old Values (JSON), New Values (JSON), IP Address, User Agent, Timestamp
- **Sensitive Fields Excluded**: password, remember_token, portal_token
- **Access**: Admin only — filterable by user, action, module, date range

### 9.2 In-App Notifications

Real-time alerts for important events.

| Event                        | Notified Roles         | Type       |
| ---------------------------- | ---------------------- | ---------- |
| New appointment booked       | Doctor, Admin, Receptionist | appointment |
| Appointment cancelled        | Admin, Receptionist    | appointment |
| Invoice paid                 | Admin                  | invoice    |
| Insurance claim status change| Admin                  | insurance  |
| Prescription dispensed       | Admin, Doctor          | pharmacy   |
| Lab report completed         | Doctor                 | lab        |
| Low stock alert              | Admin, Staff           | pharmacy   |

- **Bell Icon**: Navbar dropdown with 5 most recent + unread count badge
- **Full Page**: Filterable by type and read/unread status
- **Actions**: Mark as read, mark all read, delete, click-through to related record

### 9.3 Security Features

- **RBAC**: Role-based middleware on sensitive routes
- **CSRF Protection**: All POST/PUT/DELETE forms
- **Password Hashing**: bcrypt via Laravel
- **Session-based Auth**: Staff (Laravel session) + Portal (custom session)
- **Inactive User Block**: Deactivated users cannot log in
- **Admin-Only Operations**: User management, audit logs, settings

---

## 10. Reports & Analytics

### 10.1 Financial Report

- Total Revenue, Outstanding Amount, Invoice Count, Paid Count
- Revenue by Branch (bar chart)
- Revenue by Doctor (table)
- Revenue by Service — Top 15 (table)
- Payment Method Breakdown (pie chart)
- Daily Revenue Trend (line chart)
- **Filters**: Branch, Date Range
- **Export**: CSV

### 10.2 Patient Report

- Total Patients, New This Month, Active Patients, Insured Count
- Gender Distribution (pie chart)
- Blood Type Distribution (bar chart)
- Monthly New Patients — Last 6 months (line chart)
- Top 10 Patients by Visits (table)
- **Filters**: Branch
- **Export**: CSV

### 10.3 Appointment Report

- Total Appointments, Completed, Cancelled, Completion Rate
- Status Breakdown (pie chart)
- Appointments by Doctor with completion stats (table)
- Peak Hours Analysis (bar chart)
- Daily Trend (line chart)
- Day of Week Distribution (bar chart)
- **Filters**: Branch, Date Range
- **Export**: CSV

### 10.4 Pharmacy Report

- Total Medicines, Low Stock Count, Expiring Soon, Expired
- Stock Value (Cost vs Selling)
- Low Stock Alert List (table with details)
- Expiring Soon List (table)
- Top 10 Dispensed Medicines — Last 30 days (table)
- Stock by Category (table)
- **Filters**: Branch
- **Export**: CSV

### 10.5 Lab Report Analytics

- Total Reports, Completed, Pending
- Status Breakdown (pie chart)
- Top 10 Tests Ordered with abnormal count (table)
- Daily Volume Trend (line chart)
- **Filters**: Branch, Date Range
- **Export**: CSV

---

## 11. Default Test Data

The database seeder creates a complete test environment:

### Branches
| Branch               | Code  | Hours       |
| -------------------- | ----- | ----------- |
| Klinik Utama - Shah Alam | KU-SA | 08:00-22:00 |
| Klinik Utama - Subang    | KU-SB | 08:00-21:00 |

### Users
| Name              | Role   | Email                   |
| ----------------- | ------ | ----------------------- |
| Admin             | Admin  | admin@clinic.com        |
| Dr. Ahmad Razak   | Doctor | dr.ahmad@clinic.com     |
| Dr. Siti Nurhaliza | Doctor | dr.siti@clinic.com     |
| Dr. Lee Wei Ming  | Doctor | dr.lee@clinic.com       |
| Nurse Aminah      | Staff  | nurse.aminah@clinic.com |

### Patients (5)
Muhammad Ali bin Hassan, Fatimah binti Abdullah, Tan Mei Ling, Rajesh a/l Kumar, Nurul Aina binti Ismail

### Services (10 per branch)
General Consultation (RM50), Specialist Consultation (RM120), Blood Test (RM80), X-Ray (RM150), ECG (RM100), Wound Dressing (RM40), Injection (RM30), Nebulizer (RM35), MC Letter (RM10), Referral Letter (RM15)

### Insurance Panels (4 per branch)
AIA Bhd, PETRONAS Corporate Panel, Great Eastern Life, SOCSO (PERKESO)

### Pharmacy
4 categories × 8 medicines per category = 32 medicines per branch

### Lab Tests (10 per branch)
FBC, Fasting Blood Sugar, HbA1c, Lipid Profile, Liver Function Test, Renal Profile, Urine FEME, Uric Acid, Thyroid Function Test, Chest X-Ray

### Locum Doctors (2)
Dr. Zainab binti Yusof, Dr. Krishnan a/l Raju

---

## Appendix: Auto-Generated ID Formats

| Entity       | Format                          | Example           |
| ------------ | ------------------------------- | ----------------- |
| Patient ID   | `{branch_code}-P{5-digit}`     | KU-SA-P00001      |
| Invoice No   | `INV-{branch_code}-{6-digit}`  | INV-KU-SA-000001  |
| Claim No     | `CLM-{branch_code}-{5-digit}`  | CLM-KU-SA-00001   |
| Lab Report No| `LAB-{branch_code}-{5-digit}`  | LAB-KU-SA-00001   |

---

*End of Documentation*
