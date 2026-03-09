<?php

namespace App\Enums;

enum CombustivelEnum: string
{
    case GASOLINA = 'gasolina';
    case ALCOOL = 'alcool';
    case FLEX = 'flex';
    case DIESEL = 'diesel';
    case HIBRIDO = 'hibrido';
    case ELETRICO = 'eletrico';
}
