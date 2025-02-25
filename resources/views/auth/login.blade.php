<x-guest-layout>
    <div class="min-h-screen relative overflow-hidden bg-gradient-to-br from-[#1a2b4b] to-[#182235]">
        <!-- Elementos flotantes relacionados con la educación -->
        <div class="floating-elements">
            <div class="element">📚</div>
            <div class="element">🎓</div>
            <div class="element">✏️</div>
            <div class="element">📝</div>
            <div class="element">🔬</div>
            <div class="element">🌍</div>
        </div>

        <div class="min-h-screen flex items-center justify-center px-4 relative z-10">
            <div class="max-w-md w-full">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="mb-6 teacher-icon">
                        <span class="text-6xl">👨‍🏫</span>
                    </div>
                    <h2 class="text-4xl font-bold text-white mb-2 text-glow">
                        SanClass
                    </h2>
                    <p class="text-lg text-blue-300">
                        Accede a tu espacio educativo
                    </p>
                </div>

                <!-- Tarjeta de login -->
                <div class="bg-white/95 backdrop-blur-xl rounded-2xl p-8 shadow-pro">
                    <x-validation-errors class="mb-4 text-center text-red-500" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf
                        <!-- Email -->
                        <div class="input-group">
                            <label for="email" class="text-gray-700 text-sm font-medium mb-2 block">
                                Correo Institucional
                            </label>
                            <div class="relative">
                                <input id="email" name="email" type="email" required
                                       class="pro-input"
                                       :value="old('email')"
                                       placeholder="profesor@escuela.edu">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <!-- Contraseña -->
                        <div class="input-group">
                            <label for="password" class="text-gray-700 text-sm font-medium mb-2 block">
                                Contraseña
                            </label>
                            <div class="relative">
                                <input id="password" name="password" type="password" required
                                       class="pro-input"
                                       placeholder="••••••••">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <!-- Recordarme -->
                        <div class="flex items-center justify-between">
                            <label class="pro-checkbox">
                                <input type="checkbox" name="remember" class="form-checkbox h-4 w-4 text-blue-500 rounded border-gray-300">
                                <span class="checkbox-label text-gray-600">Mantener sesión iniciada</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="text-sm text-blue-600 hover:text-blue-800 transition-colors">
                                    ¿Olvidó su contraseña?
                                </a>
                            @endif
                        </div>

                        <!-- Botón de ingreso -->
                        <button type="submit" class="pro-button">
                            <span class="relative z-10">Ingresar al Sistema</span>
                        </button>
                    </form>

                    <!-- Ayuda -->
                    <div class="mt-6 text-center text-sm text-gray-600">
                        <p>¿Necesita ayuda? Contacte al soporte técnico</p>
                        <p class="mt-2 text-blue-600">📧 soporte@escuela.edu</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
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
            0% { transform: translateY(0) rotate(0deg) scale(1); filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.3)); }
            50% { transform: translateY(-20px) rotate(10deg) scale(1.1); filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.5)); }
            100% { transform: translateY(0) rotate(0deg) scale(1); filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.3)); }
        }

        .element:hover {
            opacity: 1;
            transform: scale(1.2);
            filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.6));
        }

        .teacher-icon {
            animation: teacherBounce 2s infinite;
        }

        @keyframes teacherBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .shadow-pro {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .pro-input {
            width: 100%;
            padding: 0.75rem 0.75rem 0.75rem 3rem;
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            color: #1F2937;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .pro-input:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .pro-input::placeholder {
            color: #9CA3AF;
        }

        .pro-button {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(45deg, #3B82F6, #2563EB);
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .pro-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                to right,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            transition: 0.5s;
        }

        .pro-button:hover::before {
            left: 100%;
        }

        .pro-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .text-glow {
            text-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
        }

        /* Animación de entrada */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .input-group {
            animation: fadeInUp 0.5s ease-out forwards;
        }
    </style>
</x-guest-layout>
