<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Display — {{ $branchName ?: 'ClinicQo' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icon-32.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700;800;900&display=swap">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #0a0e1a;
            color: #fff;
            height: 100vh;
            overflow: hidden;
            user-select: none;
        }

        /* Animated background */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            z-index: 0;
            animation: float 12s ease-in-out infinite;
        }
        body::before {
            width: 500px; height: 500px;
            background: radial-gradient(circle, #0ea5e9, transparent);
            top: -150px; left: -150px;
        }
        body::after {
            width: 600px; height: 600px;
            background: radial-gradient(circle, #10b981, transparent);
            bottom: -200px; right: -200px;
            animation-delay: -6s;
        }
        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(40px, -40px); }
        }

        /* Header */
        .header {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 16px 36px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-left { display: flex; align-items: center; gap: 16px; }
        .header-left img { height: 40px; }
        .header-left .branch {
            font-size: 18px; font-weight: 600;
            letter-spacing: 0.02em;
        }
        .header-left .branch small {
            display: block;
            font-size: 11px;
            opacity: 0.6;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            font-weight: 500;
        }
        .header-right { text-align: right; display: flex; align-items: center; gap: 24px; }
        .clock-block {
            font-variant-numeric: tabular-nums;
        }
        .clock-block .time {
            font-size: 32px; font-weight: 800;
            line-height: 1;
        }
        .clock-block .date { font-size: 13px; opacity: 0.7; margin-top: 4px; }
        .stats-pill {
            display: flex; gap: 18px;
        }
        .stats-pill div {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 8px 16px;
            border-radius: 10px;
            text-align: center;
        }
        .stats-pill .num { font-size: 20px; font-weight: 800; line-height: 1; }
        .stats-pill .lbl { font-size: 10px; opacity: 0.6; text-transform: uppercase; letter-spacing: 0.1em; margin-top: 4px; }

        /* Content */
        .content {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr 380px;
            height: calc(100vh - 80px);
        }

        /* Now Serving panel */
        .now-serving {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            text-align: center;
            position: relative;
        }
        .now-serving-empty {
            opacity: 0.3;
            text-align: center;
        }
        .now-serving-empty i { font-size: 120px; display: block; margin-bottom: 16px; }
        .now-serving-empty p { font-size: 22px; }

        .single-serving { animation: fadeIn 0.5s ease; }
        .single-serving .label {
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 0.5em;
            color: #fbbf24;
            margin-bottom: 24px;
            font-weight: 600;
            opacity: 0.9;
        }
        .single-serving .label::before, .single-serving .label::after {
            content: ''; display: inline-block; width: 60px; height: 1px;
            background: #fbbf24; vertical-align: middle; margin: 0 16px; opacity: 0.5;
        }
        .single-serving .number {
            font-size: 200px;
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(135deg, #fbbf24, #f59e0b, #ef4444);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 80px rgba(251, 191, 36, 0.4);
            animation: pulseScale 2s ease-in-out infinite;
            letter-spacing: -0.02em;
        }
        .single-serving .priority-badge {
            display: inline-block;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 12px;
            letter-spacing: 0.05em;
            animation: bounce 1s ease infinite;
        }
        .single-serving .patient-info {
            font-size: 38px;
            margin-top: 24px;
            font-weight: 600;
            color: #fff;
        }
        .single-serving .doctor-info {
            font-size: 22px;
            margin-top: 12px;
            color: #94a3b8;
            font-weight: 500;
        }

        /* Multi-serving */
        .serving-list {
            width: 100%;
            max-width: 700px;
            margin-top: 30px;
        }
        .serving-list .label {
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.4em;
            color: #fbbf24;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }
        .serving-item {
            background: linear-gradient(135deg, rgba(251, 191, 36, 0.12), rgba(245, 158, 11, 0.08));
            border: 1px solid rgba(251, 191, 36, 0.3);
            border-radius: 16px;
            padding: 24px 32px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            backdrop-filter: blur(10px);
            animation: fadeIn 0.4s ease;
        }
        .serving-item .s-number {
            font-size: 64px;
            font-weight: 900;
            color: #fbbf24;
            line-height: 1;
        }
        .serving-item .s-info { text-align: right; }
        .serving-item .s-name { font-size: 22px; font-weight: 600; }
        .serving-item .s-doctor { font-size: 14px; color: #94a3b8; margin-top: 4px; }

        /* Waiting panel */
        .waiting-panel {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-left: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            flex-direction: column;
        }
        .waiting-header {
            padding: 22px 24px;
            background: rgba(14, 165, 233, 0.1);
            border-bottom: 1px solid rgba(14, 165, 233, 0.2);
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            color: #38bdf8;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .waiting-header .count {
            background: #0ea5e9;
            color: #fff;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 13px;
            min-width: 32px;
            text-align: center;
        }
        .waiting-list {
            flex: 1;
            overflow-y: auto;
            padding: 12px 0;
        }
        .waiting-list::-webkit-scrollbar { width: 4px; }
        .waiting-list::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 2px; }
        .waiting-item {
            padding: 16px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: background 0.2s;
            animation: slideIn 0.3s ease;
        }
        .waiting-item:hover { background: rgba(255, 255, 255, 0.03); }
        .waiting-item .w-pos {
            width: 28px; height: 28px; border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700;
            color: #94a3b8;
            flex-shrink: 0;
        }
        .waiting-item .w-number {
            font-size: 24px;
            font-weight: 800;
            color: #38bdf8;
            min-width: 80px;
        }
        .waiting-item .w-name { font-size: 16px; color: #cbd5e1; flex: 1; }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #475569;
        }
        .no-data i { font-size: 64px; display: block; margin-bottom: 12px; opacity: 0.4; }

        /* Animations */
        @keyframes pulseScale {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.04); }
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Flash overlay when new number called */
        .flash-overlay {
            position: fixed;
            inset: 0;
            background: radial-gradient(ellipse at center, rgba(14, 165, 233, 0.6), transparent 70%);
            pointer-events: none;
            opacity: 0;
            z-index: 999;
            transition: opacity 0.3s;
        }
        .flash-overlay.show {
            animation: flash 1.5s ease;
        }
        @keyframes flash {
            0%, 100% { opacity: 0; }
            20% { opacity: 1; }
        }

        /* Sound enable overlay */
        .sound-overlay {
            position: fixed; inset: 0;
            background: rgba(10, 14, 26, 0.95);
            backdrop-filter: blur(20px);
            display: flex; align-items: center; justify-content: center;
            z-index: 1000;
            cursor: pointer;
        }
        .sound-overlay.hidden { display: none; }
        .sound-overlay-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 50px 60px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }
        .sound-overlay-card i { font-size: 72px; color: #fbbf24; display: block; margin-bottom: 16px; }
        .sound-overlay-card h2 { font-size: 28px; margin-bottom: 8px; }
        .sound-overlay-card p { color: #94a3b8; margin-bottom: 24px; }
        .sound-overlay-card button {
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            color: #fff; border: none;
            padding: 14px 36px;
            font-size: 16px; font-weight: 600;
            border-radius: 12px; cursor: pointer;
            transition: transform 0.2s;
        }
        .sound-overlay-card button:hover { transform: translateY(-2px); }

        /* Responsive */
        @media (max-width: 1024px) {
            .single-serving .number { font-size: 140px; }
            .single-serving .patient-info { font-size: 30px; }
            .content { grid-template-columns: 1fr 320px; }
        }
        @media (max-width: 768px) {
            .content { grid-template-columns: 1fr; grid-template-rows: 1fr auto; }
            .waiting-panel { max-height: 40vh; border-left: none; border-top: 1px solid rgba(255,255,255,0.08); }
            .single-serving .number { font-size: 100px; }
            .header { padding: 12px 20px; flex-wrap: wrap; gap: 12px; }
            .stats-pill { display: none; }
        }
    </style>
</head>
<body>
    {{-- Sound enable overlay (browsers block autoplay until user interaction) --}}
    <div class="sound-overlay" id="soundOverlay">
        <div class="sound-overlay-card">
            <i class="mdi mdi-volume-high"></i>
            <h2>Enable Live Announcements</h2>
            <p>Click below to enable voice announcements when patients are called.</p>
            <button onclick="enableSound()">Start Display</button>
        </div>
    </div>

    {{-- Flash overlay (shows when new patient called) --}}
    <div class="flash-overlay" id="flashOverlay"></div>

    <header class="header">
        <div class="header-left">
            <img src="{{ asset('images/clinicQo.png') }}" alt="ClinicQo">
            <div class="branch">
                {{ $branchName ?: 'ClinicQo' }}
                <small>Live Queue Display</small>
            </div>
        </div>
        <div class="header-right">
            <div class="stats-pill">
                <div>
                    <div class="num" id="stat-serving">{{ $currentServing->count() }}</div>
                    <div class="lbl">Serving</div>
                </div>
                <div>
                    <div class="num" id="stat-waiting">{{ $waiting->count() }}</div>
                    <div class="lbl">Waiting</div>
                </div>
            </div>
            <div class="clock-block">
                <div class="time" id="clock">--:--</div>
                <div class="date">{{ \Carbon\Carbon::parse($today)->format('l, d F Y') }}</div>
            </div>
        </div>
    </header>

    <div class="content">
        <div class="now-serving" id="nowServing">
            {{-- Populated by JS on load + polls --}}
        </div>

        <aside class="waiting-panel">
            <div class="waiting-header">
                <span><i class="mdi mdi-account-clock"></i> Waiting</span>
                <span class="count" id="waitingCount">{{ $waiting->count() }}</span>
            </div>
            <div class="waiting-list" id="waitingList">
                {{-- Populated by JS --}}
            </div>
        </aside>
    </div>

    <audio id="chimeAudio" preload="auto">
        <source src="data:audio/wav;base64,UklGRkoCAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YSYCAAB+f4CCgYJ/foB/foSDgYGFhICAgYGCgoF+f4CCfYCCfn+EgX5/g3+ChIB/g4SAgIaFgYWGgIaIgIeIfoaIfYaJfYWHfIWIfoeKgYqLg4qOhI2QhI6QhI+ShI6Sg42SgouRgYqRgIqRfomQfImPfIeOe4WMeoSLeYOLeYKLeoKLfIKMfYONf4SNf4WOgYWPgYePg4iQg4iRhImRhImQg4iQgoiPgoeOgYaNf4WMfYSLfIOKfIKKfYKKfoKLfoOLgIWMgIWNg4eOhIePhYePhoePh4iQiYqRiouSi4qSiYqRiYqRiIqQiImQh4mPhoiOhYeNhYaMhIaMg4WLgoSKgIOJfoKIfoKIfYKHfYGHfYKHfoKIfoOIfoOJf4SJgIWKgIWKgYaLgYaLgoaMgoeMg4eMg4iNg4iNhIiNg4iNhIiOg4iNg4iNg4iNg4eNg4eNg4eMg4eMg4eMg4eMgoeLgoeMgoeMg4eMg4iMg4iNg4mNg4mOg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mNg4mN" type="audio/wav">
    </audio>

    <script>
        let soundEnabled = false;
        let lastServingNumbers = {!! json_encode($currentServing->pluck('queue_number')->toArray()) !!};
        let pollTimer = null;

        function enableSound() {
            soundEnabled = true;
            // Test sound (silent unlock)
            const audio = document.getElementById('chimeAudio');
            audio.volume = 0;
            audio.play().catch(() => {});
            audio.volume = 1;
            document.getElementById('soundOverlay').classList.add('hidden');
            // Initial render
            fetchAndRender(false);
            // Start polling
            pollTimer = setInterval(() => fetchAndRender(true), 5000);
            // Try requesting fullscreen
            if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen().catch(() => {});
            }
        }

        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('clock').textContent = `${h}:${m}`;
        }
        updateClock();
        setInterval(updateClock, 30000);

        function speak(text) {
            if (!soundEnabled || !window.speechSynthesis) return;
            try {
                window.speechSynthesis.cancel();
                const utter = new SpeechSynthesisUtterance(text);
                utter.rate = 0.9;
                utter.pitch = 1;
                utter.volume = 1;
                utter.lang = 'en-MY';
                window.speechSynthesis.speak(utter);
            } catch (e) {}
        }

        function flash() {
            const el = document.getElementById('flashOverlay');
            el.classList.remove('show');
            void el.offsetWidth; // restart animation
            el.classList.add('show');
        }

        function chime() {
            if (!soundEnabled) return;
            const audio = document.getElementById('chimeAudio');
            audio.currentTime = 0;
            audio.play().catch(() => {});
        }

        function announceNew(items) {
            chime();
            flash();
            // Speak each new number
            const text = items.map(i =>
                `Number ${spell(i.queue_number)}, ${i.patient_name}, please proceed${i.doctor_name && i.doctor_name !== '-' ? ' to ' + i.doctor_name : ''}.`
            ).join(' ');
            // Slight delay so chime plays first
            setTimeout(() => speak(text), 800);
        }

        function spell(num) {
            // Spell out queue number letter-by-letter for clarity: A001 -> "A 0 0 1"
            return num.split('').join(' ');
        }

        function renderNowServing(items) {
            const wrap = document.getElementById('nowServing');
            if (!items || items.length === 0) {
                wrap.innerHTML = `
                    <div class="now-serving-empty">
                        <i class="mdi mdi-account-multiple-outline"></i>
                        <p>No patient currently being served</p>
                    </div>`;
                return;
            }
            if (items.length === 1) {
                const i = items[0];
                wrap.innerHTML = `
                    <div class="single-serving">
                        <div class="label">Now Serving</div>
                        ${i.is_priority ? '<div class="priority-badge"><i class="mdi mdi-star"></i> PRIORITY</div>' : ''}
                        <div class="number">${i.queue_number}</div>
                        <div class="patient-info">${escapeHtml(i.patient_name)}</div>
                        ${i.doctor_name && i.doctor_name !== '-' ? `<div class="doctor-info"><i class="mdi mdi-stethoscope"></i> ${escapeHtml(i.doctor_name)}</div>` : ''}
                    </div>`;
                return;
            }
            wrap.innerHTML = `
                <div class="serving-list">
                    <div class="label">Now Serving (${items.length})</div>
                    ${items.map(i => `
                        <div class="serving-item">
                            <div class="s-number">${i.queue_number}</div>
                            <div class="s-info">
                                <div class="s-name">${escapeHtml(i.patient_name)}</div>
                                ${i.doctor_name && i.doctor_name !== '-' ? `<div class="s-doctor">${escapeHtml(i.doctor_name)}</div>` : ''}
                            </div>
                        </div>
                    `).join('')}
                </div>`;
        }

        function renderWaiting(items) {
            const list = document.getElementById('waitingList');
            document.getElementById('waitingCount').textContent = items.length;
            document.getElementById('stat-waiting').textContent = items.length;
            if (!items || items.length === 0) {
                list.innerHTML = `<div class="no-data"><i class="mdi mdi-emoticon-happy-outline"></i><p>All caught up!</p></div>`;
                return;
            }
            list.innerHTML = items.map((w, idx) => `
                <div class="waiting-item">
                    <div class="w-pos">${idx + 1}</div>
                    <div class="w-number">${w.queue_number}</div>
                    <div class="w-name">${escapeHtml(w.patient_name)}</div>
                </div>`
            ).join('');
        }

        function escapeHtml(s) {
            return String(s ?? '').replace(/[&<>"']/g, c => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c]));
        }

        async function fetchAndRender(announceIfChanged) {
            try {
                const res = await fetch('{{ route('walk-in-queue.display-data') }}', { cache: 'no-store' });
                const data = await res.json();
                const serving = data.current_serving || [];
                const waiting = data.waiting || [];

                // Detect new numbers in serving (called since last poll)
                if (announceIfChanged) {
                    const currentNums = serving.map(s => s.queue_number);
                    const newOnes = serving.filter(s => !lastServingNumbers.includes(s.queue_number));
                    if (newOnes.length > 0) {
                        announceNew(newOnes);
                    }
                    lastServingNumbers = currentNums;
                }

                document.getElementById('stat-serving').textContent = serving.length;
                renderNowServing(serving);
                renderWaiting(waiting);
            } catch (e) {
                console.warn('Poll failed', e);
            }
        }

        // Initial render from server-side data (no poll yet — sound overlay still up)
        renderNowServing({!! json_encode($currentServing->map(fn($s) => [
            'queue_number' => $s->queue_number,
            'patient_name' => $s->patient_name,
            'doctor_name' => $s->doctor ? 'Dr. ' . $s->doctor->user->name : '-',
            'is_priority' => $s->is_priority ?? false,
        ])->values()) !!});
        renderWaiting({!! json_encode($waiting->map(fn($w) => [
            'queue_number' => $w->queue_number,
            'patient_name' => $w->patient_name,
        ])->values()) !!});
    </script>
</body>
</html>
