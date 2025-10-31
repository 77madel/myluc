<?php

namespace Modules\LMS\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\LMS\Repositories\Organization\ReportsRepository;
use Maatwebsite\Excel\Facades\Excel;
use Modules\LMS\Exports\ParticipantsReportExport;
use Modules\LMS\Exports\CoursesReportExport;
use Modules\LMS\Exports\UsageReportExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportsController extends Controller
{
    public function __construct(private ReportsRepository $reports)
    {
    }

    public function index(Request $request)
    {
        $org = Auth::user()->organization;
        $participantsData = $this->reports->participants($org->id, $request->all());
        $coursesData = $this->reports->courses($org->id, $request->all());
        $usageData = $this->reports->usage($org->id, $request->all());

        return view('portal::organization.reports.index', [
            'participants' => $participantsData['students'] ?? null,
            'participantsRows' => $participantsData['rows'] ?? collect(),
            'coursesPager' => $coursesData['courses'] ?? null,
            'coursesRows' => $coursesData['rows'] ?? collect(),
            'usageSeries' => $usageData['series'] ?? collect(),
            'date_from' => $usageData['date_from'] ?? null,
            'date_to' => $usageData['date_to'] ?? null,
        ]);
    }

    public function participants(Request $request)
    {
        $org = Auth::user()->organization;
        $data = $this->reports->participants($org->id, $request->all());
        return view('portal::organization.reports.partials.participants', $data);
    }

    public function courses(Request $request)
    {
        $org = Auth::user()->organization;
        $data = $this->reports->courses($org->id, $request->all());
        return view('portal::organization.reports.partials.courses', $data);
    }

    public function usage(Request $request)
    {
        $org = Auth::user()->organization;
        $data = $this->reports->usage($org->id, $request->all());
        return view('portal::organization.reports.partials.usage', $data);
    }

    public function exportParticipants(Request $request)
    {
        $org = Auth::user()->organization;
        $format = $request->get('format', 'xlsx');
        if ($format === 'pdf') {
            $view = view('portal::organization.reports.exports.participants', $this->reports->participants($org->id, $request->all()))->render();
            return Pdf::loadHTML($view)->download('participants-report.pdf');
        }
        return Excel::download(new ParticipantsReportExport($org, $request->all()), 'participants-report.' . $format);
    }

    public function exportCourses(Request $request)
    {
        $org = Auth::user()->organization;
        $format = $request->get('format', 'xlsx');
        if ($format === 'pdf') {
            $view = view('portal::organization.reports.exports.courses', $this->reports->courses($org->id, $request->all()))->render();
            return Pdf::loadHTML($view)->download('courses-report.pdf');
        }
        return Excel::download(new CoursesReportExport($org, $request->all()), 'courses-report.' . $format);
    }

    public function exportUsage(Request $request)
    {
        $org = Auth::user()->organization;
        $format = $request->get('format', 'xlsx');
        if ($format === 'pdf') {
            $view = view('portal::organization.reports.exports.usage', $this->reports->usage($org->id, $request->all()))->render();
            return Pdf::loadHTML($view)->download('usage-report.pdf');
        }
        return Excel::download(new UsageReportExport($org, $request->all()), 'usage-report.' . $format);
    }
}


