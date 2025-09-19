<?php

namespace App\Filament\Resources\Movimentos\Pages;

// CORREÇÃO 1: O "use" statement agora aponta para o namespace correto do seu Resource.
use App\Filament\Resources\Movimentos\MovimentoResource;
use App\Models\Produto;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateMovimento extends CreateRecord
{
    // CORREÇÃO 2: A referência da classe do Resource foi atualizada para o namespace correto.
    // O Laravel agora saberá exatamente onde encontrar seu MovimentoResource.
    protected static string $resource = MovimentoResource::class;

    /**
     * MÉTODO "GANCHO" (HOOK): mutateFormDataBeforeCreate
     * (O código aqui permanece o mesmo, pois a validação já usa a string 'saida')
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $produto = Produto::find($data['produto_id']);
        $quantidadeMovimento = (int)$data['quantidade'];

        if ($data['tipo'] === 'saida') {
            if ($produto->quantidade < $quantidadeMovimento) {
                throw ValidationException::withMessages([
                    'quantidade' => 'A quantidade de saída é maior que o estoque atual (' . $produto->quantidade . ').',
                ]);
            }
        }
        return $data;
    }

    /**
     * MÉTODO "GANCHO" (HOOK): afterCreate
     */
    protected function afterCreate(): void
    {
        $movimento = $this->getRecord();
        $produto = $movimento->produto;

        // Passo 2: Atualizar a quantidade de estoque do produto.
        // **AQUI ESTÁ A CORREÇÃO:**
        // Como agora estamos tratando `$movimento->tipo` como uma string simples (texto),
        // comparamos o valor diretamente, sem usar `->value`.
        if ($movimento->tipo === 'entrada') {
            $produto->quantidade += $movimento->quantidade;
        } else {
            $produto->quantidade -= $movimento->quantidade;
        }

        $produto->save();

        if ($produto->quantidade <= $produto->estoquemin) {
            Notification::make()
                ->title('Alerta de Estoque Baixo!')
                ->body("O produto '{$produto->nome}' atingiu o estoque mínimo.")
                ->warning()
                ->persistent()
                ->send();
        }
    }
}

