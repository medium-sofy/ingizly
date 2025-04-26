<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PaymentsExport;
use Illuminate\Http\Request;

class PaymentExportController extends Controller
{
    public function exportPDF(Request $request)
    {
        $payments = Payment::with(['user', 'service'])
            ->when($request->filled('start_date'), function ($query) use ($request) {
                return $query->whereDate('created_at', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function ($query) use ($request) {
                return $query->whereDate('created_at', '<=', $request->end_date);
            })
            ->get();

        $pdf = PDF::loadView('admin.payments.export-pdf', compact('payments'));
        return $pdf->download('payments-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportCSV(Request $request)
    {
        return Excel::download(new PaymentsExport($request), 'payments-' . now()->format('Y-m-d') . '.csv');
    }
} 