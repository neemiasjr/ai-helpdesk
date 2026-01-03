<?php

namespace App\Services\Ai\Prompts;

use App\Models\Ticket;

class TicketPrompts
{
    public static function summarizeSystem(): string
    {
        return "Você é um assistente de suporte. Gere um resumo curto e objetivo do ticket, em PT-PT. "
            . "O resumo deve ser claro, conciso e útil para entender rapidamente o problema do cliente.";
    }

    public static function summarizeUser(Ticket $ticket): string
    {
        return "Título do Ticket: {$ticket->title}\n\n"
            ."Descrição:\n{$ticket->description}\n\n"
            ."Por favor, gere um resumo conciso (máximo 4-5 linhas) que capture a essência do problema descrito. "
            . "O resumo deve ser claro e permitir que alguém entenda rapidamente a situação do cliente sem precisar ler todo o conteúdo.";
    }

    public static function classifySystem(): string
    {
        return "Classifique tickets de helpdesk. Responda APENAS em JSON válido.";
    }

    public static function classifyUser(Ticket $ticket): string
    {
        return "Ticket:\nTítulo: {$ticket->title}\nDescrição:\n{$ticket->description}\n\n"
            ."Responda em JSON com:\n"
            ."{\"category\":\"...\",\"priority\":\"low|medium|high|urgent\",\"tags\":[\"...\" ]}";
    }

    public static function suggestReplySystem(): string
    {
        return "Você é um agente de suporte. Escreva uma resposta educada em PT-PT, curta e prática.";
    }

    public static function suggestReplyUser(Ticket $ticket): string
    {
        return "Ticket:\nTítulo: {$ticket->title}\nDescrição:\n{$ticket->description}\n\n"
            ."Escreva um rascunho de resposta para o cliente.";
    }
}
