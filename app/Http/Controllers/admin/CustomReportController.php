<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.custom.index');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'nullable|string' // This 'status' is for Order status in the form
        ]);

        $reportType = $request->input('report_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userId = $request->input('user_id');
        $orderStatus = $request->input('status'); // Renamed to avoid confusion with payment status

        $data = match($reportType) {
            'user_transactions' => $this->getUserTransactions($userId, $startDate, $endDate),
            'pending_transactions' => $this->getPendingTransactions($startDate, $endDate),
            'user_orders' => $this->getUserOrders($userId, $startDate, $endDate),
            'status_orders' => $this->getOrdersByStatus($orderStatus, $startDate, $endDate), // Use $orderStatus here
            default => collect([])
        };

        return view('admin.reports.custom.results', compact('data', 'reportType'));
    }

    private function getUserTransactions($userId, $startDate, $endDate)
    {
        $query = Payment::query()
            ->whereHas('order', function($q) use ($userId) {
                $q->where('buyer_id', $userId); // Assuming 'buyer_id' is the foreign key in orders to users
            });

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->with(['order.buyer', 'order.service']) // Eager load order.buyer instead of order.user
            ->get()
            ->map(function($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'payment_status' => $payment->payment_status, // Use the correct column name
                    'created_at' => $payment->created_at,
                    'user' => $payment->order->buyer->name, // Access the buyer's name
                    'service' => $payment->order->service->title,
                    'provider' => $payment->order->service->provider->user->name, // Assuming service has provider and provider has user relation
                    'order_id' => $payment->order_id // Include order ID
                ];
            });
    }

    private function getPendingTransactions($startDate, $endDate)
    {
        $query = Payment::query()->where('payment_status', 'pending'); // <-- Corrected column name

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->with(['order.buyer', 'order.service']) // Eager load order.buyer
            ->get()
            ->map(function($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                     'payment_status' => $payment->payment_status, // Use the correct column name
                    'created_at' => $payment->created_at,
                    'user' => $payment->order->buyer->name, // Access the buyer's name
                    'service' => $payment->order->service->title,
                    'provider' => $payment->order->service->provider->user->name, // Assuming service has provider and provider has user relation
                     'order_id' => $payment->order_id // Include order ID
                ];
            });
    }

    private function getUserOrders($userId, $startDate, $endDate)
    {
        $query = Order::query()->where('buyer_id', $userId); // Assuming 'buyer_id' is the foreign key to users

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->with(['buyer', 'service.provider.user']) // Eager load buyer and service.provider.user
            ->get()
            ->map(function($order) {
                return [
                    'id' => $order->id,
                    'status' => $order->status,
                    'total_amount' => $order->total_amount,
                    'created_at' => $order->created_at,
                    'user' => $order->buyer->name, // Access the buyer's name
                    'service' => $order->service->title,
                    'provider' => $order->service->provider->user->name, // Access provider name
                    'scheduled_date' => $order->scheduled_date,
                    'scheduled_time' => $order->scheduled_time,
                ];
            });
    }

    private function getOrdersByStatus($status, $startDate, $endDate)
    {
        $query = Order::query()->where('status', $status);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->with(['buyer', 'service.provider.user']) // Eager load buyer and service.provider.user
            ->get()
            ->map(function($order) {
                return [
                    'id' => $order->id,
                    'status' => $order->status,
                    'total_amount' => $order->total_amount,
                    'created_at' => $order->created_at,
                    'user' => $order->buyer->name, // Access the buyer's name
                    'service' => $order->service->title,
                    'provider' => $order->service->provider->user->name, // Access provider name
                    'scheduled_date' => $order->scheduled_date,
                    'scheduled_time' => $order->scheduled_time,
                ];
            });
    }
}
