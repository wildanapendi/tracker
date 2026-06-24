<x-filament-panels::page>
    <div wire:ignore>
        <div id="calendar" class="p-4 bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10"></div>
    </div>

    <!-- Inject script only for this page -->
    @push('scripts')
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                if (calendarEl) {
                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        themeSystem: 'standard',
                        events: '{{ route("calendar.events") }}',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,listWeek'
                        },
                        height: 700,
                        eventClick: function(info) {
                            if (info.event.url) {
                                window.open(info.event.url, '_self');
                                info.jsEvent.preventDefault();
                            }
                        }
                    });
                    calendar.render();
                }
            });
        </script>
    @endpush
</x-filament-panels::page>
