<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AnalyticsExport implements FromView
{
    public function __construct(private array $analytics) {}

    public function view(): View
    {
        return view('admin.analytics.export-excel', $this->analytics);
    }
}
