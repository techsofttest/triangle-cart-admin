<?php

namespace App\Exports;

use App\Models\Timesheet;
use App\Enums\TaskType;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TimesheetsExport implements FromQuery, WithHeadings, WithMapping, WithEvents
{
    protected $query;

    public function __construct(Builder $query = null)
    {
        $this->query = $query ?? Timesheet::query();
    }

    public function query(): Builder
    {
        // Clone the builder to avoid modifying the original and append relationships
        $exportQuery = (clone $this->query)->with(['staffUser']);

        // Remove pagination order if any, but since we want to export in correct sorted order:
        // if no order is specified, default to id desc.
        if (empty($exportQuery->getQuery()->orders)) {
            $exportQuery->orderBy('id', 'desc');
        }

        return $exportQuery;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
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
            'Date',
            'Staff Name',
            'Task',
            'Description',
            'Start Time',
            'End Time',
            'Duration',
        ];
    }

    public function map($timesheet): array
    {
        $taskLabel = $timesheet->task instanceof TaskType 
            ? $timesheet->task->getLabel() 
            : (is_string($timesheet->task) ? TaskType::tryFrom($timesheet->task)?->getLabel() ?? $timesheet->task : '-');

        $start = $timesheet->start_time ? $timesheet->start_time->format('g:i A') : '-';
        $end = $timesheet->end_time ? $timesheet->end_time->format('g:i A') : 'Running';

        // Calculate duration formatted
        $duration = \App\Filament\Pages\Timesheets::calculateDuration($timesheet);

        return [
            $timesheet->date ? $timesheet->date->format('d-m-Y') : '-',
            $timesheet->staffUser?->name ?? '-',
            $taskLabel,
            $timesheet->description ?? '-',
            $start,
            $end,
            $duration,
        ];
    }
}
