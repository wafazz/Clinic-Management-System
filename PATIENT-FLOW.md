# Patient Flow Diagram

End-to-end flow from lead capture to consultation, billing, and close-out, with the person responsible for each segment.

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

---

## Responsibility Matrix (per phase)

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
