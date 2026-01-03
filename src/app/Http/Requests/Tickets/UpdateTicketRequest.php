<?php

namespace App\Http\Requests\Tickets;

use App\Domain\Tickets\Enums\TicketPriority;
use App\Domain\Tickets\Enums\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('ticket'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $statusValues = array_column(TicketStatus::cases(), 'value');
        $priorityValues = array_column(TicketPriority::cases(), 'value');

        return [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:20000'],
            'status' => ['required', Rule::in($statusValues)],
            'priority' => ['required', Rule::in($priorityValues)],
            'category' => ['nullable', 'string', 'max:120'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'O título do ticket é obrigatório.',
            'title.max' => 'O título não pode ter mais de 200 caracteres.',
            'description.required' => 'A descrição do ticket é obrigatória.',
            'description.max' => 'A descrição não pode ter mais de 20000 caracteres.',
            'status.required' => 'O status do ticket é obrigatório.',
            'status.in' => 'O status informado é inválido.',
            'priority.required' => 'A prioridade do ticket é obrigatória.',
            'priority.in' => 'A prioridade informada é inválida.',
            'category.max' => 'A categoria não pode ter mais de 120 caracteres.',
            'assigned_to.exists' => 'O usuário atribuído não foi encontrado.',
        ];
    }
}

