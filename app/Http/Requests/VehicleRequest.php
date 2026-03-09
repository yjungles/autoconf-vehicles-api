<?php

namespace App\Http\Requests;

use App\Enums\CambioEnum;
use App\Enums\CombustivelEnum;
use App\Rules\MercosulPlateRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehicleId = $this->route('id');
        return [
            'placa' => [
                'required',
                Rule::unique('vehicles', 'placa')->ignore($vehicleId),
                new MercosulPlateRule()
            ],
            'chassi' => [
                'required',
                Rule::unique('vehicles', 'chassi')->ignore($vehicleId),
                'size:17',
                'alpha_num'
            ],
            'marca' => ['required', 'string', 'max:255'],
            'modelo' => ['required', 'string', 'max:255'],
            'versao' => ['nullable', 'string', 'max:255'],
            'valor_venda' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:9999999999999.99'],
            'cor' => ['required', 'string', 'max:255'],
            'km' => ['required', 'integer', 'min:0'],
            'cambio' => ['required', Rule::enum(CambioEnum::class)],
            'combustivel' => ['required', Rule::enum(CombustivelEnum::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('placa')) {
            $this->merge([
                'placa' => strtoupper($this->placa),
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'placa.required' => 'A placa do veículo é obrigatória.',
            'placa.unique' => 'Já existe um veículo cadastrado com esta placa.',

            'chassi.required' => 'O chassi é obrigatório.',
            'chassi.unique' => 'Já existe um veículo cadastrado com este chassi.',
            'chassi.size' => 'O chassi deve possuir exatamente 17 caracteres.',
            'chassi.alpha_num' => 'O chassi deve conter apenas letras e números.',

            'marca.required' => 'A marca do veículo é obrigatória.',
            'marca.max' => 'A marca não pode ter mais que 255 caracteres.',

            'modelo.required' => 'O modelo do veículo é obrigatório.',
            'modelo.max' => 'O modelo não pode ter mais que 255 caracteres.',

            'versao.max' => 'A versão não pode ter mais que 255 caracteres.',

            'valor_venda.required' => 'O valor de venda é obrigatório.',
            'valor_venda.numeric' => 'O valor de venda deve ser numérico.',
            'valor_venda.min' => 'O valor de venda deve ser maior que zero.',
            'valor_venda.max' => 'O valor de venda excede o limite permitido.',

            'cor.required' => 'A cor do veículo é obrigatória.',
            'cor.max' => 'A cor não pode ter mais que 255 caracteres.',

            'km.required' => 'A quilometragem é obrigatória.',
            'km.integer' => 'A quilometragem deve ser um número inteiro.',
            'km.min' => 'A quilometragem não pode ser negativa.',

            'cambio.required' => 'O tipo de câmbio é obrigatório.',
            'combustivel.required' => 'O tipo de combustível é obrigatório.',
        ];
    }
}
