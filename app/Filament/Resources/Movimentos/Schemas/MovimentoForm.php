<?php

namespace App\Filament\Resources\Movimentos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MovimentoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('produto_id')
                    ->required()
                    ->numeric(),
                TextInput::make('quantidade')
                    ->required()
                    ->numeric(),
                Select::make('tipo')
                            ->label('Tipo de Movimento')
                            ->options([
                                'entrada' => 'Entrada', // 'valor' => 'rótulo'
                                'saida' => 'Saída',
                            ])
                            ->required(),
            ]);
    }
}
