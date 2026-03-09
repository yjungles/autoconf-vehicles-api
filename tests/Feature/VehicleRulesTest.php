<?php

use App\Rules\ChassiRule;
use App\Rules\MercosulPlateRule;
use Illuminate\Support\Facades\Validator;

it('valida a regra de placa Mercosul', function () {
    $validValidator = Validator::make(
        ['placa' => 'ABC1D23'],
        ['placa' => [new MercosulPlateRule()]],
    );

    $invalidValidator = Validator::make(
        ['placa' => 'AB12345'],
        ['placa' => [new MercosulPlateRule()]],
    );

    expect($validValidator->fails())->toBeFalse()
        ->and($invalidValidator->fails())->toBeTrue()
        ->and($invalidValidator->errors()->first('placa'))
        ->toBe('A placa deve seguir o padrão Mercosul (ABC1D23).');
});

it('valida a regra de chassi', function () {
    $validValidator = Validator::make(
        ['chassi' => '9BWZZZ377VT004251'],
        ['chassi' => [new ChassiRule()]],
    );

    $invalidValidator = Validator::make(
        ['chassi' => '9BWZZZ377VT0O4251'],
        ['chassi' => [new ChassiRule()]],
    );

    expect($validValidator->fails())->toBeFalse()
        ->and($invalidValidator->fails())->toBeTrue()
        ->and($invalidValidator->errors()->first('chassi'))
        ->toBe('O chassi deve possuir exatamente 17 caracteres alfanuméricos e não pode conter as letras I, O ou Q.');
});
