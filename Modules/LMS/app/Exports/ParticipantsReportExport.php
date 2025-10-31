<?php

namespace Modules\LMS\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ParticipantsReportExport implements FromView
{
    public function __construct(private $organization, private array $filters = [])
    {
    }

    public function view(): View
    {
        $data = app(\Modules\LMS\Repositories\Organization\ReportsRepository::class)
            ->participants($this->organization->id, $this->filters);
        return view('portal::organization.reports.exports.participants', $data);
    }
}


