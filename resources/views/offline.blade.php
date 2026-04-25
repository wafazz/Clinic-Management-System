<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Offline — ClinicQo</title>
    <link rel="icon" type="image/png" href="/images/clinicQo.png">
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#0ea5e9">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap">
    <link rel="stylesheet" href="/star-admin/vendors/iconfonts/mdi/css/materialdesignicons.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', system-ui, sans-serif;
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 50%, #10b981 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: #fff;
            border-radius: 20px;
            padding: 50px 40px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
        }
        .card img { height: 50px; margin-bottom: 24px; }
        .icon-circle {
            width: 96px; height: 96px; border-radius: 50%;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #b45309;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 3rem; margin: 0 auto 24px;
        }
        h1 { margin: 0 0 12px; font-size: 1.6rem; font-weight: 800; color: #0f172a; }
        p { color: #64748b; margin: 0 0 28px; line-height: 1.6; }
        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 24px; border-radius: 10px;
            background: linear-gradient(135deg, #0ea5e9, #06b6d4); color: #fff;
            font-weight: 600; text-decoration: none; border: none; cursor: pointer;
            font-size: 0.95rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 8px 22px rgba(14, 165, 233, 0.4); }
        .tip {
            background: #f0f9ff; border-left: 3px solid #0ea5e9;
            padding: 14px; border-radius: 8px; margin-top: 24px;
            text-align: left; font-size: 0.85rem; color: #475569;
        }
        .tip strong { color: #0369a1; }
    </style>
</head>
<body>
    <div class="card">
        <img src="/images/clinicQo.png" alt="ClinicQo">
        <div class="icon-circle"><i class="mdi mdi-wifi-off"></i></div>
        <h1>You're offline</h1>
        <p>It looks like you've lost your internet connection. Please check your network and try again.</p>
        <button class="btn" onclick="location.reload()"><i class="mdi mdi-refresh"></i>Try Again</button>
        <div class="tip">
            <strong><i class="mdi mdi-lightbulb-on-outline"></i> Tip:</strong> Pages you've visited recently are still available even when offline. Try going back to your dashboard or last viewed page.
        </div>
    </div>
    <script>
        window.addEventListener('online', () => location.reload());
    </script>
</body>
</html>
