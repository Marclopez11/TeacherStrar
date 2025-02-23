<x-app-layout>
    <div class="min-h-screen bg-[#F4F7FE]">
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Header -->
                <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">
                                ¡Bienvenido, {{ Auth::user()->name }}!
                            </h1>
                            <p class="text-gray-500">
                                {{ now()->format('l, d \d\e F') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Lista de Escuelas -->
                <div class="space-y-8">
                    <!-- Mis Escuelas -->
                    <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Mis Escuelas</h2>
                                <p class="text-sm text-gray-500">Escuelas a las que perteneces</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <button onclick="document.getElementById('modal-nueva-escuela').classList.remove('hidden')"
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl text-white font-semibold hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 text-sm">
                                    <span class="mr-2">✨</span>
                                    Nueva Escuela
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse($userSchools as $school)
                                <div class="group bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                                    <div class="flex items-center space-x-4 mb-4">
                                        @if($school->logo_path)
                                            <img src="{{ $school->logo_url }}"
                                                 class="w-20 h-20 rounded-2xl object-cover ring-4 ring-white shadow-lg"
                                                 alt="{{ $school->name }}">
                                        @else
                                            <img src="{{ $school->logo_url }}"
                                                 class="w-20 h-20 rounded-2xl object-cover ring-4 ring-white shadow-lg"
                                                 alt="{{ $school->name }}">
                                        @endif
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900">{{ $school->name }}</h3>
                                            <p class="text-gray-500 text-sm">🔑 {{ $school->code }}</p>
                                            @if($school->city)
                                                <p class="text-gray-500 text-sm">📍 {{ $school->city }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-3 gap-2 mb-4">
                                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                                            <div class="text-sm font-medium text-gray-600">Grupos</div>
                                            <div class="text-lg font-bold text-gray-900">{{ $school->groups_count ?? 0 }}</div>
                                        </div>
                                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                                            <div class="text-sm font-medium text-gray-600">Alumnos</div>
                                            <div class="text-lg font-bold text-gray-900">{{ $school->students_count ?? 0 }}</div>
                                        </div>
                                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                                            <div class="text-sm font-medium text-gray-600">Profes</div>
                                            <div class="text-lg font-bold text-gray-900">{{ $school->users_count ?? 0 }}</div>
                                        </div>
                                    </div>

                                    <div class="flex space-x-3">
                                        <a href="{{ route('schools.show', $school) }}"
                                           class="w-full px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 flex items-center justify-center">
                                            <span class="mr-2">🎯</span>
                                            Gestionar
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-8">
                                    <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-blue-50 mb-4">
                                        <span class="text-4xl">🎯</span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">No perteneces a ninguna escuela</h3>
                                    <p class="text-gray-500 mt-2">Crea una nueva escuela o únete a una existente</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Escuelas Disponibles -->
                    <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Escuelas Disponibles</h2>
                                <p class="text-sm text-gray-500 mt-1">Únete a otras escuelas de la comunidad</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($availableSchools as $school)
                                <div class="group bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-100 hover:shadow-lg transition-all duration-300">
                                    <div class="flex items-start space-x-4 mb-4">
                                        <div class="flex-shrink-0">
                                            @if($school->logo_path)
                                                <img src="{{ $school->logo_url }}"
                                                     class="w-20 h-20 rounded-2xl object-cover ring-4 ring-white shadow-lg"
                                                      alt="{{ $school->name }}">
                                            @else
                                                <img src="{{ $school->logo_url }}"
                                                     class="w-20 h-20 rounded-2xl object-cover ring-4 ring-white shadow-lg"
                                                      alt="{{ $school->name }}">
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-lg font-bold text-gray-900">{{ $school->name }}</h4>
                                            @if($school->city)
                                                <p class="text-sm text-gray-600 flex items-center mt-1">
                                                    <span class="mr-1">📍</span> {{ $school->city }}
                                                </p>
                                            @endif
                                            <p class="text-sm text-gray-600 flex items-center mt-1">
                                                <span class="mr-1">🔑</span> {{ $school->code }}
                                            </p>
                                        </div>
                                    </div>

                                    <form action="{{ route('schools.join') }}" method="POST" class="mt-4">
                                        @csrf
                                        <input type="hidden" name="code" value="{{ $school->code }}">
                                        <div class="flex items-center space-x-3">
                                            <input type="password"
                                                   name="password"
                                                   required
                                                   class="flex-1 px-4 py-2 text-sm border border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 bg-white"
                                                   placeholder="Contraseña">
                                            <button type="submit"
                                                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 flex items-center justify-center whitespace-nowrap">
                                                <span class="mr-2">🤝</span>
                                                Unirse
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endforeach

                            @if($availableSchools->isEmpty())
                                <div class="col-span-full text-center py-8">
                                    <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 mb-4">
                                        <span class="text-4xl">🔍</span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">No hay escuelas disponibles</h3>
                                    <p class="text-gray-500 mt-2">Todas las escuelas ya están conectadas contigo</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
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
                            <label for="logo_path" class="block text-sm font-medium text-gray-700">URL del Logo</label>
                            <input type="url"
                                   id="logo_path"
                                   name="logo_path"
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="https://ejemplo.com/logo.png">
                            <p class="mt-1 text-sm text-gray-500">Deja vacío para usar un logo generado automáticamente</p>
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">Ciudad</label>
                            <input type="text"
                                   id="city"
                                   name="city"
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Contraseña de la Escuela</label>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   required
                                   minlength="6"
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Mínimo 6 caracteres">
                            <p class="mt-1 text-sm text-gray-500">Esta contraseña será necesaria para que otros profesores se unan a la escuela</p>
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
</x-app-layout>
