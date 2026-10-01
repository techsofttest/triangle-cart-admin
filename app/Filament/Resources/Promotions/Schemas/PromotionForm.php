<?php

namespace App\Filament\Resources\Promotions\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;

class PromotionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Offer Name')
                            ->required()
                            ->maxLength(255),

                        Select::make('type')
                            ->label('Offer Type')
                            ->options([
                                'buy_x_get_x' => 'Buy X Get X',
                            ])
                            ->default('buy_x_get_x')
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        DateTimePicker::make('start_at')
                            ->label('Start Date & Time')
                            ->nullable(),

                        DateTimePicker::make('end_at')
                            ->label('End Date & Time')
                            ->nullable(),

                        FileUpload::make('offer_image')
                            ->label('Offer Image')
                            ->directory('promotions')
                            ->image()
                            ->nullable(),
                    ])
                    ->columns(1),

                Section::make('Eligibility Rules')
                    ->schema([
                        Select::make('eligibleProducts')
                            ->label('Eligible Products')
                            ->relationship(
                                name: 'eligibleProducts',
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
                            ->helperText('Customers purchasing these products will qualify for the promotion.'),

                        Select::make('eligibleCategories')
                            ->label('Eligible Categories')
                            ->relationship('eligibleCategories', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('Customers purchasing products from these categories will qualify for the promotion.'),

                        Select::make('eligibleBrands')
                            ->label('Eligible Brands')
                            ->relationship('eligibleBrands', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('Customers purchasing products from these brands will qualify for the promotion.'),

                        TextInput::make('eligible_quantity')
                            ->label('Eligible Quantity Required')
                            ->numeric()
                            ->minValue(1)
                            ->default(2)
                            ->required()
                            ->helperText('Minimum qualifying quantity required to trigger the offer (e.g. 2 for Buy 2).'),
                    ])
                    ->columns(1),

                Section::make('Free Product')
                    ->schema([
                        Select::make('freeProducts')
                            ->label('Free Products')
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
                            ->helperText('Select the free product(s). All active configured free products with available stock will be automatically added to qualifying carts (1 unit each).'),
                    ])
                    ->columns(1),
            ]);
    }
}
