<?php

namespace App\Filament\Resources\Occasions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OccasionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
