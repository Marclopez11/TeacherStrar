<div class="space-y-8">
    <!-- Mis Escuelas -->
    <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Mis Escuelas</h2>
                <p class="text-sm text-gray-500">Escuelas a las que perteneces</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <input type="text"
                           wire:model.debounce.300ms="search"
                           placeholder="Buscar en mis escuelas..."
                           class="w-64 px-4 py-2 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <button wire:click="$dispatch('modal.open', 'create-school')"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl text-white font-semibold hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 text-sm">
                    <span class="mr-2">✨</span>
                    Nueva Escuela
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($mySchools as $school)
                <div class="group bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-4 mb-4">
                        @if($school->logo_path)
                            <img src="{{ Storage::url($school->logo_path) }}"
                                 class="h-16 w-16 rounded-xl object-cover"
                                 alt="{{ $school->name }}">
                        @else
                            <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center text-2xl">
                                🏫
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $school->name }}</h3>
                            @if($school->city)
                                <p class="text-gray-500 text-sm">📍 {{ $school->city }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                            <div class="text-sm font-medium text-gray-600">Grupos</div>
                            <div class="text-lg font-bold text-gray-900">{{ $school->groups_count }}</div>
                        </div>
                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                            <div class="text-sm font-medium text-gray-600">Alumnos</div>
                            <div class="text-lg font-bold text-gray-900">{{ $school->students_count }}</div>
                        </div>
                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                            <div class="text-sm font-medium text-gray-600">Profes</div>
                            <div class="text-lg font-bold text-gray-900">{{ $school->users_count }}</div>
                        </div>
                    </div>

                    <a href="{{ route('schools.show', $school) }}"
                       class="w-full px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 flex items-center justify-center">
                        <span class="mr-2">🎯</span>
                        Gestionar
                    </a>
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
                <h2 class="text-xl font-bold text-gray-900">Unirse a una Escuela</h2>
                <p class="text-sm text-gray-500">Explora y únete a otras escuelas</p>
            </div>
            <div class="relative">
                <input type="text"
                       wire:model.debounce.300ms="searchJoin"
                       placeholder="Buscar escuelas..."
                       class="w-64 px-4 py-2 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($availableSchools as $school)
                <div class="group bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center space-x-4 mb-4">
                        @if($school->logo_path)
                            <img src="{{ Storage::url($school->logo_path) }}"
                                 class="h-16 w-16 rounded-xl object-cover"
                                 alt="{{ $school->name }}">
                        @else
                            <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-2xl">
                                🏫
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $school->name }}</h3>
                            @if($school->city)
                                <p class="text-gray-500 text-sm">📍 {{ $school->city }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 mb-4">
                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                            <div class="text-sm font-medium text-gray-600">Grupos</div>
                            <div class="text-lg font-bold text-gray-900">{{ $school->groups_count }}</div>
                        </div>
                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                            <div class="text-sm font-medium text-gray-600">Alumnos</div>
                            <div class="text-lg font-bold text-gray-900">{{ $school->students_count }}</div>
                        </div>
                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                            <div class="text-sm font-medium text-gray-600">Profes</div>
                            <div class="text-lg font-bold text-gray-900">{{ $school->users_count }}</div>
                        </div>
                    </div>

                    <button wire:click="joinSchool({{ $school->id }})"
                            class="w-full px-4 py-2 bg-white border-2 border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:border-blue-500 hover:text-blue-500 transition-all duration-300 flex items-center justify-center">
                        <span class="mr-2">🤝</span>
                        Unirse
                    </button>
                </div>
            @empty
                <div class="col-span-full text-center py-8">
                    <p class="text-gray-500">No hay escuelas disponibles para unirse</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
