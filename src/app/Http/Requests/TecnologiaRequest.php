<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TecnologiaRequest extends FormRequest
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
        //nombre, descripcion, estado
        return [
            'nombre'        => ['required','string','max:50'],
            'descripcion'   => ['required','string','max:255'],
            'pdf'           => ['file','mimetypes:application/pdf','max:5000'],
            'estado'        => ['required','in:ACTIVO,INACTIVO'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'       => 'El campo nombre es obligatorio.',
            'nombre.string'         => 'El campo nombre debe ser una cadena.',
            'nombre.max'            => 'El campo nombre no puede superar los 50 caracteres.',

            'descripcion.required'  => 'El campo descripcion es obligatorio.',
            'descripcion.string'    => 'El campo descripcion debe ser una cadena.',
            'descripcion.max'       => 'El campo descripcion no puede superar los 255 caracteres.',

            'pdf.file'              => 'El campo PDF debe ser un archivo.',
            'pdf.mimetypes'         => 'El archivo subido de ser un PDF.',
            'pdf.max'               => 'El tamaño del archivo PDF subido no puede superar los 5MB.',

            'estado.required'       => 'El campo estado es obligatorio.',
            'estado.in'             => 'El campo estado debe ser ACTIVO o INACTIVO.',
        ];
    }
}
