<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes Tâches d\'Aujourd\'hui') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- BLOC UNIQUE : AUJOURD'HUI -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-green-700 mb-4 flex items-center">
                    <span class="mr-2">📅</span> Aujourd'hui
                </h3>
                
                <!-- Inclusion de ton tableau de tâches -->
                @include('tasks.partials.table', ['tasks' => $todayTasks])
            </div>
        </div>
    </div>

    <!-- 🌟 PIED DE PAGE & SIGNATURE BADO 🌟 -->
    <footer class="text-center py-6 mt-8 border-t border-dashed border-gray-200">
        <div class="flex flex-col items-center justify-center space-y-1">
            <p class="text-sm font-semibold text-gray-500 tracking-wide">
                🚀 <span class="text-gray-800 border-r border-gray-300 pr-2 mr-2">TaskManager</span> 
                Propulsé avec passion par 
                <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold ml-1 shadow-sm">
                    ✍️ BADO
                </span>
            </p>
            <span class="text-xs text-gray-400 opacity-75">
                &copy; {{ date('Y') }} &bull; Tous droits réservés &bull; Amélioration continue
            </span>
        </div>
    </footer>
</x-app-layout>