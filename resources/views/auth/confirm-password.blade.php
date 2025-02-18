<x-guest-layout>
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
            <div class="max-w-md w-full space-y-6">
                <!-- Header -->
                <div class="text-center">
                    <div class="mb-6 teacher-icon">
                        <span class="text-6xl">🔒</span>
                    </div>
                    <h2 class="text-4xl font-bold text-white mb-2 text-glow">
                        Confirmar Contraseña
                    </h2>
                    <p class="text-lg text-blue-300">
                        Por seguridad, confirma tu contraseña para continuar
                    </p>
                </div>

                <!-- Tarjeta de confirmación -->
                <div class="bg-white/95 backdrop-blur-xl rounded-2xl p-8 shadow-pro">
                    <x-validation-errors class="mb-4 text-center text-red-500" />

                    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
                        @csrf

                        <div class="input-group">
                            <label for="password" class="text-gray-700 text-sm font-medium mb-1 block">
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

                        <button type="submit" class="pro-button mt-6">
                            <span class="relative z-10">Confirmar</span>
                        </button>
                    </form>

                    <div class="mt-4 text-center">
                        <a href="{{ route('password.request') }}" class="text-blue-600 hover:text-blue-800 transition-colors">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Los mismos estilos que login.blade.php */
    </style>
</x-guest-layout>
