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
                        <span class="text-6xl">🔐</span>
                    </div>
                    <h2 class="text-4xl font-bold text-white mb-2 text-glow">
                        Nueva Contraseña
                    </h2>
                    <p class="text-lg text-blue-300">
                        Establece tu nueva contraseña
                    </p>
                </div>

                <!-- Tarjeta de reset -->
                <div class="bg-white/95 backdrop-blur-xl rounded-2xl p-8 shadow-pro">
                    <x-validation-errors class="mb-4 text-center text-red-500" />

                    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <!-- Email -->
                        <div class="input-group">
                            <label for="email" class="text-gray-700 text-sm font-medium mb-1 block">
                                Correo Institucional
                            </label>
                            <div class="relative">
                                <input id="email" name="email" type="email" required
                                       class="pro-input"
                                       :value="old('email', $request->email)"
                                       placeholder="profesor@escuela.edu">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <!-- Contraseñas en grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="input-group">
                                <label for="password" class="text-gray-700 text-sm font-medium mb-1 block">
                                    Nueva Contraseña
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

                            <div class="input-group">
                                <label for="password_confirmation" class="text-gray-700 text-sm font-medium mb-1 block">
                                    Confirmar
                                </label>
                                <div class="relative">
                                    <input id="password_confirmation" name="password_confirmation" type="password" required
                                           class="pro-input"
                                           placeholder="••••••••">
                                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="pro-button mt-6">
                            <span class="relative z-10">Restablecer Contraseña</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Los mismos estilos que login.blade.php */
    </style>
</x-guest-layout>
