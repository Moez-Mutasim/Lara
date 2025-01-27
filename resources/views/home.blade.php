<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
</head>
<body>
    <h1>{{ $title }}</h1>

    @if($is_guest)
        <p>Welcome, Guest!</p>
    @else
        <p>Welcome, {{ $user['name'] ?? 'User' }}!</p> <!-- Safely access name -->
    @endif

    <h2>Available Features</h2>
    <ul>
        @foreach($features as $feature => $url)
            <li><a href="{{ $url }}">{{ ucfirst($feature) }}</a></li>
        @endforeach
    </ul>
</body>
</html>
