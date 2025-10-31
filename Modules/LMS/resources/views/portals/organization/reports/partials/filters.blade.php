<form method="get" class="flex flex-col md:flex-row md:items-end gap-3">
    <div>
        <label class="block text-sm font-medium mb-1">{{ translate('Du') }}</label>
        <input type="date" name="date_from" class="form-input" value="{{ request('date_from') }}">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">{{ translate('Au') }}</label>
        <input type="date" name="date_to" class="form-input" value="{{ request('date_to') }}">
    </div>
    <div class="flex gap-2">
        <button class="btn b-solid btn-primary-solid">{{ translate('Filtrer') }}</button>
        <a href="{{ request()->url() }}" class="btn b-light btn-primary-light">{{ translate('Réinitialiser') }}</a>
    </div>
    <div class="md:ml-auto flex gap-2">
        <a class="btn b-light btn-secondary-light" href="{{ route('organization.reports.participants.export', request()->all()+['format'=>'csv']) }}">CSV</a>
        <a class="btn b-light btn-secondary-light" href="{{ route('organization.reports.participants.export', request()->all()+['format'=>'xlsx']) }}">XLSX</a>
        <a class="btn b-light btn-secondary-light" href="{{ route('organization.reports.participants.export', request()->all()+['format'=>'pdf']) }}">PDF</a>
    </div>
</form>


