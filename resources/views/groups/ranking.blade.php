<x-app-layout>
    <div class="min-h-screen bg-white">
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header Minimalista -->
                <div class="flex items-center justify-between mb-6 px-4">
                    <a href="{{ route('groups.show', ['school' => $school->id, 'group' => $group->id]) }}"
                       class="text-gray-500 hover:text-gray-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div class="text-center">
                        <h1 class="text-xl font-bold text-gray-900">{{ $group->name }}</h1>
                        <p class="text-sm text-gray-500">Ranking</p>
                    </div>
                    <div class="w-5"><!-- Espaciador --></div>
                </div>

                <!-- Botones de ordenamiento -->
                <div class="flex justify-center gap-4 mb-6">
                    <a href="{{ route('groups.ranking', ['school' => $school->id, 'group' => $group->id, 'sort' => 'alpha']) }}"
                       class="px-4 py-2 text-sm font-medium rounded-md {{ request('sort', 'alpha') === 'alpha' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Alfabético
                    </a>
                    <a href="{{ route('groups.ranking', ['school' => $school->id, 'group' => $group->id, 'sort' => 'points']) }}"
                       class="px-4 py-2 text-sm font-medium rounded-md {{ request('sort') === 'points' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Por Puntos ↓
                    </a>
                </div>

                <!-- Grid de Estudiantes -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 xl:grid-cols-10 gap-4">
                    @foreach($students as $index => $student)
                        <div class="transform hover:scale-105 transition-all duration-300">
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

                            <div class="relative bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden group p-4 min-h-[200px] flex flex-col items-center justify-between">
                                <!-- Fondo con gradiente -->
                                <div class="absolute inset-0 bg-gradient-to-br {{ $gradientClass }} opacity-60"></div>

                                <!-- Contenido -->
                                <div class="relative w-full">
                                    <!-- Posición -->
                                    <div class="absolute top-0 right-0 flex items-center space-x-1">
                                        @if($medalEmoji)
                                            <span class="text-lg filter drop-shadow-md">{{ $medalEmoji }}</span>
                                        @else
                                            <span class="text-xs font-bold text-gray-400">#{{ $index + 1 }}</span>
                                        @endif
                                    </div>

                                    <!-- Avatar -->
                                    <div class="relative mx-auto w-20 h-20 mb-3">
                                        <div class="w-full h-full rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden">
                                            <img src="{{ asset('images/' . $student->avatar_path) }}"
                                                 class="w-16 h-16 object-contain"
                                                 alt="{{ $student->name }}">
                                        </div>
                                    </div>

                                    <!-- Info -->
                                    <div class="text-center">
                                        <h3 class="text-sm font-bold text-gray-900 mb-2 px-2 line-clamp-2 min-h-[2.5rem]">
                                            {{ $student->name }}
                                        </h3>
                                        <div class="inline-flex items-center px-3 py-1 rounded-full bg-white/50 backdrop-blur-sm border border-gray-100 shadow-sm">
                                            <span class="text-base font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                                {{ $student->getPointsByGroup($group->id) }}
                                            </span>
                                            <span class="ml-1 text-base">✨</span>
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
