<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Radio;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;

class Timesheets extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $title = 'Timesheets';

    protected static ?string $navigationLabel = 'Timesheets';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.timesheets';

    /**
     * Get the default/dummy timesheet records.
     */
    protected function getDefaultRecords(): array
    {
        return [
            [
                'id' => 1,
                'date' => '2026-06-12',
                'person' => 'Joe',
                'task' => 'Order Packing',
                'description' => 'Pack orders',
                'start_time' => '13:00:00',
                'end_time' => '14:00:00',
            ],
            [
                'id' => 2,
                'date' => '2026-06-12',
                'person' => 'Joe',
                'task' => 'Delivery',
                'description' => 'Deliver products',
                'start_time' => '14:00:00',
                'end_time' => '15:00:00',
            ],
        ];
    }

    /**
     * Get the current records stored in the session.
     */
    protected function getSessionRecords(): array
    {
        if (!session()->has('timesheets_demo_data')) {
            session()->put('timesheets_demo_data', $this->getDefaultRecords());
        }

        return session()->get('timesheets_demo_data');
    }

    /**
     * Save the records back to the session.
     */
    protected function saveSessionRecords(array $records): void
    {
        session()->put('timesheets_demo_data', $records);
    }

    /**
     * Helper to format time range.
     */
    public static function formatTimeRange(array $record): string
    {
        $start = isset($record['start_time']) ? Carbon::parse($record['start_time'])->format('g:i A') : '';
        if (empty($record['end_time'])) {
            return "{$start} - Running";
        }
        $end = Carbon::parse($record['end_time'])->format('g:i A');
        return "{$start} - {$end}";
    }

    /**
     * Helper to calculate duration.
     */
    public static function calculateDuration(array $record): string
    {
        if (empty($record['end_time'])) {
            return 'Running';
        }

        $start = Carbon::parse($record['start_time']);
        $end = Carbon::parse($record['end_time']);

        // Handle overnight crossing if any
        if ($end->lt($start)) {
            $end->addDay();
        }

        $diffMinutes = $start->diffInMinutes($end);
        
        if ($diffMinutes < 60) {
            return "{$diffMinutes}-Mins";
        }

        $hours = round($diffMinutes / 60, 1);
        $hourLabel = $hours == 1 ? 'Hour' : 'Hours';
        
        // Match specific format requirement if exact hour
        if (fmod($hours, 1) == 0) {
            $hours = (int)$hours;
        }

        return "{$hours}-{$hourLabel}";
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(function () {
                $records = $this->getSessionRecords();

                // Apply filters manually
                $dateFilterState = $this->getTableFilterState('date');
                if (!empty($dateFilterState['date'])) {
                    $dateFilter = $dateFilterState['date'];
                    $records = array_filter($records, fn ($rec) => $rec['date'] === $dateFilter);
                }

                $personFilterState = $this->getTableFilterState('person');
                if (!empty($personFilterState['person'])) {
                    $personFilter = $personFilterState['person'];
                    $records = array_filter($records, fn ($rec) => $rec['person'] === $personFilter);
                }

                // Sort by ID or date/time descending to show latest entries first
                usort($records, function ($a, $b) {
                    return $b['id'] <=> $a['id'];
                });

                return $records;
            })
            ->columns([
                TextColumn::make('date')
                    ->label('Date')
                    ->date('d-m-Y'),
                TextColumn::make('person')
                    ->label('Staff')
                    ->badge()
                    ->color('info'),
                TextColumn::make('task')
                    ->label('Task')
                    ->weight('bold'),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50),
                TextColumn::make('time_range')
                    ->label('Time')
                    ->state(fn (array $record) => static::formatTimeRange($record)),
                TextColumn::make('duration')
                    ->label('Duration')
                    ->badge()
                    ->color(fn (array $record) => empty($record['end_time']) ? 'warning' : 'success')
                    ->state(fn (array $record) => static::calculateDuration($record)),
            ])
            ->headerActions([
                // Add Timesheet Action
                Action::make('add')
                    ->label('Add Timesheet')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Add Timesheet Manually / Timer')
                    ->modalWidth('lg')
                    ->form([
                        Select::make('person')
                            ->label('Select Person')
                            ->options([
                                'Joe' => 'Joe',
                                'Jane' => 'Jane',
                                'Bob' => 'Bob',
                                'Alice' => 'Alice',
                            ])
                            ->required()
                            ->default('Joe'),
                        Select::make('task')
                            ->label('Select Task')
                            ->options([
                                'Order Packing' => 'Order Packing',
                                'Delivery' => 'Delivery',
                                'Inventory Count' => 'Inventory Count',
                                'Customer Support' => 'Customer Support',
                                'General Maintenance' => 'General Maintenance',
                            ])
                            ->required(),
                        Textarea::make('description')
                            ->label('Task Description')
                            ->placeholder('Describe the task performed...')
                            ->required(),
                        Radio::make('entry_mode')
                            ->label('Time Entry Mode')
                            ->options([
                                'timer' => 'Start Timer Now (Task Runs)',
                                'manual' => 'Enter Date & Time Manually',
                            ])
                            ->default('manual')
                            ->live(),
                        DatePicker::make('date')
                            ->label('Date')
                            ->default(now()->toDateString())
                            ->visible(fn (callable $get) => $get('entry_mode') === 'manual')
                            ->required(fn (callable $get) => $get('entry_mode') === 'manual'),
                        TimePicker::make('start_time')
                            ->label('Start Time')
                            ->default(now()->format('H:i'))
                            ->visible(fn (callable $get) => $get('entry_mode') === 'manual')
                            ->required(fn (callable $get) => $get('entry_mode') === 'manual'),
                        TimePicker::make('end_time')
                            ->label('End Time (Optional)')
                            ->visible(fn (callable $get) => $get('entry_mode') === 'manual'),
                    ])
                    ->action(function (array $data) {
                        $records = $this->getSessionRecords();
                        $newId = count($records) > 0 ? max(array_column($records, 'id')) + 1 : 1;

                        if ($data['entry_mode'] === 'timer') {
                            $newRecord = [
                                'id' => $newId,
                                'date' => now()->toDateString(),
                                'person' => $data['person'],
                                'task' => $data['task'],
                                'description' => $data['description'],
                                'start_time' => now()->format('H:i:s'),
                                'end_time' => null,
                            ];
                        } else {
                            $newRecord = [
                                'id' => $newId,
                                'date' => $data['date'],
                                'person' => $data['person'],
                                'task' => $data['task'],
                                'description' => $data['description'],
                                'start_time' => $data['start_time'],
                                'end_time' => $data['end_time'] ?: null,
                            ];
                        }

                        $records[] = $newRecord;
                        $this->saveSessionRecords($records);

                        Notification::make()
                            ->title($data['entry_mode'] === 'timer' ? 'Timer started!' : 'Timesheet entry added!')
                            ->success()
                            ->send();
                    }),

                // Dummy Export Action
                Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function () {
                        Notification::make()
                            ->title('Timesheets exported successfully! (Demo Export)')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                // Stop Timer Action
                Action::make('stop_timer')
                    ->label('Stop')
                    ->icon('heroicon-o-stop')
                    ->color('danger')
                    ->button()
                    ->visible(fn (array $record) => empty($record['end_time']))
                    ->action(function (array $record) {
                        $records = $this->getSessionRecords();
                        foreach ($records as &$rec) {
                            if ($rec['id'] === $record['id']) {
                                $rec['end_time'] = now()->format('H:i:s');
                                break;
                            }
                        }
                        $this->saveSessionRecords($records);

                        Notification::make()
                            ->title('Task timer stopped!')
                            ->success()
                            ->send();
                    }),

                // Edit Action
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->modalHeading('Edit Timesheet')
                    ->modalWidth('lg')
                    ->fillForm(fn (array $record): array => [
                        'person' => $record['person'],
                        'task' => $record['task'],
                        'description' => $record['description'],
                        'date' => $record['date'],
                        'start_time' => $record['start_time'],
                        'end_time' => $record['end_time'],
                    ])
                    ->form([
                        Select::make('person')
                            ->label('Select Person')
                            ->options([
                                'Joe' => 'Joe',
                                'Jane' => 'Jane',
                                'Bob' => 'Bob',
                                'Alice' => 'Alice',
                            ])
                            ->required(),
                        Select::make('task')
                            ->label('Select Task')
                            ->options([
                                'Order Packing' => 'Order Packing',
                                'Delivery' => 'Delivery',
                                'Inventory Count' => 'Inventory Count',
                                'Customer Support' => 'Customer Support',
                                'General Maintenance' => 'General Maintenance',
                            ])
                            ->required(),
                        Textarea::make('description')
                            ->label('Task Description')
                            ->required(),
                        DatePicker::make('date')
                            ->label('Date')
                            ->required(),
                        TimePicker::make('start_time')
                            ->label('Start Time')
                            ->required(),
                        TimePicker::make('end_time')
                            ->label('End Time (Optional)'),
                    ])
                    ->action(function (array $record, array $data) {
                        $records = $this->getSessionRecords();
                        foreach ($records as &$rec) {
                            if ($rec['id'] === $record['id']) {
                                $rec['person'] = $data['person'];
                                $rec['task'] = $data['task'];
                                $rec['description'] = $data['description'];
                                $rec['date'] = $data['date'];
                                $rec['start_time'] = $data['start_time'];
                                $rec['end_time'] = $data['end_time'] ?: null;
                                break;
                            }
                        }
                        $this->saveSessionRecords($records);

                        Notification::make()
                            ->title('Timesheet updated!')
                            ->success()
                            ->send();
                    }),

                // Delete Action
                Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (array $record) {
                        $records = $this->getSessionRecords();
                        $records = array_values(array_filter($records, fn ($rec) => $rec['id'] !== $record['id']));
                        $this->saveSessionRecords($records);

                        Notification::make()
                            ->title('Timesheet deleted!')
                            ->success()
                            ->send();
                    }),
            ])
            ->filters([
                Filter::make('date')
                    ->form([
                        DatePicker::make('date')
                            ->label('Filter By Date'),
                    ])
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['date']) {
                            return null;
                        }
                        return 'Date: ' . Carbon::parse($data['date'])->format('d-m-Y');
                    }),

                Filter::make('person')
                    ->form([
                        Select::make('person')
                            ->label('Filter By Staff')
                            ->options([
                                'Joe' => 'Joe',
                                'Jane' => 'Jane',
                                'Bob' => 'Bob',
                                'Alice' => 'Alice',
                            ]),
                    ])
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['person']) {
                            return null;
                        }
                        return 'Staff: ' . $data['person'];
                    }),
            ]);
    }
}
