<?php

namespace App\Filament\Resources\Cms\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Schema;

class CmsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true),
                
                TextInput::make('slug')
                    ->required()
                    ->readOnly(),

                RichEditor::make('content')
                    ->required()
                    ->columnSpanFull(),

                FileUpload::make('image')
                    ->disk('public')
                    ->image()
                    ->nullable(),

                TextInput::make('meta_title')
                    ->nullable(),

                Textarea::make('description')
                    ->label('Meta Description')
                    ->nullable(),
            ]);
    }
}
