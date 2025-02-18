<x-app-layout>
    <div class="min-h-screen bg-[#F4F7FE]">
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Header -->
                <div class="bg-white rounded-[20px] p-8 shadow-sm border border-gray-100 mb-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Estudiantes</h1>
                            <p class="text-gray-500">Gestiona los estudiantes de {{ $school->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Lista de Estudiantes -->
                <div class="bg-white rounded-[20px] p-8 shadow-sm border border-gray-100">
                    @forelse($students as $groupName => $groupStudents)
                        <div class="mb-8 last:mb-0">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">{{ $groupName ?: 'Sin Grupo' }}</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($groupStudents as $student)
                                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 hover:shadow-md transition-all duration-300">
                                        <div class="flex items-center space-x-4">
                                            @if($student->avatar_path)
                                                <img src="{{ asset('images/' . $student->avatar_path) }}"
                                                     class="h-12 w-12 rounded-xl object-cover"
                                                     alt="{{ $student->name }}">
                                            @else
                                                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center text-xl">
                                                    👨‍🎓
                                                </div>
                                            @endif
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900">{{ $student->name }}</h3>
                                                @if($student->email)
                                                    <p class="text-sm text-gray-500">{{ $student->email }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-blue-50 mb-4">
                                <span class="text-4xl">👨‍🎓</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">No hay estudiantes</h3>
                            <p class="text-gray-500 mt-2">Añade estudiantes a los grupos para empezar</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
