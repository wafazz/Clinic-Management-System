<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Medical Certificate - {{ $consultation->consultation_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; color: #000; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 15px; }
        .header h1 { margin: 0; }
        .title { text-align: center; font-size: 22px; font-weight: bold; text-decoration: underline; margin: 30px 0; letter-spacing: 3px; }
        .content { font-size: 15px; line-height: 1.8; }
        .row { display: flex; margin-bottom: 8px; }
        .label { width: 180px; font-weight: bold; }
        .value { flex: 1; border-bottom: 1px dotted #000; padding: 0 8px; }
        .signature { margin-top: 80px; text-align: right; }
        .sig-line { display: inline-block; width: 280px; border-top: 1px solid #000; padding-top: 5px; margin-top: 60px; }
        @media print { body { padding: 20px; } .no-print { display:none; } }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:right;margin-bottom:10px"><button onclick="window.print()">Print</button></div>

    <div class="header">
        <h1>{{ $consultation->branch->name ?? 'Clinic' }}</h1>
        <p>{{ $consultation->branch->address ?? '' }}</p>
        <p>Tel: {{ $consultation->branch->phone ?? '' }} | Email: {{ $consultation->branch->email ?? '' }}</p>
    </div>

    <div class="title">MEDICAL CERTIFICATE</div>

    <div class="content">
        <div class="row"><div class="label">MC No.</div><div class="value">{{ $consultation->consultation_number }}</div></div>
        <div class="row"><div class="label">Date Issued</div><div class="value">{{ $consultation->created_at->format('d F Y') }}</div></div>
        <div class="row"><div class="label">Patient Name</div><div class="value">{{ $consultation->patient->name }}</div></div>
        <div class="row"><div class="label">IC / Patient ID</div><div class="value">{{ $consultation->patient->ic_number ?? $consultation->patient->patient_id }}</div></div>

        <p style="margin-top:30px">This is to certify that the above-named patient is unfit for work / school for a period of</p>
        <p style="font-size:18px;text-align:center;margin:25px 0">
            <strong>{{ $consultation->mc_days }} day(s)</strong>
        </p>
        <p>from <strong>{{ $consultation->mc_from?->format('d F Y') }}</strong> to <strong>{{ $consultation->mc_to?->format('d F Y') }}</strong> (both dates inclusive).</p>

        @if($consultation->mc_reason)
            <p style="margin-top:20px"><strong>Reason:</strong> {{ $consultation->mc_reason }}</p>
        @endif
    </div>

    <div class="signature">
        <div class="sig-line">
            <strong>Dr. {{ $consultation->doctor->user->name }}</strong><br>
            <small>{{ $consultation->doctor->mmc_number ? 'MMC: ' . $consultation->doctor->mmc_number : '' }}</small>
        </div>
    </div>
</body>
</html>
