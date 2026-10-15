<?php

namespace App\Enums;

enum BeanType: string
{
    case Arabica = 'arabica';
    case Robusta = 'robusta';
    case Liberica = 'liberica';
    case Excelsa = 'excelsa';
    case Blend = 'blend';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
