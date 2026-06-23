<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; }
    </style>
</head>
<body>
    <div class="container text-center text-white py-5">
        <i class="bi bi-tools display-1 mb-4 d-block opacity-75"></i>
        <h1 class="fw-bold mb-3">Under Maintenance</h1>
        <p class="lead opacity-75 mb-0">{{ \App\Models\Setting::get('maintenance_message', 'We\'ll be back shortly. Please check back later.') }}</p>
    </div>
</body>
</html>
