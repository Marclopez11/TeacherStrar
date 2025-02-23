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
                        <h1 class="text-2xl font-bold text-gray-900">Horarios - {{ $school->name }}</h1>
                        <button onclick="document.getElementById('modal-time-slots').classList.remove('hidden')"
                            class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300">
                            <span class="mr-2">⏰</span>
                            Configurar Franjas Horarias
                        </button>
                    </div>
                </div>

                <!-- Selector de Grupo -->
                <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 mb-8">
                    <label for="group" class="block text-sm font-medium text-gray-700">Seleccionar Grupo</label>
                    <select id="group" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            onchange="window.location.href = this.value">
                        <option value="">Selecciona un grupo...</option>
                        @foreach($school->groups as $group)
                            <option value="{{ route('schools.schedule', ['school' => $school->id, 'group' => $group->id]) }}"
                                    {{ request()->get('group') == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($selectedGroup)
                    <div class="bg-white rounded-[20px] p-8 shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-gray-900">Horario de {{ $selectedGroup->name }}</h2>
                            <div class="flex space-x-4">
                                <a href="{{ route('schools.schedule.pdf', ['school' => $school->id, 'group' => $selectedGroup->id]) }}"
                                   target="_blank"
                                   class="px-4 py-2 bg-gradient-to-r from-red-500 to-pink-600 text-white text-sm font-semibold rounded-xl hover:from-red-600 hover:to-pink-700 transition-all duration-300 flex items-center">
                                    <span class="mr-2">📄</span>
                                    Descargar PDF
                                </a>
                                <button onclick="document.getElementById('modal-edit-subjects').classList.remove('hidden')"
                                        class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-300">
                                    <span class="mr-2">📚</span>
                                    Editar Materias
                                </button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            Hora
                                        </th>
                                        @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $day)
                                            <th class="px-6 py-3 border-b border-gray-200 {{ strtolower($day) === strtolower(now()->locale('es')->dayName) ? 'bg-blue-50' : 'bg-gray-50' }} text-left text-xs leading-4 font-medium {{ strtolower($day) === strtolower(now()->locale('es')->dayName) ? 'text-blue-600' : 'text-gray-500' }} uppercase tracking-wider">
                                                {{ $day }}
                                                @if(strtolower($day) === strtolower(now()->locale('es')->dayName))
                                                    <span class="ml-1 text-blue-400">•</span>
                                                @endif
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($timeSlots as $slot)
                                        <tr class="{{ $slot->is_break ? 'bg-gray-100' : '' }}">
                                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                                <div class="text-sm leading-5 text-gray-900">
                                                    {{ $slot->name }}
                                                    @if($slot->is_break)
                                                        <span class="ml-2 text-xs text-gray-500">(Recreo)</span>
                                                    @endif
                                                </div>
                                                <div class="text-sm leading-5 text-gray-500">
                                                    {{ $slot->start_time->format('H:i') }} - {{ $slot->end_time->format('H:i') }}
                                                </div>
                                            </td>
                                            @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $day)
                                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                                    @if($slot->is_break)
                                                        <div class="text-sm text-gray-500 italic">Recreo</div>
                                                    @else
                                                        <div class="text-sm text-gray-900">
                                                            {{ $scheduleEntries->first(function($entry) use ($slot, $day) {
                                                                return $entry->time_slot_id === $slot->id && $entry->day === $day;
                                                            })?->subject ?? '-' }}
                                                        </div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Nuevo modal para editar materias -->
                    <div id="modal-edit-subjects" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                            </div>

                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                                <form action="{{ route('schools.schedule.update', ['school' => $school->id, 'group' => $selectedGroup->id]) }}" method="POST">
                                    @csrf
                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="text-lg font-bold text-gray-900">Editar Materias - {{ $selectedGroup->name }}</h3>
                                            <button type="button" onclick="document.getElementById('modal-edit-subjects').classList.add('hidden')"
                                                    class="text-gray-400 hover:text-gray-500">
                                                <span class="sr-only">Cerrar</span>
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="overflow-x-auto">
                                            <table class="min-w-full">
                                                <thead>
                                                    <tr>
                                                        <th class="px-4 py-2 border">Horario</th>
                                                        @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $day)
                                                            <th class="px-4 py-2 border">{{ $day }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($timeSlots as $slot)
                                                        <tr>
                                                            <td class="px-4 py-2 border bg-gray-50">
                                                                <div class="font-medium">{{ $slot->name }}</div>
                                                                <div class="text-sm text-gray-500">
                                                                    {{ $slot->start_time->format('H:i') }} - {{ $slot->end_time->format('H:i') }}
                                                                </div>
                                                            </td>
                                                            @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $day)
                                                                <td class="px-4 py-2 border">
                                                                    @if(!$slot->is_break)
                                                                        <input type="text"
                                                                               name="entries[{{ $slot->id }}][{{ $day }}][subject]"
                                                                               class="w-full border-gray-300 rounded-lg text-sm"
                                                                               placeholder="Materia"
                                                                               value="{{ $scheduleEntries->first(function($entry) use ($slot, $day) {
                                                                                   return $entry->time_slot_id === $slot->id && $entry->day === $day;
                                                                               })?->subject }}">
                                                                    @else
                                                                        <div class="text-center text-gray-500">Recreo</div>
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                        <button type="submit"
                                                class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-base font-medium text-white hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                            Guardar Materias
                                        </button>
                                        <button type="button"
                                                onclick="document.getElementById('modal-edit-subjects').classList.add('hidden')"
                                                class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                            Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal para configurar franjas horarias -->
    <div id="modal-time-slots" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('schools.time-slots.store', $school) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Configurar Franjas Horarias</h3>
                            <button type="button" onclick="document.getElementById('modal-time-slots').classList.add('hidden')"
                                    class="text-gray-400 hover:text-gray-500">
                                <span class="sr-only">Cerrar</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div id="time-slots-container" class="space-y-4">
                            <!-- Las franjas horarias se añadirán aquí dinámicamente -->
                        </div>

                        <button type="button" onclick="addTimeSlot()"
                                class="mt-4 w-full px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-all duration-300 flex items-center justify-center">
                            <span class="mr-2">➕</span>
                            Añadir Franja Horaria
                        </button>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                                class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-base font-medium text-white hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Guardar Cambios
                        </button>
                        <button type="button"
                                onclick="document.getElementById('modal-time-slots').classList.add('hidden')"
                                class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
