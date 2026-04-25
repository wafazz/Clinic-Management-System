<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Display - {{ $branchName }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #1a1a2e;
            color: #fff;
            height: 100vh;
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 { font-size: 28px; font-weight: 700; }
        .header .date { font-size: 18px; opacity: 0.9; }
        .content {
            display: flex;
            height: calc(100vh - 80px);
        }
        .now-serving {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .now-serving .label {
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #ffd93d;
            margin-bottom: 20px;
        }
        .now-serving .number {
            font-size: 120px;
            font-weight: 900;
            color: #ffd93d;
            text-shadow: 0 0 30px rgba(255, 217, 61, 0.5);
            line-height: 1;
            animation: pulse 2s ease-in-out infinite;
        }
        .now-serving .patient-info {
            font-size: 28px;
            margin-top: 15px;
            color: #e0e0e0;
        }
        .now-serving .doctor-info {
            font-size: 18px;
            margin-top: 8px;
            color: #a0a0c0;
        }
        .serving-list {
            margin-top: 30px;
            width: 100%;
        }
        .serving-item {
            background: rgba(255, 217, 61, 0.1);
            border: 1px solid rgba(255, 217, 61, 0.3);
            border-radius: 12px;
            padding: 20px 30px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .serving-item .s-number {
            font-size: 48px;
            font-weight: 900;
            color: #ffd93d;
        }
        .serving-item .s-info {
            text-align: right;
        }
        .serving-item .s-name { font-size: 20px; }
        .serving-item .s-doctor { font-size: 14px; color: #a0a0c0; }
        .waiting-panel {
            width: 350px;
            background: rgba(255,255,255,0.05);
            border-left: 1px solid rgba(255,255,255,0.1);
            overflow-y: auto;
        }
        .waiting-header {
            padding: 20px;
            text-align: center;
            background: rgba(255,255,255,0.05);
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #a0c4ff;
            position: sticky;
            top: 0;
        }
        .waiting-item {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .waiting-item .w-number {
            font-size: 24px;
            font-weight: 700;
            color: #a0c4ff;
            min-width: 70px;
        }
        .waiting-item .w-name {
            font-size: 16px;
            color: #ccc;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 18px;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
        .clock {
            font-size: 18px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $branchName ?: 'Clinic' }}</h1>
        <div>
            <div class="date">{{ \Carbon\Carbon::parse($today)->format('l, d F Y') }}</div>
            <div class="clock" id="clock"></div>
        </div>
    </div>

    <div class="content" id="display-content">
        <div class="now-serving">
            @if($currentServing->count() > 0)
                <div class="label">Now Serving</div>
                @if($currentServing->count() === 1)
                    @php $first = $currentServing->first(); @endphp
                    <div class="number">{{ $first->queue_number }}</div>
                    <div class="patient-info">{{ $first->patient_name }}</div>
                    @if($first->doctor)
                        <div class="doctor-info">Dr. {{ $first->doctor->user->name }}</div>
                    @endif
                @else
                    <div class="serving-list">
                        @foreach($currentServing as $serving)
                            <div class="serving-item">
                                <div class="s-number">{{ $serving->queue_number }}</div>
                                <div class="s-info">
                                    <div class="s-name">{{ $serving->patient_name }}</div>
                                    @if($serving->doctor)
                                        <div class="s-doctor">Dr. {{ $serving->doctor->user->name }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="no-data">
                    <div style="font-size:60px;margin-bottom:20px;opacity:0.3">---</div>
                    <div>No patient being served</div>
                </div>
            @endif
        </div>

        <div class="waiting-panel">
            <div class="waiting-header">Waiting ({{ $waiting->count() }})</div>
            @forelse($waiting as $w)
                <div class="waiting-item">
                    <div class="w-number">{{ $w->queue_number }}</div>
                    <div class="w-name">{{ $w->patient_name }}</div>
                </div>
            @empty
                <div class="no-data" style="font-size:14px;padding:20px">No patients waiting</div>
            @endforelse
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent = now.toLocaleTimeString('en-MY', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
        updateClock();
        setInterval(updateClock, 1000);

        // Auto-refresh every 10 seconds
        setTimeout(function() { location.reload(); }, 10000);
    </script>
</body>
</html>
