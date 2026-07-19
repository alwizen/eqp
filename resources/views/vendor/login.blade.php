<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Portal Login</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f7fb; margin: 0; padding: 40px; }
        .box { max-width: 480px; margin: 0 auto; background: white; border-radius: 12px; padding: 28px; box-shadow: 0 8px 24px rgba(0,0,0,.08); }
        .title { margin-bottom: 8px; font-size: 24px; }
        .subtitle { color: #667085; margin-bottom: 24px; }
        label { display: block; font-weight: 700; margin-bottom: 8px; }
        input { width: 100%; padding: 12px; border: 1px solid #d0d5dd; border-radius: 8px; margin-bottom: 14px; }
        button { width: 100%; background: #2563eb; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 700; }
        .error { color: #b42318; margin-bottom: 12px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="box">
        <div class="title">Vendor Portal</div>
        <div class="subtitle">Login sederhana menggunakan nomor telepon vendor.</div>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('vendor.login.post') }}">
            @csrf
            <label for="phone">Nomor Telepon</label>
            <input id="phone" name="phone" type="text" placeholder="Contoh: 0812-3456-7890" value="{{ old('phone') }}" required>
            <button type="submit">Masuk Portal Vendor</button>
        </form>
    </div>
</body>
</html>
