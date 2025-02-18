<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header con animación -->
                <div class="relative overflow-hidden bg-white/95 backdrop-blur-xl rounded-3xl p-8 shadow-2xl mb-8 transform hover:scale-[1.02] transition-all duration-300">
                    <div class="relative z-10">
                        <div class="flex justify-between items-center">
                            <div>
                                <h1 class="text-4xl font-black text-gray-900 mb-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 bg-clip-text text-transparent">
                                    Mis Escuelas
                                </h1>
                                <p class="text-gray-600">Gestiona tus centros educativos</p>
                            </div>
                            <button onclick="document.getElementById('modal-nueva-escuela').classList.remove('hidden')"
                                    class="pro-button group">
                                <span class="mr-2 group-hover:rotate-12 transition-transform duration-300">✨</span>
                                Nueva Escuela
                            </button>
                        </div>
                    </div>
                    <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-gradient-to-br from-indigo-200 to-purple-200 rounded-full filter blur-3xl opacity-50 animate-pulse"></div>
                </div>

                <!-- Grid de Escuelas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse(auth()->user()->schools as $school)
                        <div class="group relative bg-white/95 backdrop-blur-xl rounded-3xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden">
                            <!-- Fondo decorativo -->
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 to-purple-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                            <!-- Contenido -->
                            <div class="relative z-10">
                                <div class="flex items-center space-x-4 mb-6">
                                    @if($school->logo_path)
                                        <img src="{{ Storage::url($school->logo_path) }}"
                                             class="w-20 h-20 rounded-2xl object-cover ring-4 ring-white shadow-lg"
                                             alt="{{ $school->name }}">
                                    @else
                                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-indigo-100 to-purple-100 ring-4 ring-white shadow-lg flex items-center justify-center text-3xl">
                                            🏫
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">{{ $school->name }}</h3>

                                        @if($school->city)
                                            <p class="text-gray-500 flex items-center">
                                                <span class="mr-1">📍</span> {{ $school->city }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Estadísticas con iconos -->
                                <div class="grid grid-cols-3 gap-4 mb-6">
                                    <div class="bg-indigo-50 rounded-2xl p-3 text-center">
                                        <div class="text-2xl mb-1">👥</div>
                                        <div class="text-sm font-medium text-gray-600">Grupos</div>
                                        <div class="text-lg font-bold text-indigo-600">{{ $school->groups_count ?? 0 }}</div>
                                    </div>
                                    <div class="bg-purple-50 rounded-2xl p-3 text-center">
                                        <div class="text-2xl mb-1">👨‍🎓</div>
                                        <div class="text-sm font-medium text-gray-600">Alumnos</div>
                                        <div class="text-lg font-bold text-purple-600">{{ $school->students_count ?? 0 }}</div>
                                    </div>
                                    <div class="bg-pink-50 rounded-2xl p-3 text-center">
                                        <div class="text-2xl mb-1">👨‍🏫</div>
                                        <div class="text-sm font-medium text-gray-600">Profes</div>
                                        <div class="text-lg font-bold text-pink-600">{{ $school->users_count ?? 0 }}</div>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="flex space-x-3">
                                    <a href="{{ route('schools.show', $school) }}"
                                       class="flex-1 pro-button justify-center group">
                                        <span class="group-hover:rotate-12 transition-transform duration-300 mr-2">🎯</span>
                                        Gestionar
                                    </a>
                                    @if(auth()->user()->current_school?->id !== $school->id)
                                        <form action="{{ route('schools.switch', $school) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit"
                                                    class="w-full pro-button-outline justify-center group">
                                                <span class="group-hover:rotate-12 transition-transform duration-300 mr-2">🔄</span>
                                                Cambiar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <!-- Estado vacío -->
                        <div class="col-span-full">
                            <div class="bg-white/95 backdrop-blur-xl rounded-3xl p-12 text-center">
                                <div class="text-6xl mb-4 animate-bounce">🎨</div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">¡Comienza tu aventura!</h3>
                                <p class="text-gray-600 mb-8">Crea tu primera escuela y empieza a gestionar tus grupos</p>
                                <button onclick="document.getElementById('modal-nueva-escuela').classList.remove('hidden')"
                                        class="pro-button inline-flex items-center group">
                                    <span class="mr-2 group-hover:rotate-12 transition-transform duration-300">✨</span>
                                    Crear Primera Escuela
                                </button>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Modal de Nueva Escuela -->
        <div id="modal-nueva-escuela" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                     onclick="document.getElementById('modal-nueva-escuela').classList.add('hidden')"
                     aria-hidden="true"></div>

                <!-- Modal panel -->
                <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Crear Nueva Escuela</h2>
                        <button onclick="document.getElementById('modal-nueva-escuela').classList.add('hidden')"
                                class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Cerrar</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('schools.store') }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nombre de la Escuela</label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       required
                                       class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700">Ciudad</label>
                                <input type="text"
                                       id="city"
                                       name="city"
                                       class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                                <textarea id="description"
                                          name="description"
                                          rows="3"
                                          class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>

                            <div class="flex justify-end space-x-3 mt-6">
                                <button type="button"
                                        onclick="document.getElementById('modal-nueva-escuela').classList.add('hidden')"
                                        class="px-4 py-2 bg-white border-2 border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:border-blue-500 hover:text-blue-500 transition-all duration-300">
                                    Cancelar
                                </button>
                                <button type="submit"
                                        class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300">
                                    Crear Escuela
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Estilos adicionales -->
    <style>
        .pro-button {
            @apply px-6 py-3 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white font-semibold rounded-xl
                   hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 flex items-center;
        }

        .pro-button-outline {
            @apply px-6 py-3 bg-white text-gray-700 font-semibold rounded-xl border-2 border-gray-200
                   hover:border-indigo-500 hover:text-indigo-500 transform hover:-translate-y-0.5 transition-all duration-300 flex items-center;
        }
    </style>
</x-app-layout>
