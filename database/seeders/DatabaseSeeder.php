<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Service;
use App\Models\LocumDoctor;
use App\Models\PharmacyCategory;
use App\Models\Medicine;
use App\Models\LabTest;
use App\Models\InsurancePanel;
use App\Models\PatientInsurance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create branches
        $branch1 = Branch::create([
            'name' => 'Klinik Utama - Shah Alam',
            'code' => 'KU-SA',
            'address' => 'No. 10, Jalan Plumbum, Seksyen 7, 40000 Shah Alam, Selangor',
            'phone' => '03-55101234',
            'email' => 'shahalam@klinikutama.com',
            'opening_time' => '08:00',
            'closing_time' => '22:00',
            'is_active' => true,
        ]);

        $branch2 = Branch::create([
            'name' => 'Klinik Utama - Subang',
            'code' => 'KU-SB',
            'address' => 'No. 5, Jalan SS15/4, 47500 Subang Jaya, Selangor',
            'phone' => '03-56321234',
            'email' => 'subang@klinikutama.com',
            'opening_time' => '08:00',
            'closing_time' => '21:00',
            'is_active' => true,
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@clinic.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'branch_id' => $branch1->id,
            'phone' => '012-3456789',
            'is_active' => true,
        ]);

        // Create doctor users + doctor records
        $doctorUser1 = User::create([
            'name' => 'Dr. Ahmad Razak',
            'email' => 'dr.ahmad@clinic.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
            'branch_id' => $branch1->id,
            'phone' => '012-1111111',
            'is_active' => true,
        ]);

        $doctor1 = Doctor::create([
            'user_id' => $doctorUser1->id,
            'branch_id' => $branch1->id,
            'specialization' => 'General Practice',
            'qualification' => 'MBBS (UM)',
            'mmc_number' => 'MMC-12345',
            'apc_number' => 'APC-12345',
            'consultation_fee' => 50.00,
            'is_active' => true,
        ]);

        $doctorUser2 = User::create([
            'name' => 'Dr. Siti Nurhaliza',
            'email' => 'dr.siti@clinic.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
            'branch_id' => $branch1->id,
            'phone' => '012-2222222',
            'is_active' => true,
        ]);

        $doctor2 = Doctor::create([
            'user_id' => $doctorUser2->id,
            'branch_id' => $branch1->id,
            'specialization' => 'Paediatrics',
            'qualification' => 'MBBS, MRCPCH (UK)',
            'mmc_number' => 'MMC-67890',
            'apc_number' => 'APC-67890',
            'consultation_fee' => 80.00,
            'is_active' => true,
        ]);

        $doctorUser3 = User::create([
            'name' => 'Dr. Lee Wei Ming',
            'email' => 'dr.lee@clinic.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
            'branch_id' => $branch2->id,
            'phone' => '012-3333333',
            'is_active' => true,
        ]);

        $doctor3 = Doctor::create([
            'user_id' => $doctorUser3->id,
            'branch_id' => $branch2->id,
            'specialization' => 'General Practice',
            'qualification' => 'MD (UKM)',
            'mmc_number' => 'MMC-11111',
            'apc_number' => 'APC-11111',
            'consultation_fee' => 45.00,
            'is_active' => true,
        ]);

        // Create staff user
        User::create([
            'name' => 'Nurse Aminah',
            'email' => 'nurse@clinic.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'branch_id' => $branch1->id,
            'phone' => '012-4444444',
            'is_active' => true,
        ]);

        // Create doctor schedules (Mon-Fri for doctor1)
        foreach (range(1, 5) as $day) {
            DoctorSchedule::create([
                'doctor_id' => $doctor1->id,
                'branch_id' => $branch1->id,
                'day_of_week' => $day,
                'start_time' => '09:00',
                'end_time' => '17:00',
                'slot_duration' => 30,
                'is_available' => true,
            ]);
        }

        foreach (range(1, 5) as $day) {
            DoctorSchedule::create([
                'doctor_id' => $doctor2->id,
                'branch_id' => $branch1->id,
                'day_of_week' => $day,
                'start_time' => '10:00',
                'end_time' => '18:00',
                'slot_duration' => 20,
                'is_available' => true,
            ]);
        }

        // Create patients
        $patients = [
            ['name' => 'Muhammad Ali bin Hassan', 'ic_number' => '900101-01-1234', 'gender' => 'male', 'date_of_birth' => '1990-01-01', 'phone' => '011-11111111', 'blood_type' => 'A+'],
            ['name' => 'Fatimah binti Abdullah', 'ic_number' => '850515-02-5678', 'gender' => 'female', 'date_of_birth' => '1985-05-15', 'phone' => '011-22222222', 'blood_type' => 'B+'],
            ['name' => 'Tan Mei Ling', 'ic_number' => '950720-03-9012', 'gender' => 'female', 'date_of_birth' => '1995-07-20', 'phone' => '011-33333333', 'blood_type' => 'O+'],
            ['name' => 'Rajesh a/l Kumar', 'ic_number' => '880303-04-3456', 'gender' => 'male', 'date_of_birth' => '1988-03-03', 'phone' => '011-44444444', 'blood_type' => 'AB+'],
            ['name' => 'Nurul Aina binti Ismail', 'ic_number' => '000101-05-7890', 'gender' => 'female', 'date_of_birth' => '2000-01-01', 'phone' => '011-55555555', 'blood_type' => 'O-'],
        ];

        foreach ($patients as $i => $data) {
            Patient::create(array_merge($data, [
                'branch_id' => $i < 3 ? $branch1->id : $branch2->id,
                'patient_id' => Patient::generatePatientId($i < 3 ? $branch1->code : $branch2->code),
                'is_active' => true,
            ]));
        }

        // Create services
        $services = [
            ['name' => 'General Consultation', 'price' => 50.00, 'category' => 'Consultation'],
            ['name' => 'Specialist Consultation', 'price' => 80.00, 'category' => 'Consultation'],
            ['name' => 'Blood Test (Full)', 'price' => 120.00, 'category' => 'Lab'],
            ['name' => 'X-Ray', 'price' => 80.00, 'category' => 'Imaging'],
            ['name' => 'ECG', 'price' => 60.00, 'category' => 'Diagnostic'],
            ['name' => 'Wound Dressing', 'price' => 30.00, 'category' => 'Procedure'],
            ['name' => 'Injection', 'price' => 25.00, 'category' => 'Procedure'],
            ['name' => 'Nebulizer', 'price' => 35.00, 'category' => 'Treatment'],
            ['name' => 'MC Letter', 'price' => 10.00, 'category' => 'Document'],
            ['name' => 'Referral Letter', 'price' => 15.00, 'category' => 'Document'],
        ];

        foreach ($services as $service) {
            Service::create(array_merge($service, ['branch_id' => $branch1->id, 'is_active' => true]));
            Service::create(array_merge($service, ['branch_id' => $branch2->id, 'is_active' => true]));
        }

        // Create locum doctors
        LocumDoctor::create([
            'name' => 'Dr. Zainab binti Yusof',
            'email' => 'dr.zainab@locum.com',
            'phone' => '019-9999999',
            'mmc_number' => 'MMC-99999',
            'specialization' => 'General Practice',
            'hourly_rate' => 100.00,
            'session_rate' => 500.00,
            'is_active' => true,
        ]);

        LocumDoctor::create([
            'name' => 'Dr. Krishnan a/l Raju',
            'email' => 'dr.krish@locum.com',
            'phone' => '019-8888888',
            'mmc_number' => 'MMC-88888',
            'specialization' => 'Emergency Medicine',
            'hourly_rate' => 120.00,
            'session_rate' => 600.00,
            'is_active' => true,
        ]);

        // Phase 2: Pharmacy Categories & Medicines
        foreach ([$branch1, $branch2] as $branch) {
            $catAntibiotic = PharmacyCategory::create(['branch_id' => $branch->id, 'name' => 'Antibiotics', 'description' => 'Antibacterial medicines']);
            $catPainkiller = PharmacyCategory::create(['branch_id' => $branch->id, 'name' => 'Painkillers', 'description' => 'Pain relief medicines']);
            $catVitamin = PharmacyCategory::create(['branch_id' => $branch->id, 'name' => 'Vitamins & Supplements']);
            $catCough = PharmacyCategory::create(['branch_id' => $branch->id, 'name' => 'Cough & Cold']);

            $medicines = [
                ['name' => 'Amoxicillin 500mg', 'generic_name' => 'Amoxicillin', 'pharmacy_category_id' => $catAntibiotic->id, 'unit' => 'capsule', 'cost_price' => 0.30, 'selling_price' => 1.00, 'current_stock' => 500, 'manufacturer' => 'Hovid'],
                ['name' => 'Augmentin 625mg', 'generic_name' => 'Amoxicillin/Clavulanic Acid', 'pharmacy_category_id' => $catAntibiotic->id, 'unit' => 'tablet', 'cost_price' => 1.50, 'selling_price' => 3.50, 'current_stock' => 200, 'manufacturer' => 'GSK'],
                ['name' => 'Paracetamol 500mg', 'generic_name' => 'Paracetamol', 'pharmacy_category_id' => $catPainkiller->id, 'unit' => 'tablet', 'cost_price' => 0.05, 'selling_price' => 0.50, 'current_stock' => 1000, 'manufacturer' => 'Hovid'],
                ['name' => 'Ibuprofen 400mg', 'generic_name' => 'Ibuprofen', 'pharmacy_category_id' => $catPainkiller->id, 'unit' => 'tablet', 'cost_price' => 0.10, 'selling_price' => 0.80, 'current_stock' => 300, 'manufacturer' => 'Duopharma'],
                ['name' => 'Vitamin C 1000mg', 'generic_name' => 'Ascorbic Acid', 'pharmacy_category_id' => $catVitamin->id, 'unit' => 'tablet', 'cost_price' => 0.20, 'selling_price' => 1.00, 'current_stock' => 400, 'manufacturer' => 'Blackmores'],
                ['name' => 'Vitamin B Complex', 'generic_name' => 'B Vitamins', 'pharmacy_category_id' => $catVitamin->id, 'unit' => 'tablet', 'cost_price' => 0.15, 'selling_price' => 0.80, 'current_stock' => 350],
                ['name' => 'Diphenhydramine Syrup', 'generic_name' => 'Diphenhydramine', 'pharmacy_category_id' => $catCough->id, 'unit' => 'bottle', 'cost_price' => 3.00, 'selling_price' => 8.00, 'current_stock' => 50],
                ['name' => 'Loratadine 10mg', 'generic_name' => 'Loratadine', 'pharmacy_category_id' => $catCough->id, 'unit' => 'tablet', 'cost_price' => 0.20, 'selling_price' => 1.50, 'current_stock' => 5, 'reorder_level' => 10],
            ];

            foreach ($medicines as $med) {
                Medicine::create(array_merge($med, [
                    'branch_id' => $branch->id,
                    'reorder_level' => $med['reorder_level'] ?? 10,
                    'is_active' => true,
                ]));
            }
        }

        // Phase 2: Lab Tests
        $labTests = [
            ['name' => 'Full Blood Count (FBC)', 'category' => 'Blood', 'normal_range' => 'See reference', 'unit' => '-', 'price' => 30.00],
            ['name' => 'Fasting Blood Sugar', 'category' => 'Blood', 'normal_range' => '3.9-5.6', 'unit' => 'mmol/L', 'price' => 15.00],
            ['name' => 'HbA1c', 'category' => 'Blood', 'normal_range' => '<5.7', 'unit' => '%', 'price' => 45.00],
            ['name' => 'Lipid Profile', 'category' => 'Blood', 'normal_range' => 'See reference', 'unit' => 'mmol/L', 'price' => 40.00],
            ['name' => 'Liver Function Test', 'category' => 'Blood', 'normal_range' => 'See reference', 'unit' => 'U/L', 'price' => 35.00],
            ['name' => 'Renal Profile', 'category' => 'Blood', 'normal_range' => 'See reference', 'unit' => 'mmol/L', 'price' => 35.00],
            ['name' => 'Urine FEME', 'category' => 'Urine', 'normal_range' => 'See reference', 'unit' => '-', 'price' => 20.00],
            ['name' => 'Uric Acid', 'category' => 'Blood', 'normal_range' => '200-430', 'unit' => 'umol/L', 'price' => 18.00],
            ['name' => 'Thyroid Function Test', 'category' => 'Blood', 'normal_range' => 'TSH 0.4-4.0', 'unit' => 'mIU/L', 'price' => 55.00],
            ['name' => 'Chest X-Ray', 'category' => 'Imaging', 'price' => 80.00],
        ];

        foreach ([$branch1, $branch2] as $branch) {
            foreach ($labTests as $test) {
                LabTest::create(array_merge($test, [
                    'branch_id' => $branch->id,
                    'is_active' => true,
                ]));
            }
        }

        // Phase 2: Insurance Panels
        foreach ([$branch1, $branch2] as $branch) {
            $panelAia = InsurancePanel::create([
                'branch_id' => $branch->id,
                'company_name' => 'AIA Bhd',
                'type' => 'insurance',
                'contact_person' => 'Ahmad Faizal',
                'phone' => '03-21234567',
                'email' => 'panel@aia.com.my',
                'credit_terms' => 30,
                'consultation_limit' => 80.00,
                'annual_limit' => 2000.00,
                'covered_services' => 'Outpatient consultation, lab tests, medications',
                'exclusions' => 'Dental, optical, cosmetic',
                'requires_gl' => false,
                'is_active' => true,
            ]);

            $panelPetronas = InsurancePanel::create([
                'branch_id' => $branch->id,
                'company_name' => 'PETRONAS Corporate Panel',
                'type' => 'corporate',
                'contact_person' => 'Siti Rahmah',
                'phone' => '03-23456789',
                'email' => 'medical@petronas.com.my',
                'credit_terms' => 60,
                'consultation_limit' => 150.00,
                'annual_limit' => 5000.00,
                'covered_services' => 'Full outpatient, specialist referral, lab tests, medications, physiotherapy',
                'requires_gl' => true,
                'is_active' => true,
            ]);

            InsurancePanel::create([
                'branch_id' => $branch->id,
                'company_name' => 'Great Eastern Life',
                'type' => 'insurance',
                'contact_person' => 'Lee Mei Ying',
                'phone' => '03-34567890',
                'email' => 'claims@greateastern.com.my',
                'credit_terms' => 45,
                'consultation_limit' => 100.00,
                'annual_limit' => 3000.00,
                'covered_services' => 'Outpatient consultation, lab tests, medications',
                'exclusions' => 'Pre-existing conditions first 12 months',
                'requires_gl' => false,
                'is_active' => true,
            ]);

            InsurancePanel::create([
                'branch_id' => $branch->id,
                'company_name' => 'SOCSO (PERKESO)',
                'type' => 'government',
                'contact_person' => 'Mohd Azlan',
                'phone' => '03-45678901',
                'email' => 'panel@perkeso.gov.my',
                'credit_terms' => 90,
                'requires_gl' => true,
                'is_active' => true,
            ]);
        }

        // Link first 2 patients to insurance panels
        $firstPatient = Patient::first();
        $secondPatient = Patient::skip(1)->first();
        $firstPanel = InsurancePanel::where('branch_id', $branch1->id)->where('type', 'insurance')->first();
        $corpPanel = InsurancePanel::where('branch_id', $branch1->id)->where('type', 'corporate')->first();

        if ($firstPatient && $firstPanel) {
            PatientInsurance::create([
                'patient_id' => $firstPatient->id,
                'insurance_panel_id' => $firstPanel->id,
                'member_id' => 'AIA-MEM-001234',
                'policy_number' => 'POL-2026-00001',
                'effective_date' => '2026-01-01',
                'expiry_date' => '2026-12-31',
                'status' => 'active',
            ]);
        }

        if ($secondPatient && $corpPanel) {
            PatientInsurance::create([
                'patient_id' => $secondPatient->id,
                'insurance_panel_id' => $corpPanel->id,
                'member_id' => 'PTR-EMP-005678',
                'company_name' => 'PETRONAS',
                'department' => 'Engineering',
                'effective_date' => '2026-01-01',
                'expiry_date' => '2027-01-01',
                'status' => 'active',
            ]);
        }

        // Enable portal access for first patient
        $firstPatient = Patient::first();
        if ($firstPatient) {
            $firstPatient->update([
                'password' => Hash::make('patient123'),
                'portal_token' => \Illuminate\Support\Str::random(64),
                'portal_token_expires_at' => now()->addYear(),
            ]);
        }
    }
}
