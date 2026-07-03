<?php

namespace App\Filament\Resources\HomePageSections\Schemas;

use App\Models\Brand;
use App\Models\Category;
use App\Models\HomePageSection;
use App\Models\Product;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HomePageSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Section Setup')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->helperText('Internal admin name.')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('title')
                            ->helperText('Visible heading on the frontend.')
                            ->maxLength(255),
                        Select::make('type')
                            ->required()
                            ->options([
                                HomePageSection::TYPE_PROMO_SLIDER => 'Promo Slider',
                                HomePageSection::TYPE_PRODUCT_ROW => 'Product Row',
                                HomePageSection::TYPE_CATEGORY_GRID => 'Category Grid',
                                HomePageSection::TYPE_SUB_CATEGORIES => 'Sub Categories',
                                HomePageSection::TYPE_BRANDS => 'Brands',
                                HomePageSection::TYPE_ADVERTISEMENT => 'Advertisement',
                                HomePageSection::TYPE_TOP_OFFERS => 'Top Offers',
                            ])
                            ->default(HomePageSection::TYPE_PRODUCT_ROW),
                        Select::make('source')
                            ->required()
                            ->options([
                                HomePageSection::SOURCE_BANNERS => 'Banners',
                                HomePageSection::SOURCE_LATEST_PRODUCTS => 'Latest Products',
                                HomePageSection::SOURCE_FEATURED_PRODUCTS => 'Featured Products',
                                HomePageSection::SOURCE_TOP_OFFERS => 'Top Offers',
                                HomePageSection::SOURCE_CATEGORIES => 'Categories',
                                HomePageSection::SOURCE_BRANDS => 'Brands',
                                HomePageSection::SOURCE_CUSTOM_PRODUCTS => 'Selected Products',
                                HomePageSection::SOURCE_CUSTOM_CATEGORIES => 'Selected Categories',
                                HomePageSection::SOURCE_CUSTOM_BRANDS => 'Selected Brands',
                                HomePageSection::SOURCE_ADVERTISEMENT => 'Advertisement',
                            ])
                            ->default(HomePageSection::SOURCE_LATEST_PRODUCTS),
                        TextInput::make('subtitle')
                            ->maxLength(255),
                        TextInput::make('background_color')
                            ->placeholder('bg-[#0c4a9e] or #ffffff')
                            ->maxLength(255),
                        TextInput::make('link_label')
                            ->maxLength(255),
                        TextInput::make('link_url')
                            ->maxLength(255),
                        TextInput::make('item_limit')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(48)
                            ->default(12),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
                Section::make('Manual Items')
                    ->columns(1)
                    ->schema([
                        Select::make('product_ids')
                            ->label('Products')
                            ->multiple()
                            ->searchable()
                            ->options(fn () => Product::query()->orderBy('name')->pluck('name', 'id')->all()),
                        Select::make('category_ids')
                            ->label('Categories')
                            ->multiple()
                            ->searchable()
                            ->options(fn () => Category::query()->orderBy('name')->pluck('name', 'id')->all()),
                        Select::make('brand_ids')
                            ->label('Brands')
                            ->multiple()
                            ->searchable()
                            ->options(fn () => Brand::query()->orderBy('name')->pluck('name', 'id')->all()),
                        Textarea::make('settings')
                            ->helperText('Optional JSON for frontend-specific settings.')
                            ->rows(4)
                            ->dehydrateStateUsing(fn ($state) => blank($state) ? null : json_decode($state, true))
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state),
                    ]),
            ]);
    }
}
