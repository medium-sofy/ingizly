<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Violation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Violation::with(['service.provider.user', 'user'])
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhereHas('service', function($q) use ($search) {
                      $q->where('title', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Apply status filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Apply date range filter
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $violations = $query->paginate(10);

        return view('admin.reports.index', compact('violations'));
    }

    public function show(Violation $violation)
    {
        $violation->load(['service.provider.user', 'user']);
        return view('admin.reports.show', compact('violation'));
    }

    public function update(Request $request, Violation $violation)
    {
        $request->validate([
            'status' => 'required|in:pending,investigating,resolved,dismissed'
        ]);
        $oldStatus = $violation->status;

        $violation->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes
        ]);

      /// Notify buyer about status change
    if ($oldStatus != $request->status) {
        $serviceTitle = $violation->service->title;
        
        // Create a more professional message based on the status
        $statusMessages = [
            'pending' => "We've received your report regarding '{$serviceTitle}' and it's currently under review.",
            'investigating' => "Your report regarding '{$serviceTitle}' is now being investigated by our team.",
            'resolved' => "Your report regarding '{$serviceTitle}' has been resolved. Thank you for helping us maintain our community standards.",
            'dismissed' => "After careful review, we've determined that your report regarding '{$serviceTitle}' doesn't violate our policies."
        ];

        $message = $statusMessages[$request->status] ?? "The status of your report regarding '{$serviceTitle}' has been updated to {$request->status}.";

        Notification::create([
            'user_id' => $violation->user_id,
            'title' => 'Report Status Update',
            'content' => json_encode([
                'message' => $message,
                'violation_id' => $violation->id,
                'service_id' => $violation->service_id,
                'status' => $request->status
            ]),
            'notification_type' => 'system',
            'is_read' => false
        ]);
    }

    return redirect()->route('admin.reports.index')
        ->with('success', 'Violation status updated successfully.');
}
}