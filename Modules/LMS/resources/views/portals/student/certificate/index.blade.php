<x-dashboard-layout>
    <x-slot:title> {{ translate('My Certificate') }} </x-slot:title>
    <!-- BREADCRUMB -->
    <x-portal::admin.breadcrumb title="My All Certificate" page-to="Certificate" />
    <!-- Start Main Content -->
    <div class="card overflow-hidden">
        @if ($certificates->count() > 0)
            <x-portal::certificates.certificate-list :certificates=$certificates />
        @else
            <x-portal::admin.empty-card title="You have no Certificate" />
        @endif
    </div>

    @if(session('success'))
        <script>
            if (typeof toastr !== 'undefined') {
                toastr.success('{{ session('success') }}');
            } else {
                alert('{{ session('success') }}');
            }
        </script>
    @endif

    @if(session('error'))
        <script>
            if (typeof toastr !== 'undefined') {
                toastr.error('{{ session('error') }}');
            } else {
                alert('{{ session('error') }}');
            }
        </script>
    @endif

    @if(session('warning'))
        <script>
            if (typeof toastr !== 'undefined') {
                toastr.warning('{{ session('warning') }}');
            } else {
                alert('{{ session('warning') }}');
            }
        </script>
    @endif
</x-dashboard-layout>

