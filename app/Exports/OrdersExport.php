<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OrdersExport implements FromQuery, WithHeadings, WithMapping, WithEvents
{
    protected $query;

    public function __construct(Builder $query = null)
    {
        $this->query = $query ?? Order::query();
    }

    public function query(): Builder
    {
        return $this->query->with(['assignedStaff', 'deliverySlot'])
            ->orderBy('id', 'desc');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $orders = $this->query()->get();

                foreach ($orders as $index => $order) {
                    $row = $index + 2; // +2 because of header row (1-indexed)
                    $paymentStatus = $order->payment_status instanceof \BackedEnum ? $order->payment_status->value : $order->payment_status;

                    if ($paymentStatus === 'paid') {
                        $sheet->getStyle("{$row}:{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'E8F5E9'], // Light green
                            ],
                        ]);
                    } elseif ($paymentStatus === 'pending' || $paymentStatus === 'failed') {
                        $sheet->getStyle("{$row}:{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FFEBEE'], // Light red
                            ],
                        ]);
                    }
                }

                // Style header row
                $sheet->getStyle('1:1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F3F4F6'],
                    ],
                ]);
            },
        ];
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Customer Name',
            'Customer Email',
            'Customer Phone',
            'Delivery Type',
            'Delivery Date',
            'Delivery Slot',
            'Shipping Address',
            'Shipping Suburb',
            'Shipping City',
            'Shipping State',
            'Shipping Postcode',
            'Payment Status',
            'Order Status',
            'Subtotal',
            'Shipping Cost',
            'Discount',
            'Grand Total',
            'Payment Method',
            'Assigned Staff',
            'Created At',
        ];
    }

    public function map($order): array
    {
        $deliverySlot = $order->deliverySlot ? $order->deliverySlot->start_time . ' - ' . $order->deliverySlot->end_time : '-';

        return [
            $order->order_number,
            $order->customer_name,
            $order->customer_email,
            $order->customer_phone,
            $order->delivery_type,
            $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d M Y') : '-',
            $deliverySlot,
            $order->shipping_address_line_1,
            $order->shipping_suburb,
            $order->shipping_city,
            $order->shipping_state,
            $order->shipping_postcode,
            $order->payment_status instanceof \BackedEnum ? $order->payment_status->value : $order->payment_status,
            $order->status instanceof \BackedEnum ? $order->status->value : $order->status,
            $order->subtotal,
            $order->shipping_cost,
            $order->discount,
            $order->grand_total,
            $order->payment_method,
            $order->assignedStaff?->name ?? '-',
            $order->created_at?->format('d M Y H:i'),
        ];
    }
}
