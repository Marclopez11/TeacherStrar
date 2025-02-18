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
                        <span class="text-6xl">✉️</span>
                    </div>
                    <h2 class="text-4xl font-bold text-white mb-2 text-glow">
                        Verifica tu Email
                    </h2>
                    <p class="text-lg text-blue-300">
                        Gracias por registrarte. Revisa tu email para verificar tu cuenta.
                    </p>
                </div>

                <!-- Tarjeta de verificación -->
                <div class="bg-white/95 backdrop-blur-xl rounded-2xl p-8 shadow-pro">
                    @if (session('status') == 'verification-link-sent')
                        <div class="mb-4 text-sm font-medium text-green-600 text-center">
                            Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
                        </div>
                    @endif

                    <div class="text-center mb-6 text-gray-600">
                        Si no has recibido el email, podemos enviarte otro.
                    </div>

                    <form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
                        @csrf
                        <button type="submit" class="pro-button">
                            <span class="relative z-10">Reenviar Email de Verificación</span>
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full text-blue-600 hover:text-blue-800 transition-colors">
                            Cerrar Sesión
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
