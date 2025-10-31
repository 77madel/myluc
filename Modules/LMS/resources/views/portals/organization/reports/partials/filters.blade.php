<form method="get" class="flex flex-col md:flex-row flex-wrap gap-3">
    <div>
        <label class="block text-sm font-medium mb-1">{{ translate('Du') }}</label>
        <input type="date" name="date_from" class="form-input" value="{{ request('date_from') }}">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">{{ translate('Au') }}</label>
        <input type="date" name="date_to" class="form-input" value="{{ request('date_to') }}">
    </div>
    <div class="flex flex-wrap gap-1.5 md:gap-2 mt-8 md:mt-14 pt-2 md:pt-4">
        <button class="btn b-solid btn-primary-solid rounded-full flex items-center gap-1 md:gap-1.5 px-1.5 md:px-2 py-0.5 md:py-1 text-[11px] md:text-xs h-7 md:h-8 leading-tight">
            <i class="ri-filter-3-line text-xs md:text-sm"></i>
            <span class="text-[10px] md:text-[11px]">{{ translate('Filtrer') }}</span>
        </button>
        <a href="{{ request()->url() }}" class="btn b-light btn-primary-light rounded-full flex items-center gap-1 md:gap-1.5 px-1.5 md:px-2 py-0.5 md:py-1 text-[11px] md:text-xs h-7 md:h-8 leading-tight">
            <i class="ri-refresh-line text-xs md:text-sm"></i>
            <span class="text-[10px] md:text-[11px]">{{ translate('Réinitialiser') }}</span>
        </a>
    </div>
    <div class="md:ml-auto flex flex-wrap gap-1.5 md:gap-2 mt-8 md:mt-14 pt-2 md:pt-4">
        <a class="btn b-light btn-secondary-light rounded-full flex items-center gap-1 md:gap-1.5 px-1.5 md:px-2 py-0.5 md:py-1 text-[11px] md:text-xs h-7 md:h-8 leading-tight" href="{{ route('organization.reports.participants.export', request()->all()+['format'=>'csv']) }}">
            <i class="ri-file-text-line text-xs md:text-sm"></i>
            <span class="text-[10px] md:text-[11px]">CSV</span>
        </a>
        <a class="btn b-light btn-secondary-light rounded-full flex items-center gap-1 md:gap-1.5 px-1.5 md:px-2 py-0.5 md:py-1 text-[11px] md:text-xs h-7 md:h-8 leading-tight" href="{{ route('organization.reports.participants.export', request()->all()+['format'=>'xlsx']) }}">
            <i class="ri-file-excel-2-line text-xs md:text-sm"></i>
            <span class="text-[10px] md:text-[11px]">XLSX</span>
        </a>
        <a class="btn b-light btn-secondary-light rounded-full flex items-center gap-1 md:gap-1.5 px-1.5 md:px-2 py-0.5 md:py-1 text-[11px] md:text-xs h-7 md:h-8 leading-tight" href="{{ route('organization.reports.participants.export', request()->all()+['format'=>'pdf']) }}">
            <i class="ri-file-pdf-2-line text-xs md:text-sm"></i>
            <span class="text-[10px] md:text-[11px]">PDF</span>
        </a>
    </div>
</form>



