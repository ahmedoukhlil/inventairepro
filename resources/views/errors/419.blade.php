<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Session expirée</title>
    <meta http-equiv="refresh" content="0;url={{ route('login') }}">
</head>
<body>
    <script>window.location.href = '{{ route('login') }}';</script>
</body>
</html>
