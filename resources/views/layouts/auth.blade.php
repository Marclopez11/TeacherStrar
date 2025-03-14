<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SanClass</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body>
        <div class="min-h-screen relative overflow-hidden bg-gradient-to-br from-[#1a2b4b] to-[#182235]">
            <!-- Elementos flotantes -->
            <div class="floating-elements">
                <div class="element">📚</div>
                <div class="element">🎓</div>
                <div class="element">✏️</div>
                <div class="element">📝</div>
                <div class="element">🔬</div>
                <div class="element">🌍</div>
            </div>

            <div class="min-h-screen flex items-center justify-center px-4 relative z-10">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
