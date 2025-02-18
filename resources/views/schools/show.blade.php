<x-app-layout>
    <div class="min-h-screen bg-[#F4F7FE]">
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Header de la Escuela -->
                <div class="bg-white rounded-[20px] p-8 shadow-sm border border-gray-100 mb-8">
                    <div class="flex items-start space-x-6">
                        @if($school->logo_path)
                            <img src="{{ asset('images/' . $school->logo_path) }}"
                                 class="w-24 h-24 rounded-2xl object-cover ring-4 ring-white shadow-lg"
                                 alt="{{ $school->name }}">
                        @else
                            <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-indigo-100 to-purple-100 ring-4 ring-white shadow-lg flex items-center justify-center text-4xl">
                                🏫
                            </div>
                        @endif
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h1 class="text-3xl font-black text-gray-900 mb-2">{{ $school->name }}</h1>
                                    <div class="flex items-center space-x-4 text-gray-500">
                                        @if($school->city)
                                            <span class="flex items-center">
                                                <span class="mr-1">📍</span> {{ $school->city }}
                                            </span>
                                        @endif
                                        <span class="flex items-center">
                                            <span class="mr-1">🔑</span> {{ $school->code }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex space-x-3">
                                    <form action="{{ route('schools.regenerate-logo', $school->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="p-2 bg-white border-2 border-gray-200 text-gray-700 rounded-xl hover:border-blue-500 hover:text-blue-500 transition-all duration-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                        </button>
                                    </form>
                                    <button onclick="document.getElementById('modal-editar-escuela').classList.remove('hidden')"
                                            class="px-4 py-2 bg-white border-2 border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:border-blue-500 hover:text-blue-500 transition-all duration-300 flex items-center">
                                        <span class="mr-2">✏️</span>
                                        Editar
                                    </button>
                                </div>
                            </div>
                            @if($school->description)
                                <p class="mt-4 text-gray-600">{{ $school->description }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="grid grid-cols-3 gap-6 mt-8">
                        <div class="bg-indigo-50 rounded-2xl p-6 text-center">
                            <div class="text-3xl mb-2">👥</div>
                            <div class="text-sm font-medium text-gray-600 mb-1">Grupos</div>
                            <div class="text-2xl font-bold text-indigo-600">{{ $school->groups_count }}</div>
                        </div>
                        <div class="bg-purple-50 rounded-2xl p-6 text-center">
                            <div class="text-3xl mb-2">👨‍🎓</div>
                            <div class="text-sm font-medium text-gray-600 mb-1">Alumnos</div>
                            <div class="text-2xl font-bold text-purple-600">{{ $school->students_count }}</div>
                        </div>
                        <div class="bg-pink-50 rounded-2xl p-6 text-center">
                            <div class="text-3xl mb-2">👨‍🏫</div>
                            <div class="text-sm font-medium text-gray-600 mb-1">Profesores</div>
                            <div class="text-2xl font-bold text-pink-600">{{ $school->users_count }}</div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Grupos -->
                <div class="bg-white rounded-[20px] p-8 shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Grupos</h2>
                            <p class="text-sm text-gray-500">Gestiona los grupos de la escuela</p>
                        </div>
                        <button onclick="document.getElementById('modal-nuevo-grupo').classList.remove('hidden')"
                                class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 flex items-center">
                            <span class="mr-2">✨</span>
                            Nuevo Grupo
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($school->groups as $group)
                            <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 hover:shadow-md transition-all duration-300">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="relative group">
                                            @if($group->avatar_path)
                                                <img src="{{ asset('images/' . $group->avatar_path) }}"
                                                     class="h-12 w-12 rounded-xl object-cover"
                                                     alt="{{ $group->name }}">
                                            @else
                                                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center text-xl">
                                                    👥
                                                </div>
                                            @endif
                                            <form action="{{ route('groups.regenerate-avatar', ['school' => $school->id, 'group' => $group->id]) }}"
                                                  method="POST"
                                                  class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                @csrf
                                                <button type="submit"
                                                        class="p-1 bg-white rounded-lg shadow-sm border border-gray-200 hover:border-blue-500 hover:text-blue-500">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900">{{ $group->name }}</h3>
                                            @if($group->description)
                                                <p class="text-sm text-gray-500">{{ $group->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div class="text-center p-3 bg-white rounded-lg">
                                        <div class="text-sm font-medium text-gray-600">Alumnos</div>
                                        <div class="text-lg font-bold text-gray-900">{{ $group->students_count }}</div>
                                    </div>
                                    <div class="text-center p-3 bg-white rounded-lg">
                                        <div class="text-sm font-medium text-gray-600">Actitudes</div>
                                        <div class="text-lg font-bold text-gray-900">{{ $group->attitudes_count }}</div>
                                    </div>
                                </div>

                                <div class="flex space-x-2">
                                    <form action="{{ route('groups.regenerate-avatar', ['school' => $school->id, 'group' => $group->id]) }}"
                                          method="POST"
                                          class="flex-shrink-0">
                                        @csrf
                                        <button type="submit"
                                                class="px-4 py-2 bg-white border-2 border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:border-blue-500 hover:text-blue-500 transition-all duration-300 flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </button>
                                    </form>

                                    <a href="{{ route('groups.show', ['school' => $school->id, 'group' => $group->id]) }}"
                                       class="flex-1 px-4 py-2 bg-white border-2 border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:border-blue-500 hover:text-blue-500 transition-all duration-300 flex items-center justify-center">
                                        <span class="mr-2">👥</span>
                                        Ver Grupo
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-blue-50 mb-4">
                                    <span class="text-4xl">👥</span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">No hay grupos</h3>
                                <p class="text-gray-500 mt-2">Crea tu primer grupo para empezar</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Editar Escuela -->
    <div id="modal-editar-escuela" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                 onclick="document.getElementById('modal-editar-escuela').classList.add('hidden')"
                 aria-hidden="true"></div>

            <!-- Modal panel -->
            <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Editar Escuela</h2>
                    <button onclick="document.getElementById('modal-editar-escuela').classList.add('hidden')"
                            class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Cerrar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('schools.update', $school->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nombre de la Escuela</label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ $school->name }}"
                                   required
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">Ciudad</label>
                            <input type="text"
                                   id="city"
                                   name="city"
                                   value="{{ $school->city }}"
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                            <textarea id="description"
                                      name="description"
                                      rows="3"
                                      class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $school->description }}</textarea>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button"
                                    onclick="document.getElementById('modal-editar-escuela').classList.add('hidden')"
                                    class="px-4 py-2 bg-white border-2 border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:border-blue-500 hover:text-blue-500 transition-all duration-300">
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300">
                                Guardar Cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Nuevo Grupo -->
    <div id="modal-nuevo-grupo" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                 onclick="document.getElementById('modal-nuevo-grupo').classList.add('hidden')"
                 aria-hidden="true"></div>

            <!-- Modal panel -->
            <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Crear Nuevo Grupo</h2>
                    <button onclick="document.getElementById('modal-nuevo-grupo').classList.add('hidden')"
                            class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Cerrar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('groups.store', ['school' => $school->id]) }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nombre del Grupo</label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   required
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                            <textarea id="description"
                                      name="description"
                                      rows="3"
                                      class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>

                        <!-- Sección de Actitudes -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Actitudes</h3>

                            <!-- Nuevas Actitudes -->
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <label class="block text-sm font-medium text-gray-700">Actitudes del grupo</label>
                                    <button type="button"
                                            onclick="addNewAttitude()"
                                            class="text-sm text-blue-600 hover:text-blue-800">
                                        + Añadir actitud
                                    </button>
                                </div>
                                <div id="new-attitudes" class="space-y-3">
                                    <!-- Los campos de actitudes se añadirán aquí dinámicamente -->
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button"
                                    onclick="document.getElementById('modal-nuevo-grupo').classList.add('hidden')"
                                    class="px-4 py-2 bg-white border-2 border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:border-blue-500 hover:text-blue-500 transition-all duration-300">
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300">
                                Crear Grupo
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
function addNewAttitude() {
    const container = document.getElementById('new-attitudes');
    const newField = document.createElement('div');
    newField.className = 'flex items-center space-x-2';
    newField.innerHTML = `
        <input type="text"
               name="new_attitudes[]"
               class="flex-1 rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
               placeholder="Nombre de la actitud">
        <input type="number"
               name="new_attitude_points[]"
               class="w-20 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
               placeholder="Puntos">
        <button type="button" onclick="removeAttitude(this)"
                class="p-2 text-red-500 hover:text-red-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    container.appendChild(newField);
}

function removeAttitude(button) {
    button.parentElement.remove();
}
</script>
