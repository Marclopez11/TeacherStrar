<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50">
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header Moderno -->
                <div class="relative bg-white rounded-3xl p-8 shadow-xl mb-8 overflow-hidden">
                    <!-- Decoración de fondo -->
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/5 to-purple-500/5"></div>
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-gradient-to-br from-yellow-400/20 to-orange-500/20 rounded-full blur-2xl"></div>

                    <div class="relative flex justify-between items-center">
                        <a href="{{ route('groups.show', ['school' => $school->id, 'group' => $group->id]) }}"
                           class="inline-flex items-center px-4 py-2 bg-white/80 backdrop-blur-sm border border-gray-200 text-gray-700 rounded-xl hover:bg-indigo-500 hover:text-white transition-all duration-300">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Volver
                        </a>
                        <div class="text-center flex-1">
                            <h1 class="text-3xl font-black">
                                <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-500 to-purple-600">
                                    {{ $group->name }}
                                </span>
                            </h1>
                            <p class="text-gray-500 mt-1">Ranking de la Clase</p>
                        </div>
                        <div class="w-24"><!-- Espaciador --></div>
                    </div>
                </div>

                <!-- Grid de Estudiantes -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    @foreach($group->students->sortByDesc(function($student) use ($group) {
                        return $student->getPointsByGroup($group->id);
                    }) as $index => $student)
                        <div class="transform hover:scale-105 transition-all duration-300 hover:rotate-1">
                            @php
                                $gradientClass = match($index) {
                                    0 => 'from-yellow-500/10 to-amber-500/10',
                                    1 => 'from-slate-500/10 to-gray-500/10',
                                    2 => 'from-orange-500/10 to-red-500/10',
                                    default => 'from-indigo-500/5 to-purple-500/5'
                                };

                                $borderAccentClass = match($index) {
                                    0 => 'border-yellow-400/50',
                                    1 => 'border-gray-400/50',
                                    2 => 'border-orange-400/50',
                                    default => 'border-purple-300/30'
                                };

                                $medalEmoji = match($index) {
                                    0 => '🏆',
                                    1 => '🥈',
                                    2 => '🥉',
                                    default => null
                                };
                            @endphp

                            <div class="relative bg-white rounded-2xl border border-gray-100 shadow-lg overflow-hidden group">
                                <!-- Fondo con gradiente -->
                                <div class="absolute inset-0 bg-gradient-to-br {{ $gradientClass }} opacity-60"></div>

                                <!-- Contenido -->
                                <div class="relative p-6">
                                    <!-- Posición -->
                                    <div class="absolute top-3 right-3 flex items-center space-x-1">
                                        @if($medalEmoji)
                                            <span class="text-2xl filter drop-shadow-md">{{ $medalEmoji }}</span>
                                        @else
                                            <span class="text-sm font-bold text-gray-400">#{{ $index + 1 }}</span>
                                        @endif
                                    </div>

                                    <!-- Avatar -->
                                    <div class="relative mx-auto w-20 h-20 mb-4">
                                        @if($student->avatar_url)
                                            <div class="absolute inset-0 bg-gradient-to-br {{ $gradientClass }} rounded-xl blur-md transform group-hover:scale-110 transition-transform duration-300"></div>
                                            <img src="{{ $student->avatar_url }}"
                                                 class="relative w-full h-full rounded-xl object-cover border-2 {{ $borderAccentClass }}"
                                                 alt="{{ $student->name }}">
                                        @endif
                                    </div>

                                    <!-- Info -->
                                    <div class="text-center">
                                        <h3 class="font-bold text-gray-900 truncate">{{ $student->name }}</h3>
                                        <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full bg-white/50 backdrop-blur-sm border border-gray-100 shadow-sm">
                                            <span class="text-lg font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                                {{ $student->getPointsByGroup($group->id) }}
                                            </span>
                                            <span class="ml-1 text-lg">✨</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</x-app-layout>
