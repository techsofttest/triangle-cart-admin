<?php

namespace App\Filament\Resources\Advertisements\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

class AdvertisementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('title'),
                FileUpload::make('banner')
                    ->image()
                    ->disk('public')
                    ->required(),
                TextInput::make('location')
                    ->disabled(),
                TextInput::make('url')
                    ->url()
                    ->required(),
            ]);
    }
}
