<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TecnologiaPaginationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'skip'        => 'required|integer|min:0',
            'take'        => 'required|integer|min:1',
            'orderColumn' => 'nullable|string|in:id,nombre,descripcion,estado',
            'order'       => 'nullable|string|in:asc,desc',
            'search'      => 'nullable|string|max:255'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'skip.required'         => 'El número de página a saltar es obligatorio.',
            'skip.integer'          => 'El número de pagina a saltar debe ser un número entero.',
            'skip.min'              => 'El número de pagina a saltar debe ser mayor o igual a 1.',
            'take.required'         => 'El número de elementos por página es obligatorio.',
            'take.integer'          => 'El número de elementos por página debe ser un número entero.',
            'take.min'              => 'El número de elementos por página debe ser mayor o igual a 1.',
            'orderColumn.in'        => 'La columna de ordenamiento debe ser una de las disponibles.',
            'order.in'              => 'El ordenamiento debe ser ascendente o descendente.',
            'search.max'            => 'El texto de búsqueda no puede tener más de 255 caracteres.'
        ];
    }
}
