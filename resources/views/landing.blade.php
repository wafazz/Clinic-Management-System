<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="ClinicQo — modern, multi-branch clinic management system for Malaysia. Patients, appointments, queue, consultation, billing, and online payments — all in one place.">
    <title>ClinicQo — Clinic Management System</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icon-32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/icon-16.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">

    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            color: #0f172a;
            line-height: 1.6;
            background: #fff;
        }
        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; display: block; }

        /* ---------- NAV ---------- */
        .nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid #e5e7eb;
        }
        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .nav-logo img { height: 36px; }
        .nav-links { display: flex; gap: 28px; align-items: center; }
        .nav-links a { color: #475569; font-weight: 500; font-size: 0.95rem; }
        .nav-links a:hover { color: #0ea5e9; }
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 22px; border-radius: 10px; font-weight: 600;
            font-size: 0.95rem; transition: all 0.2s ease; border: none;
            cursor: pointer; line-height: 1;
        }
        .btn-primary { background: linear-gradient(135deg, #0ea5e9, #06b6d4); color: #fff; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 22px rgba(14, 165, 233, 0.35); }
        .btn-outline { background: transparent; color: #0f172a; border: 1px solid #e5e7eb; }
        .btn-outline:hover { border-color: #0ea5e9; color: #0ea5e9; }
        .btn-lg { padding: 14px 28px; font-size: 1rem; }

        /* ---------- HERO ---------- */
        .hero {
            position: relative;
            padding: 80px 24px 100px;
            background:
                radial-gradient(ellipse at 80% 20%, rgba(14, 165, 233, 0.12), transparent 60%),
                radial-gradient(ellipse at 20% 80%, rgba(16, 185, 129, 0.10), transparent 60%),
                #fff;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image:
                linear-gradient(rgba(14, 165, 233, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(14, 165, 233, 0.04) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
            mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
        }
        .hero-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 60px;
            align-items: center;
            position: relative;
        }
        .hero h1 {
            font-size: 3.2rem;
            line-height: 1.1;
            font-weight: 800;
            margin: 0 0 20px;
            letter-spacing: -0.02em;
            color: #0f172a;
        }
        .hero h1 .accent {
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero p.lead {
            font-size: 1.15rem;
            color: #475569;
            margin: 0 0 32px;
            max-width: 540px;
        }
        .hero-cta { display: flex; gap: 14px; flex-wrap: wrap; }
        .hero-trust {
            margin-top: 32px;
            display: flex;
            align-items: center;
            gap: 18px;
            color: #64748b;
            font-size: 0.85rem;
        }
        .hero-trust .dots {
            display: flex; gap: -8px;
        }
        .hero-trust .dots span {
            width: 28px; height: 28px; border-radius: 50%;
            border: 2px solid #fff;
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            margin-left: -8px;
            display: inline-flex; align-items: center; justify-content: center;
            color: #fff; font-size: 0.7rem; font-weight: 700;
        }
        .hero-trust .dots span:first-child { margin-left: 0; }

        .hero-visual {
            position: relative;
        }
        .hero-card {
            background: #fff;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.18), 0 0 0 1px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
        }
        .hero-card-header {
            display: flex; justify-content: space-between; align-items: center;
            padding-bottom: 16px; border-bottom: 1px solid #f1f5f9;
            margin-bottom: 16px;
        }
        .hero-card-header strong { font-size: 0.95rem; }
        .hero-card-header .pill {
            background: rgba(16, 185, 129, 0.12); color: #047857;
            padding: 4px 10px; border-radius: 999px; font-size: 0.72rem; font-weight: 600;
        }
        .hero-stat-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 18px; }
        .hero-stat-cell {
            background: #f8fafc; border-radius: 12px; padding: 14px;
            border-left: 3px solid;
        }
        .hero-stat-cell.b { border-left-color: #0ea5e9; }
        .hero-stat-cell.g { border-left-color: #10b981; }
        .hero-stat-cell.o { border-left-color: #f59e0b; }
        .hero-stat-cell .label { font-size: 0.7rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; }
        .hero-stat-cell .num { font-size: 1.4rem; font-weight: 800; color: #0f172a; line-height: 1; margin-top: 4px; }
        .hero-queue-list { font-size: 0.88rem; }
        .hero-queue-list .row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 0; border-bottom: 1px solid #f1f5f9;
        }
        .hero-queue-list .row:last-child { border-bottom: none; }
        .hero-queue-list .qnum {
            background: #eff6ff; color: #0369a1; font-weight: 700;
            padding: 4px 10px; border-radius: 6px; font-size: 0.78rem;
        }
        .hero-queue-list .qnum.priority { background: #fef2f2; color: #b91c1c; }
        .hero-queue-list .status {
            font-size: 0.7rem; padding: 3px 8px; border-radius: 4px; font-weight: 600;
        }
        .hero-queue-list .status.serving { background: rgba(14, 165, 233, 0.12); color: #0369a1; }
        .hero-queue-list .status.waiting { background: rgba(245, 158, 11, 0.12); color: #b45309; }
        .floating-icon {
            position: absolute;
            background: #fff; border-radius: 14px;
            padding: 12px 14px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
            display: flex; align-items: center; gap: 10px;
            font-size: 0.85rem; font-weight: 600;
        }
        .floating-icon i { font-size: 1.4em; }
        .floating-1 {
            top: -20px; right: -10px;
            color: #047857;
        }
        .floating-2 {
            bottom: -20px; left: -10px;
            color: #0369a1;
        }

        /* ---------- TRUST BAR ---------- */
        .trust-bar {
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
            padding: 32px 24px;
            background: #f8fafc;
        }
        .trust-bar-inner {
            max-width: 1200px; margin: 0 auto;
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;
        }
        .trust-item { text-align: center; }
        .trust-item .num {
            font-size: 2rem; font-weight: 800;
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
        }
        .trust-item .label { color: #64748b; font-size: 0.85rem; margin-top: 6px; }

        /* ---------- SECTIONS ---------- */
        .section { padding: 90px 24px; }
        .section-inner { max-width: 1200px; margin: 0 auto; }
        .section-head { text-align: center; max-width: 680px; margin: 0 auto 60px; }
        .section-head .eyebrow {
            display: inline-block; padding: 6px 14px;
            background: rgba(14, 165, 233, 0.12); color: #0369a1;
            border-radius: 999px; font-size: 0.78rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.05em;
            margin-bottom: 16px;
        }
        .section-head h2 {
            font-size: 2.4rem; font-weight: 800; margin: 0 0 14px;
            color: #0f172a; letter-spacing: -0.01em; line-height: 1.2;
        }
        .section-head p { color: #64748b; font-size: 1.05rem; margin: 0; }

        /* ---------- FEATURE GRID ---------- */
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .feature-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 28px;
            transition: all 0.25s ease;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            border-color: #0ea5e9;
            box-shadow: 0 20px 40px -10px rgba(14, 165, 233, 0.18);
        }
        .feature-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 16px;
        }
        .fi-blue { background: rgba(14, 165, 233, 0.12); color: #0369a1; }
        .fi-green { background: rgba(16, 185, 129, 0.12); color: #047857; }
        .fi-amber { background: rgba(245, 158, 11, 0.12); color: #b45309; }
        .fi-purple { background: rgba(139, 92, 246, 0.12); color: #6d28d9; }
        .fi-pink { background: rgba(236, 72, 153, 0.12); color: #be185d; }
        .fi-teal { background: rgba(20, 184, 166, 0.12); color: #0f766e; }
        .feature-card h3 { font-size: 1.05rem; font-weight: 700; margin: 0 0 8px; color: #0f172a; }
        .feature-card p { color: #64748b; margin: 0; font-size: 0.92rem; line-height: 1.5; }

        /* ---------- HOW IT WORKS ---------- */
        .steps {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }
        .step { text-align: center; position: relative; }
        .step-num {
            width: 56px; height: 56px; border-radius: 50%;
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            color: #fff; font-weight: 800; font-size: 1.3rem;
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.3);
        }
        .step h4 { font-size: 1.05rem; margin: 0 0 6px; }
        .step p { color: #64748b; margin: 0; font-size: 0.9rem; }

        /* ---------- SECURITY / TRUST ---------- */
        .trust-section {
            background: linear-gradient(135deg, #f0f9ff, #ecfeff, #ecfdf5);
        }
        .trust-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 24px;
        }
        .trust-card {
            background: #fff;
            border-radius: 16px;
            padding: 28px;
            display: flex; gap: 16px; align-items: flex-start;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
        }
        .trust-card i { font-size: 1.8rem; flex-shrink: 0; }
        .trust-card h4 { margin: 0 0 4px; font-size: 1rem; font-weight: 700; }
        .trust-card p { margin: 0; color: #64748b; font-size: 0.9rem; }

        /* ---------- CTA ---------- */
        .cta {
            padding: 90px 24px;
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 50%, #10b981 100%);
            color: #fff; text-align: center;
        }
        .cta h2 {
            font-size: 2.4rem; font-weight: 800; margin: 0 0 14px;
            color: #fff; letter-spacing: -0.01em;
        }
        .cta p { color: rgba(255, 255, 255, 0.9); font-size: 1.1rem; margin: 0 0 32px; }
        .cta .btn-primary {
            background: #fff; color: #0369a1;
        }
        .cta .btn-primary:hover {
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.2);
        }
        .cta .btn-outline {
            border-color: rgba(255, 255, 255, 0.4); color: #fff; background: rgba(255, 255, 255, 0.08);
        }
        .cta .btn-outline:hover {
            border-color: #fff; background: rgba(255, 255, 255, 0.18);
        }

        /* ---------- FOOTER ---------- */
        footer {
            background: #0f172a; color: #94a3b8;
            padding: 60px 24px 30px;
        }
        .footer-inner {
            max-width: 1200px; margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .footer-logo img { height: 32px; filter: brightness(0) invert(1); margin-bottom: 14px; }
        .footer-logo p { font-size: 0.9rem; color: #94a3b8; max-width: 320px; line-height: 1.5; }
        .footer-col h5 { color: #fff; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 16px; font-weight: 600; }
        .footer-col a { display: block; color: #94a3b8; margin-bottom: 10px; font-size: 0.92rem; transition: color 0.2s; }
        .footer-col a:hover { color: #fff; }
        .footer-bottom {
            max-width: 1200px; margin: 0 auto;
            padding-top: 24px;
            display: flex; justify-content: space-between; align-items: center;
            font-size: 0.85rem;
            flex-wrap: wrap; gap: 12px;
        }

        /* ---------- RESPONSIVE ---------- */
        @media (max-width: 991px) {
            .hero-inner { grid-template-columns: 1fr; gap: 40px; }
            .hero h1 { font-size: 2.4rem; }
            .feature-grid, .trust-grid { grid-template-columns: 1fr 1fr; }
            .trust-bar-inner { grid-template-columns: 1fr 1fr; row-gap: 30px; }
            .steps { grid-template-columns: 1fr 1fr; }
            .footer-inner { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 600px) {
            .hero { padding: 50px 20px 60px; }
            .hero h1 { font-size: 2rem; }
            .hero p.lead { font-size: 1rem; }
            .nav-links a:not(.btn) { display: none; }
            .section { padding: 60px 20px; }
            .section-head h2, .cta h2 { font-size: 1.8rem; }
            .feature-grid, .trust-grid, .steps { grid-template-columns: 1fr; }
            .footer-inner { grid-template-columns: 1fr; gap: 30px; }
            .floating-icon { display: none; }
        }
    </style>
</head>
<body>

{{-- =================== NAVBAR =================== --}}
<nav class="nav">
    <div class="nav-inner">
        <a href="/" class="nav-logo"><img src="{{ asset('images/clinicQo.png') }}" alt="ClinicQo"></a>
        <div class="nav-links">
            <a href="#features">Features</a>
            <a href="#how-it-works">How It Works</a>
            <a href="#security">Security</a>
            <a href="{{ route('portal.login') }}" class="btn btn-outline"><i class="mdi mdi-account"></i>Patient Portal</a>
            <a href="{{ route('login') }}" class="btn btn-primary">Sign In <i class="mdi mdi-arrow-right"></i></a>
        </div>
    </div>
</nav>

{{-- =================== HERO =================== --}}
<section class="hero">
    <div class="hero-inner">
        <div>
            <span style="display:inline-block;padding:6px 14px;background:rgba(16,185,129,0.12);color:#047857;border-radius:999px;font-size:0.8rem;font-weight:600;margin-bottom:20px;">
                <i class="mdi mdi-shield-check"></i> Built for Malaysian Clinics
            </span>
            <h1>Run your clinic<br><span class="accent">end-to-end</span> from one screen.</h1>
            <p class="lead">From patient lead → queue → consultation → prescription → billing → insurance claim. ClinicQo handles every step so your team can focus on care, not paperwork.</p>
            <div class="hero-cta">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg"><i class="mdi mdi-rocket-launch"></i>Get Started</a>
                <a href="#features" class="btn btn-outline btn-lg"><i class="mdi mdi-play-circle-outline"></i>See Features</a>
            </div>
            <div class="hero-trust">
                <div class="dots">
                    <span>D</span><span>N</span><span>R</span><span>+</span>
                </div>
                <span>Doctors, nurses & receptionists working in sync — daily.</span>
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-card">
                <div class="hero-card-header">
                    <strong><i class="mdi mdi-monitor-dashboard text-primary"></i> Today at HQ</strong>
                    <span class="pill"><i class="mdi mdi-circle" style="font-size:8px"></i> Live</span>
                </div>
                <div class="hero-stat-row">
                    <div class="hero-stat-cell b">
                        <div class="label">Waiting</div>
                        <div class="num">7</div>
                    </div>
                    <div class="hero-stat-cell g">
                        <div class="label">Serving</div>
                        <div class="num">2</div>
                    </div>
                    <div class="hero-stat-cell o">
                        <div class="label">Done</div>
                        <div class="num">23</div>
                    </div>
                </div>
                <div class="hero-queue-list">
                    <div class="row">
                        <span><span class="qnum priority">⭐ A001</span> &nbsp;Aisyah Binti Ahmad</span>
                        <span class="status serving">Serving</span>
                    </div>
                    <div class="row">
                        <span><span class="qnum">W008</span> &nbsp;Lim Wei Ming</span>
                        <span class="status waiting">Waiting</span>
                    </div>
                    <div class="row">
                        <span><span class="qnum">W009</span> &nbsp;Raj Kumar</span>
                        <span class="status waiting">Waiting</span>
                    </div>
                    <div class="row">
                        <span><span class="qnum">W010</span> &nbsp;Tan Mei Lin</span>
                        <span class="status waiting">Waiting</span>
                    </div>
                </div>
            </div>
            <div class="floating-icon floating-1">
                <i class="mdi mdi-check-circle" style="color:#10b981"></i>
                <span>Invoice paid</span>
            </div>
            <div class="floating-icon floating-2">
                <i class="mdi mdi-whatsapp" style="color:#25d366"></i>
                <span>WhatsApp sent</span>
            </div>
        </div>
    </div>
</section>

{{-- =================== TRUST BAR =================== --}}
<div class="trust-bar">
    <div class="trust-bar-inner">
        <div class="trust-item">
            <div class="num">9</div>
            <div class="label">Staff Roles</div>
        </div>
        <div class="trust-item">
            <div class="num">15+</div>
            <div class="label">Modules</div>
        </div>
        <div class="trust-item">
            <div class="num">100%</div>
            <div class="label">Audit Trail</div>
        </div>
        <div class="trust-item">
            <div class="num">24/7</div>
            <div class="label">Available</div>
        </div>
    </div>
</div>

{{-- =================== FEATURES =================== --}}
<section class="section" id="features">
    <div class="section-inner">
        <div class="section-head">
            <span class="eyebrow">Features</span>
            <h2>Everything your clinic needs.</h2>
            <p>Patients flow through your clinic without dropping context. One system, one source of truth.</p>
        </div>
        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon fi-blue"><i class="mdi mdi-account-multiple"></i></div>
                <h3>Patient & Appointments</h3>
                <p>IC-based registration, appointment booking, recurring schedules, and walk-in queue (Nombor Giliran) with TV display.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon fi-green"><i class="mdi mdi-stethoscope"></i></div>
                <h3>Consultation & Records</h3>
                <p>Full clinical encounter: vitals, diagnosis, prescriptions, lab orders, MC issuance, treatment plans, and referrals.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon fi-amber"><i class="mdi mdi-pill"></i></div>
                <h3>Pharmacy & Inventory</h3>
                <p>Suppliers, purchase orders, stock transfers between branches, expiry tracking, and dispensing with auto-deduction.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon fi-purple"><i class="mdi mdi-receipt"></i></div>
                <h3>Billing & Payments</h3>
                <p>Auto-invoice from consultation, cash/card/online payments via Billplz, receipt PDFs, and insurance panel claims.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon fi-pink"><i class="mdi mdi-card-account-details"></i></div>
                <h3>Membership & Packages</h3>
                <p>Tiered memberships with auto-discount, family-member sharing, priority queue, service packages, and subscriptions.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon fi-teal"><i class="mdi mdi-whatsapp"></i></div>
                <h3>WhatsApp Reminders</h3>
                <p>Send appointment reminders via OnSend.io, WhatsApp Cloud API, Fonnte, or Wassenger — fully configurable in settings.</p>
            </div>
        </div>
    </div>
</section>

{{-- =================== HOW IT WORKS =================== --}}
<section class="section" id="how-it-works" style="background:#f8fafc;">
    <div class="section-inner">
        <div class="section-head">
            <span class="eyebrow">Workflow</span>
            <h2>From walk-in to paid in 5 steps.</h2>
            <p>Each role plays its part. The system tracks the rest.</p>
        </div>
        <div class="steps">
            <div class="step">
                <div class="step-num">1</div>
                <h4>Check In</h4>
                <p>Receptionist registers patient or checks in their appointment — gets a queue number.</p>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <h4>Consult</h4>
                <p>Doctor records vitals, diagnosis, treatment plan, prescription, lab order, or MC.</p>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <h4>Dispense</h4>
                <p>Pharmacist dispenses medicines — stock auto-deducts, audit log auto-writes.</p>
            </div>
            <div class="step">
                <div class="step-num">4</div>
                <h4>Bill & Pay</h4>
                <p>Cashier creates invoice (pre-filled), accepts cash, card, or Billplz online payment.</p>
            </div>
        </div>
    </div>
</section>

{{-- =================== SECURITY / TRUST =================== --}}
<section class="section trust-section" id="security">
    <div class="section-inner">
        <div class="section-head">
            <span class="eyebrow">Trust & Security</span>
            <h2>Your data, properly handled.</h2>
            <p>Built with audit trails, role-based access, and patient privacy at the core.</p>
        </div>
        <div class="trust-grid">
            <div class="trust-card">
                <i class="mdi mdi-shield-lock" style="color:#0369a1"></i>
                <div>
                    <h4>Role-Based Access</h4>
                    <p>9 distinct roles — admin, doctor, nurse, pharmacist, receptionist, sales, locum, patient. Everyone sees only what they should.</p>
                </div>
            </div>
            <div class="trust-card">
                <i class="mdi mdi-history" style="color:#047857"></i>
                <div>
                    <h4>Full Audit Trail</h4>
                    <p>Every create, update, and delete on key models logged with old/new values, IP, user agent — admin-only visibility.</p>
                </div>
            </div>
            <div class="trust-card">
                <i class="mdi mdi-lock" style="color:#b45309"></i>
                <div>
                    <h4>Patient Portal</h4>
                    <p>Patients log in via IC + password to view their own appointments, lab reports, prescriptions, and invoices.</p>
                </div>
            </div>
            <div class="trust-card">
                <i class="mdi mdi-database-check" style="color:#6d28d9"></i>
                <div>
                    <h4>Multi-Branch Scoping</h4>
                    <p>Branch-aware data: every patient, queue, invoice, claim is scoped to its branch with a session-based switcher.</p>
                </div>
            </div>
            <div class="trust-card">
                <i class="mdi mdi-bell-ring" style="color:#be185d"></i>
                <div>
                    <h4>In-App Notifications</h4>
                    <p>Real-time bell notifications for new appointments, payments, claim status, low stock, and lab completions.</p>
                </div>
            </div>
            <div class="trust-card">
                <i class="mdi mdi-cloud-check" style="color:#0f766e"></i>
                <div>
                    <h4>Reliable Infrastructure</h4>
                    <p>Built on Laravel — battle-tested PHP framework powering thousands of healthcare and SaaS apps worldwide.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =================== CTA =================== --}}
<section class="cta">
    <div style="max-width:680px;margin:0 auto">
        <h2>Ready to streamline your clinic?</h2>
        <p>Sign in to ClinicQo and get your team running on a single, audit-ready platform.</p>
        <div class="hero-cta" style="justify-content:center">
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg"><i class="mdi mdi-login"></i>Staff Sign In</a>
            <a href="{{ route('portal.login') }}" class="btn btn-outline btn-lg"><i class="mdi mdi-account"></i>Patient Portal</a>
        </div>
    </div>
</section>

{{-- =================== FOOTER =================== --}}
<footer>
    <div class="footer-inner">
        <div class="footer-logo">
            <img src="{{ asset('images/clinicQo.png') }}" alt="ClinicQo">
            <p>Multi-branch clinic management for Malaysia. Patients, queue, consultation, billing, insurance — done.</p>
        </div>
        <div class="footer-col">
            <h5>Product</h5>
            <a href="#features">Features</a>
            <a href="#how-it-works">Workflow</a>
            <a href="#security">Security</a>
        </div>
        <div class="footer-col">
            <h5>Access</h5>
            <a href="{{ route('login') }}">Staff Login</a>
            <a href="{{ route('portal.login') }}">Patient Portal</a>
            <a href="{{ route('locum-portal.login') }}">Locum Portal</a>
        </div>
        <div class="footer-col">
            <h5>Support</h5>
            <a href="mailto:support@clinicqo.my">Contact Support</a>
            <a href="#">Documentation</a>
            <a href="#">Status</a>
        </div>
    </div>
    <div class="footer-bottom">
        <span>© {{ date('Y') }} ClinicQo. All rights reserved.</span>
        <span>Built with <i class="mdi mdi-heart" style="color:#ef4444"></i> in Malaysia</span>
    </div>
</footer>

</body>
</html>
