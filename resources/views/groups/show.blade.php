<x-app-layout>
    <div class="min-h-screen bg-[#F4F7FE]">
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Header del Grupo -->
                <div class="bg-white rounded-[20px] p-8 shadow-sm border border-gray-100 mb-8">
                    <div class="flex items-start space-x-6">
                        <div class="relative group">
                            @if($group->avatar_path)
                                <img src="{{ asset('images/' . $group->avatar_path) }}"
                                     class="w-24 h-24 rounded-2xl object-cover ring-4 ring-white shadow-lg"
                                     alt="{{ $group->name }}">
                            @else
                                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 ring-4 ring-white shadow-lg flex items-center justify-center text-4xl">
                                    👥
                                </div>
                            @endif
                            <form action="{{ route('groups.regenerate-avatar', ['school' => $school->id, 'group' => $group->id]) }}"
                                  method="POST"
                                  class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                @csrf
                                <button type="submit"
                                        class="p-2 bg-white rounded-lg shadow-sm border border-gray-200 hover:border-blue-500 hover:text-blue-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h1 class="text-3xl font-black text-gray-900 mb-2">{{ $group->name }}</h1>
                                    <a href="{{ route('schools.show', $school) }}"
                                       class="text-sm text-gray-500 hover:text-blue-500">
                                        {{ $school->name }}
                                    </a>
                                    @if($group->description)
                                        <p class="mt-2 text-gray-600">{{ $group->description }}</p>
                                    @endif
                                </div>
                                <button onclick="document.getElementById('modal-editar-grupo').classList.remove('hidden')"
                                        class="px-4 py-2 bg-white border-2 border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:border-blue-500 hover:text-blue-500 transition-all duration-300 flex items-center">
                                    <span class="mr-2">✏️</span>
                                    Editar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas Generales -->
                    <div class="mt-6 space-y-4">
                        <!-- Resumen General -->
                        <div class="grid grid-cols-4 gap-3">
                            <div class="bg-indigo-50 rounded-xl p-3 text-center">
                                <div class="text-2xl mb-1">👨‍🎓</div>
                                <div class="text-xs font-medium text-gray-600">Alumnos</div>
                                <div class="text-lg font-bold text-indigo-600">{{ $group->students->count() }}</div>
                            </div>
                            <div class="bg-purple-50 rounded-xl p-3 text-center">
                                <div class="text-2xl mb-1">⭐</div>
                                <div class="text-xs font-medium text-gray-600">Actitudes</div>
                                <div class="text-lg font-bold text-purple-600">{{ $group->attitudes->count() }}</div>
                            </div>
                            <div class="bg-green-50 rounded-xl p-3 text-center">
                                <div class="text-2xl mb-1">📊</div>
                                <div class="text-xs font-medium text-gray-600">Total Puntos</div>
                                <div class="text-lg font-bold text-green-600">
                                    {{ $group->students->sum(function($student) use ($group) {
                                        return $student->getPointsByGroup($group->id);
                                    }) }}
                                </div>
                            </div>
                            <div class="bg-blue-50 rounded-xl p-3 text-center">
                                <div class="text-2xl mb-1">📈</div>
                                <div class="text-xs font-medium text-gray-600">Media/Alumno</div>
                                <div class="text-lg font-bold text-blue-600">
                                    @php
                                        $studentCount = $group->students->count();
                                        $totalPoints = $group->students->sum(function($student) use ($group) {
                                            return $student->getPointsByGroup($group->id);
                                        });
                                        $average = $studentCount > 0 ? round($totalPoints / $studentCount, 1) : 0;
                                    @endphp
                                    {{ $average }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón de Ranking -->
                <div class="flex justify-end mb-8">
                    <a href="{{ route('groups.ranking', ['school' => $school->id, 'group' => $group->id]) }}"
                       class="px-4 py-2 bg-gradient-to-r from-yellow-400 to-yellow-500 text-white text-sm font-semibold rounded-xl hover:from-yellow-500 hover:to-yellow-600 transition-all duration-300 flex items-center">
                        <span class="mr-2">🏆</span>
                        Ver Ranking
                    </a>
                </div>

                <!-- Grid de Estudiantes con Sistema de Actitudes -->
                <div class="bg-white rounded-[20px] p-8 shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $group->name }}</h2>
                            <p class="text-sm text-gray-500">Gestión de actitudes de estudiantes</p>
                        </div>
                        <button onclick="document.getElementById('modal-nuevo-estudiante').classList.remove('hidden')"
                                class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 flex items-center">
                            <span class="mr-2">✨</span>
                            Nuevo Estudiante
                        </button>
                    </div>

                    <!-- After the "Nuevo Estudiante" button and before the grid of students -->
                    <div class="flex justify-between items-center mb-4">
                        <div class="relative flex-1 max-w-xs">
                            <input type="text"
                                   id="searchInput"
                                   placeholder="Buscar estudiantes..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <button id="sortButton"
                                    class="px-4 py-2 bg-white border-2 border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:border-blue-500 hover:text-blue-500 transition-all duration-300 flex items-center">
                                <span class="mr-2">↑↓</span>
                                Ordenar A-Z
                            </button>
                        </div>
                    </div>

                    @if($group->attitudes->isNotEmpty())
                        <div class="bg-gray-50 rounded-xl p-4 mb-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Resumen de Actitudes</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                @foreach($group->attitudes as $attitude)
                                    <div class="bg-white rounded-lg p-3 border border-gray-100">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-700">{{ $attitude->name }}</span>
                                            <span class="text-sm font-semibold {{ $attitude->points >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $attitude->points > 0 ? '+' : '' }}{{ $attitude->points }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Grid de Estudiantes -->
                    <div id="studentsGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                        @foreach($group->students as $student)
                            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-all duration-300">
                                <!-- Cabecera del Estudiante -->
                                <div class="p-4 border-b border-gray-100">
                                    <div class="flex items-center space-x-4">
                                        <div class="relative group">
                                            <div class="w-16 h-16 rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden">
                                                <img src="{{ asset('images/' . $student->avatar_path) }}"
                                                     class="w-14 h-14 object-contain"
                                                     alt="{{ $student->name }}">
                                            </div>

                                            @if($student->canChangeAvatar())
                                                <form action="{{ route('students.update-avatar', ['school' => $school->id, 'student' => $student->id]) }}"
                                                      method="POST"
                                                      class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="p-1 bg-white rounded-lg shadow-sm border border-gray-200 hover:border-blue-500 hover:text-blue-500">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-semibold text-gray-900 text-base mb-1">{{ $student->name }}</h3>
                                            <div class="flex items-center text-sm">
                                                <span class="text-gray-500">Puntos:</span>
                                                <span class="ml-1 font-bold {{ $student->getPointsByGroup($group->id) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $student->getPointsByGroup($group->id) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lista de Actitudes -->
                                @php
                                    $today = now()->format('Y-m-d');
                                    $actitudesHoy = $student->attitudes()
                                        ->wherePivot('created_at', '>=', $today)
                                        ->get()
                                        ->groupBy('id');
                                @endphp

                                @if($actitudesHoy->isNotEmpty())
                                    <div class="p-2 bg-gray-50">
                                        <div class="p-3 bg-gray-50">
                                            <h4 class="text-sm font-medium text-gray-600 mb-2">Actitudes de Hoy</h4>
                                            <div class="space-y-2">
                                                @foreach($actitudesHoy as $actitudeGroup)
                                                    @php
                                                        $attitude = $actitudeGroup->first();
                                                        $count = $actitudeGroup->count();
                                                        $isPositive = $attitude->pivot->is_positive;
                                                    @endphp

                                                    <div class="flex items-center justify-between bg-white rounded-lg p-2 border border-gray-100">
                                                        <div class="flex items-center space-x-2">
                                                            <span class="text-lg">{{ $isPositive ? '✅' : '❌' }}</span>
                                                            <span class="text-sm font-medium text-gray-700">{{ $attitude->name }}</span>
                                                            @if($count > 1)
                                                                <span class="px-2 py-0.5 bg-gray-100 rounded-full text-xs font-medium text-gray-600">
                                                                    x{{ $count }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center space-x-1">
                                                            <span class="text-sm font-semibold {{ $isPositive ? 'text-green-600' : 'text-red-600' }}">
                                                                {{ $isPositive ? '+' : '' }}{{ $attitude->pivot->points }}
                                                            </span>
                                                            <button onclick="removeAttitude('{{ $school->id }}', '{{ $student->id }}', '{{ $attitude->id }}')"
                                                                    class="p-1 text-gray-400 hover:text-red-500 transition-colors">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Editar Grupo -->
    <div id="modal-editar-grupo" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                 onclick="document.getElementById('modal-editar-grupo').classList.add('hidden')"
                 aria-hidden="true"></div>

            <!-- Modal panel -->
            <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Editar Grupo</h2>
                    <button onclick="document.getElementById('modal-editar-grupo').classList.add('hidden')"
                            class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Cerrar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('groups.update', ['school' => $school->id, 'group' => $group->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="avatar_seed" id="edit_avatar_seed" value="{{ $group->avatar_seed ?? Str::random(10) }}">
                    <input type="hidden" name="avatar_style" id="edit_avatar_style" value="avataaars">

                    <div class="grid grid-cols-3 gap-6">
                        <!-- Columna Izquierda: Avatar y Datos Básicos -->
                        <div class="col-span-1 space-y-6">
                            <!-- Avatar Preview -->
                            <div class="flex flex-col items-center">
                                <img id="edit-avatar-preview"
                                     src="{{ $group->avatar_path ? asset('images/' . $group->avatar_path) : '' }}"
                                     alt="Avatar Preview"
                                     class="w-32 h-32 rounded-2xl mb-4">
                                <button type="button" onclick="regenerateEditAvatar()"
                                        class="px-4 py-2 bg-white border-2 border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:border-blue-500 hover:text-blue-500 transition-all duration-300">
                                    🎲 Regenerar Avatar
                                </button>
                            </div>

                            <!-- Datos Básicos -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nombre del Grupo</label>
                                <input type="text" id="name" name="name" value="{{ $group->name }}" required
                                       class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                                <textarea id="description" name="description" rows="3"
                                          class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $group->description }}</textarea>
                            </div>
                        </div>

                        <!-- Columna Derecha: Actitudes -->
                        <div class="col-span-2">
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">Actitudes del Grupo</h3>

                                <!-- Lista de Actitudes -->
                                <div class="max-h-[400px] overflow-y-auto pr-2 space-y-3">
                                    @foreach($group->attitudes as $attitude)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                            <div class="flex-1 mr-4">
                                                <input type="text"
                                                       name="attitudes[{{ $attitude->id }}][name]"
                                                       value="{{ $attitude->name }}"
                                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                       placeholder="Nombre de la actitud">
                                            </div>
                                            <input type="number"
                                                   name="attitudes[{{ $attitude->id }}][points]"
                                                   value="{{ $attitude->points }}"
                                                   class="w-20 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                   placeholder="Puntos">
                                            <button type="button" onclick="removeExistingAttitude(this)"
                                                    class="ml-2 p-2 text-red-500 hover:text-red-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Nuevas Actitudes -->
                                <div id="new-attitudes" class="space-y-3">
                                    <!-- Los campos de actitudes se añadirán aquí dinámicamente -->
                                </div>

                                <button type="button"
                                        onclick="addNewAttitude()"
                                        class="w-full px-4 py-2 bg-gray-50 border border-gray-200 text-blue-600 text-sm font-semibold rounded-xl hover:bg-blue-50 hover:border-blue-500 transition-all duration-300">
                                    + Añadir actitud
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                        <button type="button"
                                onclick="document.getElementById('modal-editar-grupo').classList.add('hidden')"
                                class="px-4 py-2 bg-white border-2 border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:border-blue-500 hover:text-blue-500 transition-all duration-300">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Nuevo Estudiante -->
    <div id="modal-nuevo-estudiante" class="fixed inset-0 z-50 overflow-y-auto hidden" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
                 onclick="document.getElementById('modal-nuevo-estudiante').classList.add('hidden')"
                 aria-hidden="true"></div>

            <!-- Modal panel -->
            <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Nuevo Estudiante</h2>
                    <button onclick="document.getElementById('modal-nuevo-estudiante').classList.add('hidden')"
                            class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Cerrar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('students.store', ['school' => $school->id]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="group_id" value="{{ $group->id }}">

                    <div class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nombre del Estudiante</label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   required
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button"
                                    onclick="document.getElementById('modal-nuevo-estudiante').classList.add('hidden')"
                                    class="px-4 py-2 bg-white border-2 border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:border-blue-500 hover:text-blue-500 transition-all duration-300">
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300">
                                Crear Estudiante
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div id="modal-confirmar" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full" id="modal-icon-container">
                    <!-- El icono cambiará según la acción -->
                </div>
                <h3 class="text-lg font-medium text-gray-900 text-center mt-2" id="modal-title"></h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 text-center" id="modal-message"></p>
                </div>
                <div class="flex justify-center gap-4 mt-3">
                    <button id="modal-confirm"
                            class="px-4 py-2 text-white text-base font-medium rounded-md shadow-sm focus:outline-none focus:ring-2">
                        Confirmar
                    </button>
                    <button id="modal-cancel"
                            class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md border border-gray-300 shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
function regenerateAvatar() {
    const seed = Math.random().toString(36).substring(2, 12);
    document.getElementById('avatar_seed').value = seed;
    updateAvatarPreview();
}

function updateAvatarPreview() {
    const seed = document.getElementById('avatar_seed').value;
    const style = document.getElementById('avatar_style').value;
    const imageUrl = `https://api.dicebear.com/7.x/${style}/svg?seed=${seed}&backgroundColor=transparent`;
    document.getElementById('avatar-preview').src = imageUrl;
}

function regenerateEditAvatar() {
    const seed = Math.random().toString(36).substring(2, 12);
    document.getElementById('edit_avatar_seed').value = seed;
    updateEditAvatarPreview();
}

function updateEditAvatarPreview() {
    const seed = document.getElementById('edit_avatar_seed').value;
    const style = document.getElementById('edit_avatar_style').value;
    const imageUrl = `https://api.dicebear.com/7.x/${style}/svg?seed=${seed}&backgroundColor=transparent`;
    document.getElementById('edit-avatar-preview').src = imageUrl;
}

// Inicializar los previews cuando se abren los modales
document.addEventListener('DOMContentLoaded', function() {
    updateAvatarPreview();
    updateEditAvatarPreview();
});

function addNewAttitude() {
    const container = document.getElementById('new-attitudes');
    const newField = document.createElement('div');
    newField.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-xl';
    newField.innerHTML = `
        <div class="flex-1 mr-4">
            <input type="text"
                   name="new_attitudes[]"
                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                   placeholder="Nombre de la actitud">
        </div>
        <input type="number"
               name="new_attitude_points[]"
               class="w-20 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
               placeholder="Puntos">
        <button type="button" onclick="removeAttitude(this)"
                class="ml-2 p-2 text-red-500 hover:text-red-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    container.appendChild(newField);
}

function removeAttitude(button) {
    button.closest('.flex').remove();
}

function removeExistingAttitude(button) {
    const container = button.closest('.flex');
    const attitudeId = container.querySelector('input[name^="attitudes["]').name.match(/\d+/)[0];
    container.remove();

    // Añadir campo oculto para marcar la actitud como eliminada
    const form = document.querySelector('form');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = `delete_attitudes[]`;
    input.value = attitudeId;
    form.appendChild(input);
}

function registrarActitud(studentId, attitudeId, action = 'add') {
    const modal = document.getElementById('modal-confirmar');
    const title = document.getElementById('modal-title');
    const message = document.getElementById('modal-message');
    const confirmBtn = document.getElementById('modal-confirm');
    const iconContainer = document.getElementById('modal-icon-container');

    // Guardar los datos para el evento de confirmación
    modal.dataset.studentId = studentId;
    modal.dataset.attitudeId = attitudeId;
    modal.dataset.action = action;

    // Configurar el modal según la acción
    if (action === 'add') {
        title.textContent = 'Aumentar Puntos';
        message.textContent = '¿Quieres aumentar los puntos de este alumno?';
        iconContainer.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100';
        iconContainer.innerHTML = `
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
        `;
        confirmBtn.className = 'px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-300';
    } else {
        title.textContent = 'Disminuir Puntos';
        message.textContent = '¿Quieres disminuir los puntos de este alumno?';
        iconContainer.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100';
        iconContainer.innerHTML = `
            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
            </svg>
        `;
        confirmBtn.className = 'px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300';
    }

    // Mostrar el modal
    modal.classList.remove('hidden');
}

// Evento para confirmar la acción
document.getElementById('modal-confirm').addEventListener('click', function() {
    const modal = document.getElementById('modal-confirmar');
    const studentId = modal.dataset.studentId;
    const attitudeId = modal.dataset.attitudeId;
    const action = modal.dataset.action;

    if (!studentId || !attitudeId || !action) {
        console.error('Faltan datos necesarios');
        return;
    }

    fetch(`/schools/{{ $school->id }}/students/${studentId}/attitudes`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            attitude_id: attitudeId,
            multiplier: action === 'add' ? 1 : -1
        })
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Error en el servidor');
        return data;
    })
    .then(data => {
        if (data.success) {
            location.reload();
        }
        modal.classList.add('hidden');
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'Error al procesar la solicitud');
        modal.classList.add('hidden');
    });
});

// Evento para cancelar la acción
document.getElementById('modal-cancel').addEventListener('click', function() {
    document.getElementById('modal-confirmar').classList.add('hidden');
});

// Sorting and filtering functionality
document.addEventListener('DOMContentLoaded', function() {
    const grid = document.getElementById('studentsGrid');
    const searchInput = document.getElementById('searchInput');
    const sortButton = document.getElementById('sortButton');
    let isAscending = true;

    // Function to get all student cards
    const getStudentCards = () => Array.from(grid.children);

    // Function to get student name from card
    const getStudentName = card => {
        return card.querySelector('h3').textContent.trim().toLowerCase();
    };

    // Function to sort students
    const sortStudents = () => {
        const cards = getStudentCards();
        const sortedCards = cards.sort((a, b) => {
            const nameA = getStudentName(a);
            const nameB = getStudentName(b);
            return isAscending ? nameA.localeCompare(nameB) : nameB.localeCompare(nameA);
        });

        // Clear and re-append cards in new order
        grid.innerHTML = '';
        sortedCards.forEach(card => grid.appendChild(card));

        // Update button text
        sortButton.innerHTML = `<span class="mr-2">↑↓</span>Ordenar ${isAscending ? 'Z-A' : 'A-Z'}`;
        isAscending = !isAscending;
    };

    // Function to filter students
    const filterStudents = () => {
        const searchTerm = searchInput.value.toLowerCase();
        const cards = getStudentCards();

        cards.forEach(card => {
            const name = getStudentName(card);
            card.style.display = name.includes(searchTerm) ? '' : 'none';
        });
    };

    // Event listeners
    sortButton.addEventListener('click', sortStudents);
    searchInput.addEventListener('input', filterStudents);

    // Initial sort
    sortStudents();
});
</script>
