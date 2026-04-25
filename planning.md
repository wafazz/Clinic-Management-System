# Clinic Management System - Planning

## Phase 1: Core Modules
- [x] Branch Management (CRUD, multi-branch, session switching)
- [x] Patient Management (CRUD, IC-based, branch-linked)
- [x] Doctor Management (CRUD, specialization, consultation fee)
- [x] Doctor Schedule (day/time slots, availability toggle)
- [x] Appointment Management (booking, status workflow, time slots)
- [x] Billing / Invoice (items, payments, partial/full, print)
- [x] Locum Doctor Management (doctors, sessions, payment tracking)

## Phase 2: Extended Modules
- [x] Pharmacy Inventory (categories, medicines, stock adjustment)
- [x] Prescriptions (create from appointment, dispense with stock deduction)
- [x] Lab Tests & Reports (test catalog, reports with items, print)
- [x] Appointment Reminders (WhatsApp/SMS/Email, bulk create)
- [x] Patient Portal (separate auth, appointments, invoices, lab reports, prescriptions, profile)
- [x] Insurance Panel (panels CRUD, patient coverage, GL support)
- [x] Insurance Claims (claims workflow: draft → submitted → approved/partial/rejected → paid)

## Template & UI
- [x] Star Admin Bootstrap 4 Migration (all views converted from Tailwind)
- [x] Sidebar Navigation (icons, collapsible submenus, active states)
- [x] Guest/Auth Layout (gradient background, logo from settings)
- [x] Portal Layout (Star Admin CSS/JS, Bootstrap navbar)
- [x] Profile (Bootstrap modal for delete, no Breeze components)
- [x] Settings (clinic name, logo upload, dynamic navbar branding)

## Phase 3: Reports & Analytics
- [x] Dashboard Analytics (patient stats, revenue charts, appointment trends)
- [x] Financial Reports (revenue by branch, by doctor, by service)
- [x] Patient Reports (demographics, visit history, insurance usage)
- [x] Appointment Reports (utilization, no-show rates, peak hours)
- [x] Pharmacy Reports (stock levels, dispensing history, expiry alerts)
- [x] Lab Reports Analytics (test volume, turnaround time)

## Phase 3: System Features
- [ ] Notifications (in-app, email)
- [ ] Audit Logs (user actions, data changes tracking)
- [ ] Role-Based Access Control (admin, doctor, staff, receptionist)
- [ ] Data Export (PDF, Excel for reports)
