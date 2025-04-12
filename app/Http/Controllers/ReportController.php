<?php

namespace App\Http\Controller;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function showReportForm($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        return view('service_buyer.service_details.report_form', compact('service'));
    }
}