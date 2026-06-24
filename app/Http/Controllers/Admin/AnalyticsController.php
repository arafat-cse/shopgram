<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AnalyticsExport;
use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AnalyticsController extends Controller
{
    public function __construct(private AnalyticsService $analyticsService) {}

    public function index(Request $request)
    {
        $analytics = $this->analyticsService->getAnalyticsData($request);

        return view('admin.analytics.index', $analytics);
    }

    public function getAnalyticsData(Request $request): array
    {
        return $this->analyticsService->getAnalyticsData($request);
    }

    public function getSalesChart(Request $request): array
    {
        $range = $this->analyticsService->getDateRange($request);
        return $this->analyticsService->getSalesChart($range['from'], $range['to']);
    }

    public function getProfitChart(Request $request): array
    {
        $range = $this->analyticsService->getDateRange($request);
        return $this->analyticsService->getProfitChart($range['from'], $range['to']);
    }

    public function getTopSellingProducts(Request $request)
    {
        $range = $this->analyticsService->getDateRange($request);
        return $this->analyticsService->getTopSellingProducts($range['from'], $range['to']);
    }

    public function exportPdf(Request $request)
    {
        abort_unless($request->user()->can('analytics.report.export'), 403);

        $analytics = $this->analyticsService->getAnalyticsData($request);
        $pdf = Pdf::loadView('admin.analytics.export-pdf', $analytics)->setPaper('a4', 'landscape');

        return $pdf->download('analytics-report.pdf');
    }

    public function exportExcel(Request $request)
    {
        abort_unless($request->user()->can('analytics.report.export'), 403);

        return Excel::download(new AnalyticsExport($this->analyticsService->getAnalyticsData($request)), 'analytics-report.xlsx');
    }
}
