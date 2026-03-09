<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $allowedExtensions = implode(',', config('vehicles.images.allowed_extensions'));
        $maxSizeKb = (int) config('vehicles.images.max_size_kb');

        return [
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['required', 'image', 'mimes:' . $allowedExtensions, 'min:1', 'max:' . $maxSizeKb],
        ];
    }

    public function messages(): array
    {
        return [
            'files.required' => 'Envie pelo menos uma imagem.',
            'files.array' => 'O formato das imagens enviadas é inválido.',
            'files.min' => 'Envie pelo menos uma imagem.',

            'files.*.required' => 'Cada item deve conter uma imagem.',
            'files.*.image' => 'O arquivo enviado deve ser uma imagem.',
            'files.*.mimes' => 'Formato de imagem não permitido.',
            'files.*.max' => 'A imagem excede o tamanho máximo permitido.',
            'files.*.min' => 'A imagem é inválida.',
        ];
    }
}
