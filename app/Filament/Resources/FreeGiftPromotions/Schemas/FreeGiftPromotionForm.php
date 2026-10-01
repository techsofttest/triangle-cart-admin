<?php

namespace App\Filament\Resources\FreeGiftPromotions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FreeGiftPromotionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Promotion Name')
                            ->required()
                            ->maxLength(255),

                        Select::make('status')
                            ->label('Status')
                        ->options([                         
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->default('active')
                            ->required(),

                        DateTimePicker::make('starts_at')
                            ->label('Start Date & Time')
                            ->nullable(),

                        DateTimePicker::make('ends_at')
                            ->label('End Date & Time')
                            ->nullable(),

                        TextInput::make('minimum_cart_amount')
                            ->label('Minimum Cart Amount')
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->default(0.00)
                            ->required()
                            ->helperText('Minimum cart subtotal required to qualify for the free gift.'),
                    ])
                    ->columns(2),

                Section::make('Excluded Categories')
                    ->schema([
                        Select::make('excludedCategories')
                            ->label('Excluded Categories')
                            ->relationship('excludedCategories', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('If the cart contains any product from these categories, the entire order becomes ineligible for a free gift.'),
                    ]),

                Section::make('Free Gift Products')
                    ->schema([
                        Select::make('freeProducts')
                            ->label('Free Gift Products')
                            ->relationship(
                                name: 'freeProducts',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query->with('brand')
                            )
                            ->getSearchResultsUsing(function (string $search): array {
                                return \App\Models\Product::query()
                                    ->with('brand')
                                    ->where(function ($query) use ($search) {
                                        $query->where('products.name', 'like', "%{$search}%")
                                            ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', "%{$search}%"));
                                    })
                                    ->get()
                                    ->mapWithKeys(fn ($product) => [
                                        $product->id => $product->brand ? "{$product->name} ({$product->brand->name})" : $product->name,
                                    ])
                                    ->toArray();
                            })
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->brand ? "{$record->name} ({$record->brand->name})" : $record->name)
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->optionsLimit(1000)
                            ->required()
                            ->helperText('Select the product(s) offered as free gifts'),
                    ]),
            ]);
    }
}
