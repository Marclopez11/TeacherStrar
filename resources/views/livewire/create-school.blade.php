<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Crear Nueva Escuela</h2>
        <button wire:click="$dispatch('modal.close')" class="text-gray-400 hover:text-gray-500">
            <span class="sr-only">Cerrar</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <form wire:submit="save">
        <div class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nombre de la Escuela</label>
                <input type="text"
                       id="name"
                       wire:model.defer="name"
                       class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('name') <span class="mt-1 text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="city" class="block text-sm font-medium text-gray-700">Ciudad</label>
                <input type="text"
                       id="city"
                       wire:model.defer="city"
                       class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('city') <span class="mt-1 text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea id="description"
                          wire:model.defer="description"
                          rows="3"
                          class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('description') <span class="mt-1 text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button type="button"
                        wire:click="$dispatch('modal.close')"
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

<style>
    .pro-button {
        @apply px-6 py-3 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white font-semibold rounded-xl
               hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center;
    }

    .pro-button-outline {
        @apply px-6 py-3 bg-white text-gray-700 font-semibold rounded-xl border-2 border-gray-200
               hover:border-indigo-500 hover:text-indigo-500 transform hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center;
    }
</style>
