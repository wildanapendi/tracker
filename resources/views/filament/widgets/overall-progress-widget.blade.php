<x-filament-widgets::widget>
    <div class="relative overflow-hidden rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <!-- Background decorative blob -->
        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-indigo-500/10 blur-3xl dark:bg-indigo-500/5"></div>
        <div class="absolute -left-10 -bottom-10 h-40 w-40 rounded-full bg-pink-500/10 blur-3xl dark:bg-pink-500/5"></div>

        <div class="relative flex flex-col gap-6">
            <!-- Left Side: Title & Description -->
            <div class="space-y-2 flex-1">
                <h2 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="p-2 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-lg">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.231-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                        </svg>
                    </span>
                    Progres Kelulusan Tugas Akhir/Skripsi
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Nilai rata-rata berbobot dari seluruh penyelesaian bab skripsi Anda. Selesaikan semua tugas untuk mencapai 100%.
                </p>

                <!-- Progress details grid -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-4">
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3 border border-gray-100/50 dark:border-gray-800/30">
                        <span class="text-xs text-gray-400 dark:text-gray-500 block">Tugas Selesai</span>
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            {{ $completedTasks }} <span class="text-xs font-normal text-gray-400">/ {{ $totalTasks }}</span>
                        </span>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3 border border-gray-100/50 dark:border-gray-800/30">
                        <span class="text-xs text-gray-400 dark:text-gray-500 block">Milestone Dicapai</span>
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            {{ $completedMilestones }} <span class="text-xs font-normal text-gray-400">/ {{ $totalMilestones }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Linear Progress bar -->
        <div class="mt-6">
            <div class="flex justify-between items-center text-xs text-gray-400 mb-1.5">
                <span>Penyelesaian Keseluruhan</span>
            </div>
            <!-- Wrapper relative agar teks persentase bisa diposisikan di tengah bar -->
            <div class="relative w-full bg-gray-100 dark:bg-gray-800 rounded-full h-6 overflow-hidden">
                @php
                    $progressColorClass = 'bg-red-500';
                    if ($overallProgress >= 75) {
                        $progressColorClass = 'bg-green-500';
                    } elseif ($overallProgress >= 50) {
                        $progressColorClass = 'bg-yellow-500';
                    } elseif ($overallProgress >= 25) {
                        $progressColorClass = 'bg-orange-500';
                    }
                @endphp
                <!-- Bar progres -->
                <div
                    class="{{ $progressColorClass }} h-full rounded-full transition-all duration-1000 ease-out"
                    {!! "sty" . "le='width: " . max($overallProgress, 0) . "%;'" !!}
                ></div>
                <!-- Persentase di tengah bar (absolute center) -->
                <span class="absolute inset-0 flex items-center justify-center text-xs font-bold text-white drop-shadow">
                    {{ number_format($overallProgress, 1) }}%
                </span>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
