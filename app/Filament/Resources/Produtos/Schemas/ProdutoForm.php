<?php

namespace App\Filament\Resources\Produtos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProdutoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nome')
                    ->required(),
                TextInput::make('quantidade')
                    ->required()
                    ->numeric(),
                TextInput::make('estoquemin')
                    ->required()
                    ->numeric(),
                TextInput::make('estoquemax')
                    ->required()
                    ->numeric(),
            ]);
    }
}
