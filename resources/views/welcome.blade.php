<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Portal Sandra</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
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

            <!-- Navbar -->
            <nav class="fixed w-full z-50 bg-white/10 backdrop-blur-lg border-b border-white/20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16 items-center">
                        <div class="flex items-center space-x-2">
                            <span class="text-3xl">🏫</span>
                            <span class="text-2xl font-bold text-white">Portal Sandra</span>
                        </div>

                        @if (Route::has('login'))
                            <div class="flex items-center space-x-4">
                                @auth
                                    <a href="{{ url('/dashboard') }}"
                                       class="pro-button">
                                        Panel de Control
                                    </a>
                                @else
                                    <a href="{{ route('login') }}"
                                       class="pro-button-outline">
                                        Iniciar Sesión
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}"
                                           class="pro-button">
                                            Registrarse
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            </nav>

            <!-- Hero Section -->
            <div class="relative min-h-screen flex items-center">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                        <!-- Texto principal -->
                        <div class="text-center md:text-left space-y-6">
                            <h1 class="text-5xl md:text-6xl font-bold text-white text-glow">
                                Bienvenido al Portal Sandra
                            </h1>
                            <p class="text-xl text-blue-300">
                                Un espacio diseñado para facilitar la gestión educativa y mejorar la experiencia de aprendizaje.
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                                @auth
                                    <a href="{{ url('/dashboard') }}"
                                       class="pro-button">
                                        Ir al Panel
                                    </a>
                                @else
                                    <a href="{{ route('register') }}"
                                       class="pro-button">
                                        Registrarse
                                    </a>
                                    <a href="{{ route('login') }}"
                                       class="pro-button-secondary">
                                        Iniciar Sesión
                                    </a>
                                @endauth
                            </div>
                        </div>

                        <!-- Imagen o ilustración -->
                        <div class="hidden md:block relative">
                            <div class="aspect-square rounded-2xl bg-white/10 backdrop-blur-xl p-8 transform rotate-3 hover:rotate-0 transition-all duration-300">
                                <div class="grid grid-cols-2 gap-4 h-full">
                                    <div class="space-y-4">
                                        <div class="h-1/2 bg-white/20 rounded-lg flex items-center justify-center text-6xl">
                                            👨‍🏫
                                        </div>
                                        <div class="h-1/2 bg-white/20 rounded-lg flex items-center justify-center text-6xl">
                                            📊
                                        </div>
                                    </div>
                                    <div class="space-y-4">
                                        <div class="h-1/2 bg-white/20 rounded-lg flex items-center justify-center text-6xl">
                                            📝
                                        </div>
                                        <div class="h-1/2 bg-white/20 rounded-lg flex items-center justify-center text-6xl">
                                            🎯
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            /* Los mismos estilos que login.blade.php */
            .floating-elements {
                position: absolute;
                width: 100%;
                height: 100%;
                pointer-events: none;
                z-index: 1;
            }

            .element {
                position: absolute;
                font-size: 3rem;
                opacity: 0.8;
                animation: float 10s infinite;
                filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.3));
                transition: all 0.3s ease;
            }

            .element:nth-child(1) { top: 15%; left: 10%; animation-delay: 0s; font-size: 3.5rem; }
            .element:nth-child(2) { top: 65%; left: 15%; animation-delay: 2s; font-size: 4rem; }
            .element:nth-child(3) { top: 25%; right: 12%; animation-delay: 4s; font-size: 3rem; }
            .element:nth-child(4) { top: 70%; right: 15%; animation-delay: 6s; font-size: 3.2rem; }
            .element:nth-child(5) { top: 45%; left: 20%; animation-delay: 8s; font-size: 3.8rem; }
            .element:nth-child(6) { top: 35%; right: 20%; animation-delay: 10s; font-size: 4rem; }

            @keyframes float {
                0% { transform: translateY(0) rotate(0deg) scale(1); }
                50% { transform: translateY(-20px) rotate(10deg) scale(1.1); }
                100% { transform: translateY(0) rotate(0deg) scale(1); }
            }

            .text-glow {
                text-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
            }

            .pro-button {
                padding: 0.875rem 2rem;
                background: linear-gradient(45deg, #3B82F6, #2563EB);
                border: none;
                border-radius: 8px;
                color: white;
                font-weight: 600;
                position: relative;
                overflow: hidden;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .pro-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
            }

            .pro-button-outline {
                padding: 0.875rem 2rem;
                background: transparent;
                border: 2px solid rgba(255, 255, 255, 0.2);
                border-radius: 8px;
                color: white;
                font-weight: 600;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .pro-button-outline:hover {
                background: rgba(255, 255, 255, 0.1);
                border-color: rgba(255, 255, 255, 0.3);
                transform: translateY(-2px);
            }

            .pro-button-secondary {
                padding: 0.875rem 2rem;
                background: rgba(255, 255, 255, 0.1);
                border: 2px solid rgba(255, 255, 255, 0.2);
                border-radius: 8px;
                color: white;
                font-weight: 600;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .pro-button-secondary:hover {
                background: rgba(255, 255, 255, 0.2);
                transform: translateY(-2px);
            }
        </style>
    </body>
</html>
