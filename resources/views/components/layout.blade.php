<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JRCZ Extraction Pipeline</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body style="background-color: #080b12; margin: 0;">
    {{ $slot }}
</body>
</html>