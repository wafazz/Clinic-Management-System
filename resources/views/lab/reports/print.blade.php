<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lab Report - {{ $labReport->report_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; }
        .info-grid dt { color: #666; font-size: 10px; }
        .info-grid dd { margin: 0; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f5f5f5; font-size: 10px; text-transform: uppercase; }
        .abnormal { color: red; font-weight: bold; }
        .footer { margin-top: 40px; font-size: 10px; color: #666; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>{{ $labReport->branch->name ?? 'Clinic' }}</h1>
        <p>{{ $labReport->branch->address ?? '' }}</p>
        <p>Tel: {{ $labReport->branch->phone ?? '' }}</p>
    </div>

    <h2 style="text-align:center;">Laboratory Report</h4>

    <div class="info-grid">
        <div><dt>Report Number</dt><dd>{{ $labReport->report_number }}</dd></div>
        <div><dt>Date</dt><dd>{{ $labReport->reported_at?->format('d M Y H:i') ?? $labReport->created_at->format('d M Y') }}</dd></div>
        <div><dt>Patient</dt><dd>{{ $labReport->patient->name }} ({{ $labReport->patient->patient_id }})</dd></div>
        <div><dt>Referring Doctor</dt><dd>Dr. {{ $labReport->doctor->user->name ?? '-' }}</dd></div>
    </div>

    <table>
        <thead><tr>
            <th>Test</th>
            <th>Result</th>
            <th>Normal Range</th>
            <th>Unit</th>
            <th>Remarks</th>
        </tr></thead>
        <tbody>
            @foreach($labReport->items as $item)
                <tr>
                    <td>{{ $item->test->name }}</td>
                    <td class="{{ $item->is_abnormal ? 'abnormal' : '' }}">{{ $item->result ?? '-' }}</td>
                    <td>{{ $item->test->normal_range ?? '-' }}</td>
                    <td>{{ $item->test->unit ?? '-' }}</td>
                    <td>{{ $item->is_abnormal ? 'ABNORMAL' : '' }} {{ $item->notes }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($labReport->notes)
        <p><strong>Notes:</strong> {{ $labReport->notes }}</p>
    @endif

    <div class="footer">
        <p>This is a computer-generated report. Printed on {{ now()->format('d M Y H:i') }}</p>
    </div>
</body>
</html>
