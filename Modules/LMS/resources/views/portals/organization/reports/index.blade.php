<x-dashboard-layout>
    <x-slot:title>{{ translate('Progression Étudiants') }}</x-slot:title>

    <div class="px-4">
        <x-portal::admin.breadcrumb title="{{ translate('Progression Étudiants') }}" page-to="{{ translate('Rapports') }}" />

        <div class="card mb-4">
            <div class="p-4">
                @include('portal::organization.reports.partials.filters')
            </div>
        </div>

        <div class="card">
            <div class="p-4 border-b border-gray-200 dark:border-dark-border-four">
                <div class="flex items-center gap-2">
                    <button class="tab-btn btn b-light btn-primary-light btn-sm rounded-full [&.active]:bg-primary [&.active]:text-white active" data-target="#tab-participants">{{ translate('Participants') }}</button>
                    <button class="tab-btn btn b-light btn-primary-light btn-sm rounded-full [&.active]:bg-primary [&.active]:text-white" data-target="#tab-courses">{{ translate('Cours') }}</button>
                    <button class="tab-btn btn b-light btn-primary-light btn-sm rounded-full [&.active]:bg-primary [&.active]:text-white" data-target="#tab-usage">{{ translate('Usage') }}</button>
                </div>
            </div>
            <div class="p-4">
                <div id="tab-participants" class="tab-pane !block">
                    @php $rows = $participantsRows ?? collect(); $students = $participants ?? null; @endphp
                    @include('portal::organization.reports.partials.participants')
                </div>
                <div id="tab-courses" class="tab-pane hidden">
                    @php $rows = $coursesRows ?? collect(); $courses = $coursesPager ?? null; @endphp
                    @include('portal::organization.reports.partials.courses')
                </div>
                <div id="tab-usage" class="tab-pane hidden">
                    @php $series = $usageSeries ?? collect(); @endphp
                    @include('portal::organization.reports.partials.usage')
                </div>
            </div>
        </div>
    </div>

    @push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const btns = document.querySelectorAll('.tab-btn');
            const panes = document.querySelectorAll('.tab-pane');
            btns.forEach(btn => {
                btn.addEventListener('click', () => {
                    btns.forEach(b => b.classList.remove('active'));
                    panes.forEach(p => p.classList.add('hidden'));
                    btn.classList.add('active');
                    const target = document.querySelector(btn.getAttribute('data-target'));
                    if(target){
                        target.classList.remove('hidden');
                        target.classList.add('!block');
                    }
                });
            });
        });
    </script>
    @endpush
</x-dashboard-layout>


