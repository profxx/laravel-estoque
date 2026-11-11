<?php

namespace App\Filament\Resources\Movimentos\Pages;

use App\Filament\Resources\Movimentos\MovimentoResource;
use App\Models\Produto;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateMovimento extends CreateRecord
{
    protected static string $resource = MovimentoResource::class;

    /**
     * Valida o estoque antes de criar o registro.
     */
    protected function beforeCreate(): void
    {
        $data = $this->data;
        $produto = Produto::find($data['produto_id'] ?? null);

        if (!$produto) {
            throw ValidationException::withMessages([
                'produto_id' => 'O produto selecionado não foi encontrado.',
            ]);
        }

        $quantidadeMovimento = (int) ($data['quantidade'] ?? 0);

        if ($data['tipo'] === 'saida' && $produto->quantidade < $quantidadeMovimento) {
            throw ValidationException::withMessages([
                'quantidade' => "A quantidade de saída é maior que o estoque atual ({$produto->quantidade}).",
            ]);
        }
    }

    /**
     * Atualiza o estoque e notifica após criar o registro.
     */
    protected function afterCreate(): void
    {
        $movimento = $this->getRecord();
        $produto = $movimento->produto;

        if (!$produto) {
            return;
        }

        $quantidade = (int) $movimento->quantidade;

        if ($movimento->tipo === 'entrada') {
            $produto->increment('quantidade', $quantidade);
        } else {
            $produto->decrement('quantidade', $quantidade);
        }

        // Atualiza a instância do model para pegar o valor correto do banco
        $produto->refresh();

        // Dispara notificação caso atinja o estoque mínimo
        if ($produto->quantidade <= $produto->estoquemin) {
            Notification::make()
                ->title('Alerta de Estoque Baixo!')
                ->body("O produto '{$produto->nome}' atingiu o estoque mínimo ({$produto->quantidade} restantes).")
                ->warning()
                ->persistent()
                ->send();
        }
    }
}