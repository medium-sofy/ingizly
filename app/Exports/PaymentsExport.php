<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        return Payment::with(['order.user', 'order.service'])
            ->when($this->request->filled('start_date'), function ($query) {
                return $query->whereDate('created_at', '>=', $this->request->start_date);
            })
            ->when($this->request->filled('end_date'), function ($query) {
                return $query->whereDate('created_at', '<=', $this->request->end_date);
            })
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Order ID',
            'User',
            'Service',
            'Amount',
            'Currency',
            'Status',
            'Transaction ID',
            'Created At',
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->id,
            $payment->order_id,
            $payment->order?->user?->name ?? 'N/A',
            $payment->order?->service?->title ?? 'N/A',
            $payment->amount,
            $payment->currency,
            $payment->payment_status,
            $payment->transaction_id,
            $payment->created_at->format('Y-m-d H:i:s'),
        ];
    }
} 