let timeSlotIndex = 0;

function addTimeSlot(existingData = null) {
    const container = document.getElementById('time-slots-container');
    const index = timeSlotIndex++;

    const slotHtml = `
        <div class="bg-gray-50 p-4 rounded-xl relative mb-4">
            <button type="button" onclick="removeTimeSlot(this)"
                    class="absolute top-2 right-2 text-gray-400 hover:text-red-500">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text"
                           name="slots[${index}][name]"
                           required
                           class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Ej: Primera Hora"
                           value="${existingData ? existingData.name : ''}">
                </div>

                <div class="flex items-center mt-6">
                    <input type="hidden" name="slots[${index}][is_break]" value="false">
                    <input type="checkbox"
                           name="slots[${index}][is_break]"
                           id="is_break_${index}"
                           value="true"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                           ${existingData && existingData.is_break ? 'checked' : ''}>
                    <label for="is_break_${index}" class="ml-2 block text-sm text-gray-700">
                        Es recreo
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Hora inicio</label>
                    <input type="time"
                           name="slots[${index}][start_time]"
                           required
                           class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           value="${existingData ? existingData.start_time : ''}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Hora fin</label>
                    <input type="time"
                           name="slots[${index}][end_time]"
                           required
                           class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           value="${existingData ? existingData.end_time : ''}">
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', slotHtml);

    // Añadir evento para manejar el cambio del checkbox
    const checkbox = container.querySelector(`#is_break_${index}`);
    checkbox.addEventListener('change', function() {
        const nameInput = this.closest('.grid').querySelector('input[type="text"]');
        if (this.checked) {
            nameInput.value = nameInput.value || 'Recreo';
        }
    });
}

function removeTimeSlot(button) {
    button.closest('.bg-gray-50').remove();
}

function resetTimeSlots() {
    const container = document.getElementById('time-slots-container');
    container.innerHTML = '';
    timeSlotIndex = 0;
}

// Manejar la apertura del modal
document.querySelector('[onclick="document.getElementById(\'modal-time-slots\').classList.remove(\'hidden\')"]')
    .addEventListener('click', function() {
        resetTimeSlots();

        @if($timeSlots->isNotEmpty())
            @foreach($timeSlots as $slot)
                addTimeSlot({
                    name: @json($slot->name),
                    start_time: @json($slot->start_time->format('H:i')),
                    end_time: @json($slot->end_time->format('H:i')),
                    is_break: @json((bool)$slot->is_break)  // Asegurar que sea booleano
                });
            @endforeach
        @else
            addTimeSlot();
        @endif
    });

// Manejar el cierre del modal
document.querySelectorAll('[onclick="document.getElementById(\'modal-time-slots\').classList.add(\'hidden\')"]')
    .forEach(button => {
        button.addEventListener('click', function(e) {
            if (e.target === this) {
                resetTimeSlots();
            }
        });
    });
</script>
