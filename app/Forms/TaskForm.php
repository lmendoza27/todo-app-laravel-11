<?php

namespace App\Forms;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class TaskForm extends FormRequest
{
    // Define las reglas de validación
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',  // Título es obligatorio y debe ser una cadena
            'description' => 'required|string',      // Descripción es opcional y debe ser una cadena
        ];
    }

    // Autoriza la solicitud
    public function authorize()
    {
        return true; // Cambia esto si necesitas implementar lógica de autorización
    }

    // (Opcional) Personaliza los mensajes de error
    public function messages()
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'description.required' => 'La descripción es obligatoria.'
        ];
    }
    
}