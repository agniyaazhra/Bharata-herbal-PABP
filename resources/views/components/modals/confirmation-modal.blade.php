@props([
'id' => 'confirmationModal',
])

<div x-cloak x-show="confirmationModals['{{ $id }}'] && confirmationModals['{{ $id }}'].show"
    x-transition.opacity
    class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
    @click.self="closeConfirmationModal('{{ $id }}')">

    <div @click.stop
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        class="bg-white rounded-2xl shadow-lg w-full max-w-md">

        {{-- Icon & Message --}}
        <div class="text-center pt-8 pb-6 px-8">
            {{-- Icon --}}
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full"
                :class="confirmationModals['{{ $id }}'].iconColor === 'red' ? 'bg-red-100' : 'bg-orange-100'">
                <svg class="w-8 h-8"
                    :class="confirmationModals['{{ $id }}'].iconColor === 'red' ? 'text-red-600' : 'text-orange-600'"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <template x-if="confirmationModals['{{ $id }}'].icon === 'trash-2'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </template>
                    <template x-if="confirmationModals['{{ $id }}'].icon === 'log-out'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </template>
                </svg>
            </div>

            {{-- Title & Message --}}
            <h3 class="mt-4 font-bold text-gray-900 text-lg">
                <span x-text="confirmationModals['{{ $id }}'].title"></span>
            </h3>
            <p class="mt-2 text-gray-600 text-sm">
                <span x-text="confirmationModals['{{ $id }}'].message"></span>
            </p>
        </div>

        {{-- Buttons --}}
        <div class="flex items-center gap-3 px-8 pb-6 border-t border-gray-100 pt-4">
            <button type="button"
                @click="closeConfirmationModal('{{ $id }}')"
                class="flex-1 border border-gray-200 hover:bg-gray-50 text-gray-600 font-medium px-6 py-2.5 rounded-xl text-sm transition">
                <span x-text="confirmationModals['{{ $id }}'].cancelText"></span>
            </button>
            <button type="button"
                @click="submitConfirmation('{{ $id }}')"
                :disabled="confirmationModals['{{ $id }}'].isLoading"
                class="flex-1 bg-red-700 hover:bg-red-800 disabled:bg-red-500 text-white font-medium px-6 py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-2">
                <svg x-show="!confirmationModals['{{ $id }}'].isLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span x-text="confirmationModals['{{ $id }}'].isLoading ? 'Proses...' : confirmationModals['{{ $id }}'].confirmText"></span>
            </button>
        </div>
    </div>
</div>